<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:sync {--language= : Specific language code to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync translations from /lang to /resources/lang, adding missing files and translations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting translation sync...');
        
        $languageOption = $this->option('language');
        
        if ($languageOption) {
            $this->syncLanguage($languageOption);
        } else {
            // Get all languages from /lang
            $languageDirs = File::directories(base_path('lang'));
            
            foreach ($languageDirs as $languageDir) {
                $langCode = basename($languageDir);
                
                // Skip vendor directory
                if ($langCode === 'vendor') {
                    continue;
                }
                
                $this->syncLanguage($langCode);
            }
            
            // Sync vendor translations
            $this->syncVendorTranslations();
        }
        
        $this->info('Translation sync completed!');
        
        return Command::SUCCESS;
    }
    
    /**
     * Sync translations for a specific language
     */
    protected function syncLanguage($langCode)
    {
        $this->info("Syncing translations for language: {$langCode}");
        
        $sourcePath = base_path("lang/{$langCode}");
        $targetPath = resource_path("lang/{$langCode}");
        
        if (!File::exists($sourcePath)) {
            $this->error("Source language directory not found: {$sourcePath}");
            return;
        }
        
        // Create target directory if it doesn't exist
        if (!File::exists($targetPath)) {
            File::makeDirectory($targetPath, 0755, true);
            $this->info("Created directory: {$targetPath}");
        }
        
        // Get all translation files
        $files = File::files($sourcePath);
        $syncedFiles = 0;
        $syncedTranslations = 0;
        
        foreach ($files as $file) {
            $fileName = $file->getFilename();
            $sourceFilePath = $file->getPathname();
            $targetFilePath = "{$targetPath}/{$fileName}";
            
            // Load source translations
            $sourceTranslations = include $sourceFilePath;
            
            // Check if target file exists
            if (File::exists($targetFilePath)) {
                // Merge translations
                $targetTranslations = include $targetFilePath;
                $missingKeys = 0;
                
                // Find missing keys
                foreach ($sourceTranslations as $key => $value) {
                    if (!isset($targetTranslations[$key])) {
                        $targetTranslations[$key] = $value;
                        $missingKeys++;
                        $syncedTranslations++;
                    }
                }
                
                if ($missingKeys > 0) {
                    // Write updated translations
                    $content = "<?php\n\nreturn [\n";
                    
                    foreach ($targetTranslations as $key => $value) {
                        // Properly escape single quotes
                        $escapedKey = str_replace("'", "\'", $key);
                        
                        if (is_array($value)) {
                            $content .= "    '{$escapedKey}' => [\n";
                            $this->writeArrayToContent($content, $value, 8);
                            $content .= "    ],\n";
                        } else {
                            $escapedValue = str_replace("'", "\'", $value);
                            $content .= "    '{$escapedKey}' => '{$escapedValue}',\n";
                        }
                    }
                    
                    $content .= "];\n";
                    
                    File::put($targetFilePath, $content);
                    $this->info("Updated {$fileName} with {$missingKeys} new translations");
                    $syncedFiles++;
                }
            } else {
                // Copy file with modifications if needed
                $content = "<?php\n\nreturn [\n";
                
                foreach ($sourceTranslations as $key => $value) {
                    // Properly escape single quotes
                    $escapedKey = str_replace("'", "\'", $key);
                    
                    if (is_array($value)) {
                        $content .= "    '{$escapedKey}' => [\n";
                        $this->writeArrayToContent($content, $value, 8);
                        $content .= "    ],\n";
                    } else {
                        $escapedValue = str_replace("'", "\'", $value);
                        $content .= "    '{$escapedKey}' => '{$escapedValue}',\n";
                    }
                }
                
                $content .= "];\n";
                
                File::put($targetFilePath, $content);
                $this->info("Created new file: {$fileName} with " . count($sourceTranslations) . " translations");
                $syncedFiles++;
                $syncedTranslations += count($sourceTranslations);
            }
        }
        
        $this->info("Synced {$syncedTranslations} translations in {$syncedFiles} files for {$langCode}");
    }
    
    /**
     * Helper method to write nested arrays to the content string
     */
    protected function writeArrayToContent(&$content, $array, $indentation)
    {
        $spaces = str_repeat(' ', $indentation);
        
        foreach ($array as $key => $value) {
            $escapedKey = str_replace("'", "\'", $key);
            
            if (is_array($value)) {
                $content .= "{$spaces}'{$escapedKey}' => [\n";
                $this->writeArrayToContent($content, $value, $indentation + 4);
                $content .= "{$spaces}],\n";
            } else {
                $escapedValue = str_replace("'", "\'", $value);
                $content .= "{$spaces}'{$escapedKey}' => '{$escapedValue}',\n";
            }
        }
    }
    
    /**
     * Sync vendor translations
     */
    protected function syncVendorTranslations()
    {
        $vendorPath = base_path('lang/vendor');
        
        if (!File::exists($vendorPath)) {
            return;
        }
        
        $this->info('Syncing vendor translations...');
        
        // Get all vendor packages
        $vendorDirs = File::directories($vendorPath);
        
        foreach ($vendorDirs as $vendorDir) {
            $packageName = basename($vendorDir);
            $this->info("Syncing vendor package: {$packageName}");
            
            // Get all languages for this package
            $languageDirs = File::directories($vendorDir);
            
            foreach ($languageDirs as $languageDir) {
                $langCode = basename($languageDir);
                
                $sourcePath = "{$vendorPath}/{$packageName}/{$langCode}";
                $targetPath = resource_path("lang/vendor/{$packageName}/{$langCode}");
                
                // Create target directory if it doesn't exist
                if (!File::exists($targetPath)) {
                    File::makeDirectory($targetPath, 0755, true);
                }
                
                // Get all translation files
                $files = File::files($sourcePath);
                
                foreach ($files as $file) {
                    $fileName = $file->getFilename();
                    $sourceFilePath = $file->getPathname();
                    $targetFilePath = "{$targetPath}/{$fileName}";
                    
                    // Simply copy vendor files if they don't exist
                    if (!File::exists($targetFilePath)) {
                        File::copy($sourceFilePath, $targetFilePath);
                        $this->info("Created vendor file: {$packageName}/{$langCode}/{$fileName}");
                    }
                }
            }
        }
    }
} 