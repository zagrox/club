<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;

class PaymentOptionsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the payment options page.
     */
    public function index()
    {
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized. You need to be an admin to access this page.');
        }
        
        // Get Zibal configuration
        $zibalConfig = [
            'merchant' => config('zibal.merchant'),
            'sandbox' => config('zibal.sandbox'),
            'mock' => config('zibal.mock'),
            'callback_url' => config('zibal.callback_url'),
            'description_prefix' => config('zibal.description_prefix'),
            'log_enabled' => config('zibal.log_enabled'),
            'log_channel' => config('zibal.log_channel'),
        ];
        
        return view('payment-options.index', compact('zibalConfig'));
    }

    /**
     * Show the form for creating a new payment option.
     */
    public function create()
    {
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized. You need to be an admin to access this page.');
        }
        
        return view('payment-options.create');
    }

    /**
     * Store a newly created payment option.
     */
    public function store(Request $request)
    {
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized. You need to be an admin to access this page.');
        }
        
        // For now, redirect to index with success message
        return redirect()->route('payment-options.index')
            ->with('success', 'Payment option created successfully.');
    }

    /**
     * Show the form for editing a payment option.
     */
    public function edit($id)
    {
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized. You need to be an admin to access this page.');
        }
        
        return view('payment-options.edit', ['id' => $id]);
    }

    /**
     * Update the specified payment option.
     */
    public function update(Request $request, $id)
    {
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized. You need to be an admin to access this page.');
        }
        
        // For now, redirect to index with success message
        return redirect()->route('payment-options.index')
            ->with('success', 'Payment option updated successfully.');
    }

    /**
     * Remove the specified payment option.
     */
    public function destroy($id)
    {
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized. You need to be an admin to access this page.');
        }
        
        // For now, redirect to index with success message
        return redirect()->route('payment-options.index')
            ->with('success', 'Payment option deleted successfully.');
    }

    /**
     * Update Zibal settings.
     */
    public function updateZibal(Request $request)
    {
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You need to be an admin to access this page.'
                ], 403);
            }
            abort(403, 'Unauthorized. You need to be an admin to access this page.');
        }
        
        $validated = $request->validate([
            'merchant' => 'required|string',
            'sandbox' => 'nullable',
            'mock' => 'nullable',
            'callback_url' => 'required|string',
            'description_prefix' => 'nullable|string',
            'log_enabled' => 'nullable',
            'log_channel' => 'required|string',
        ]);
        
        try {
            // Properly handle boolean values from checkboxes
            $validated['sandbox'] = $request->has('sandbox') && $request->sandbox == '1';
            $validated['mock'] = $request->has('mock') && $request->mock == '1';
            $validated['log_enabled'] = $request->has('log_enabled') && $request->log_enabled == '1';
            
            // Prepare data for env file
            $envData = [
                'ZIBAL_MERCHANT' => $validated['merchant'],
                'ZIBAL_SANDBOX' => $validated['sandbox'] ? 'true' : 'false',
                'ZIBAL_MOCK' => $validated['mock'] ? 'true' : 'false',
                'ZIBAL_CALLBACK_URL' => $validated['callback_url'],
                'ZIBAL_DESCRIPTION_PREFIX' => $validated['description_prefix'] ?? 'Payment for order: ',
                'ZIBAL_LOG_ENABLED' => $validated['log_enabled'] ? 'true' : 'false',
                'ZIBAL_LOG_CHANNEL' => $validated['log_channel'],
            ];
            
            // Update .env file
            $this->updateEnvFile($envData);
            
            // Clear config cache and give time for filesystem operations to complete
            Artisan::call('config:clear');
            
            // Log the successful update
            \Log::info('Zibal configuration updated', [
                'user_id' => Auth::id(),
                'values' => array_map(function($value) {
                    return is_string($value) && strlen($value) > 30 ? 
                        substr($value, 0, 30) . '...' : $value;
                }, $envData)
            ]);
            
            // Return appropriate response based on request type
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تنظیمات درگاه پرداخت با موفقیت ذخیره شد.'
                ]);
            }
            
            // Redirect with success message for regular form submission
            return redirect()->route('payment-options.index')
                ->with('success', 'تنظیمات درگاه پرداخت با موفقیت ذخیره شد.');
                
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Failed to update Zibal configuration', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطا در ذخیره تنظیمات: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('payment-options.index')
                ->with('error', 'خطا در ذخیره تنظیمات: ' . $e->getMessage());
        }
    }
    
    /**
     * Test Zibal connection.
     */
    public function testZibal()
    {
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized. You need to be an admin to access this page.');
        }
        
        try {
            // Get current Zibal settings to restore after test
            $zibalSettings = [
                'ZIBAL_MERCHANT' => config('zibal.merchant'),
                'ZIBAL_SANDBOX' => config('zibal.sandbox') ? 'true' : 'false',
                'ZIBAL_MOCK' => config('zibal.mock') ? 'true' : 'false',
                'ZIBAL_CALLBACK_URL' => config('zibal.callback_url'),
                'ZIBAL_DESCRIPTION_PREFIX' => config('zibal.description_prefix'),
                'ZIBAL_LOG_ENABLED' => config('zibal.log_enabled') ? 'true' : 'false',
                'ZIBAL_LOG_CHANNEL' => config('zibal.log_channel'),
            ];
            
            // Set test values if needed
            $testSettings = [
                'ZIBAL_SANDBOX' => 'true',
                'ZIBAL_LOG_ENABLED' => 'true',
            ];
            
            // Apply test settings using the special zibal method that doesn't create multiple backups
            $this->updateZibalEnvFile($testSettings);
            
            // Run the test command
            $output = Artisan::call('zibal:test', [
                'user_id' => auth()->id(),
                'amount' => 10000
            ]);
            
            // Get the command output
            $outputText = Artisan::output();
            
            // Find the trackId and payment URL in the output
            preg_match('/trackId\s+\|\s+([a-zA-Z0-9]+)/', $outputText, $trackMatches);
            preg_match('/Payment URL: (.+)$/', $outputText, $urlMatches);
            
            $trackId = $trackMatches[1] ?? null;
            $paymentUrl = $urlMatches[1] ?? null;
            
            // Restore original settings
            $this->updateZibalEnvFile($zibalSettings);
            
            if ($trackId && $paymentUrl) {
                return redirect()->route('payment-options.index')
                    ->with('success', "Zibal test successful! Track ID: {$trackId}")
                    ->with('paymentUrl', $paymentUrl);
            }
            
            return redirect()->route('payment-options.index')
                ->with('error', 'Zibal test completed but could not parse output: ' . $outputText);
        } catch (\Exception $e) {
            return redirect()->route('payment-options.index')
                ->with('error', 'Zibal test failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Update environment file with Zibal values without creating multiple backups.
     * This method is specifically for Zibal testing to prevent multiple backup files.
     */
    private function updateZibalEnvFile(array $values)
    {
        try {
            $envFile = app()->environmentFilePath();
            
            if (!file_exists($envFile)) {
                // Create .env file if it doesn't exist
                file_put_contents($envFile, '');
            }
            
            // Check if the file is writable
            if (!is_writable($envFile)) {
                throw new \Exception("Environment file is not writable. Please check file permissions.");
            }
            
            // Create a single reusable backup for zibal testing
            $backupPath = $envFile . '.zibal-test-backup';
            
            // Only create the backup if it doesn't exist already
            if (!file_exists($backupPath)) {
                copy($envFile, $backupPath);
            }
            
            // Get current content with an exclusive lock
            $fp = fopen($envFile, 'r+');
            
            if (flock($fp, LOCK_EX)) { // Acquire an exclusive lock
                $envContent = '';
                while(!feof($fp)) {
                    $envContent .= fread($fp, 8192);
                }
                
                // Update the content - only modify Zibal settings
                foreach ($values as $key => $value) {
                    // Only process ZIBAL_ prefixed keys to preserve other settings
                    if (strpos($key, 'ZIBAL_') !== 0) {
                        continue;
                    }
                    
                    // Format the value appropriately
                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    } else if (is_null($value)) {
                        $value = '';
                    } else {
                        // Escape any quotes
                        $value = is_string($value) ? str_replace('"', '\"', $value) : $value;
                    }
                    
                    // Check if the key exists
                    if (preg_match("/^{$key}=.*/m", $envContent)) {
                        // Replace existing value - make sure to handle quotes correctly
                        $envContent = preg_replace(
                            "/^{$key}=.*/m",
                            "{$key}=\"{$value}\"",
                            $envContent
                        );
                    } else {
                        // Add new value
                        $envContent .= PHP_EOL . "{$key}=\"{$value}\"";
                    }
                }
                
                // Make sure the APP_URL is present
                if (!preg_match("/^APP_URL=.*/m", $envContent)) {
                    $envContent .= PHP_EOL . "APP_URL=\"http://localhost:8000\"";
                }
                
                // Truncate and write the file
                ftruncate($fp, 0); // Clear the file
                rewind($fp); // Set the file pointer to the beginning
                fwrite($fp, $envContent); // Write the new content
                fflush($fp); // Flush output before releasing the lock
                flock($fp, LOCK_UN); // Release the lock
            } else {
                throw new \Exception("Could not acquire a lock on the .env file. Another process may be using it.");
            }
            
            fclose($fp);
            
            // Clear config cache to ensure changes take effect
            \Artisan::call('config:clear');
            
            return true;
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Failed to update .env file: ' . $e->getMessage(), [
                'exception' => $e,
                'file_path' => $envFile ?? app()->environmentFilePath()
            ]);
            
            // You can handle the exception here or rethrow it
            throw $e;
        }
    }
    
    /**
     * Update environment file with new values.
     */
    private function updateEnvFile(array $values)
    {
        try {
            $envFile = app()->environmentFilePath();
            
            if (!file_exists($envFile)) {
                // Create .env file if it doesn't exist
                file_put_contents($envFile, '');
            }
            
            // Check if the file is writable
            if (!is_writable($envFile)) {
                throw new \Exception("Environment file is not writable. Please check file permissions.");
            }
            
            // Create a backup of the original .env file
            $backupPath = $envFile . '.backup-' . date('Y-m-d-H-i-s');
            copy($envFile, $backupPath);
            
            // Get current content with an exclusive lock
            $fp = fopen($envFile, 'r+');
            
            if (flock($fp, LOCK_EX)) { // Acquire an exclusive lock
                $envContent = '';
                while(!feof($fp)) {
                    $envContent .= fread($fp, 8192);
                }
                
                // Store critical settings that should be preserved if not in $values
                $criticalSettings = [];
                $criticalKeys = ['APP_URL', 'APP_KEY', 'DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
                
                foreach ($criticalKeys as $criticalKey) {
                    // Only if not being updated and exists in current env
                    if (!isset($values[$criticalKey]) && preg_match("/^{$criticalKey}=(.*)$/m", $envContent, $matches)) {
                        $criticalSettings[$criticalKey] = trim($matches[1], '"\'');
                    }
                }
                
                // Update the content
                foreach ($values as $key => $value) {
                    // Format the value appropriately
                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    } else if (is_null($value)) {
                        $value = '';
                    } else {
                        // Escape any quotes
                        $value = is_string($value) ? str_replace('"', '\"', $value) : $value;
                    }
                    
                    // Check if the key exists
                    if (preg_match("/^{$key}=.*/m", $envContent)) {
                        // Replace existing value - make sure to handle quotes correctly
                        $envContent = preg_replace(
                            "/^{$key}=.*/m",
                            "{$key}=\"{$value}\"",
                            $envContent
                        );
                    } else {
                        // Add new value
                        $envContent .= PHP_EOL . "{$key}=\"{$value}\"";
                    }
                }
                
                // Make sure critical settings are present
                foreach ($criticalSettings as $key => $value) {
                    if (!preg_match("/^{$key}=.*/m", $envContent)) {
                        $envContent .= PHP_EOL . "{$key}=\"{$value}\"";
                    }
                }
                
                // Make sure APP_URL is present with default value if not set
                if (!preg_match("/^APP_URL=.*/m", $envContent)) {
                    $envContent .= PHP_EOL . "APP_URL=\"http://localhost:8000\"";
                }
                
                // Truncate and write the file
                ftruncate($fp, 0); // Clear the file
                rewind($fp); // Set the file pointer to the beginning
                fwrite($fp, $envContent); // Write the new content
                fflush($fp); // Flush output before releasing the lock
                flock($fp, LOCK_UN); // Release the lock
            } else {
                throw new \Exception("Could not acquire a lock on the .env file. Another process may be using it.");
            }
            
            fclose($fp);
            
            // Clear config cache to ensure changes take effect
            \Artisan::call('config:clear');
            
            return true;
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Failed to update .env file: ' . $e->getMessage(), [
                'exception' => $e,
                'file_path' => $envFile ?? app()->environmentFilePath()
            ]);
            
            // You can handle the exception here or rethrow it
            throw $e;
        }
    }
} 