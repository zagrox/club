<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use App\Models\Language;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;

class TranslationController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user() || !Auth::user()->hasRole('admin')) {
                abort(403, 'Unauthorized. You need to be an admin to access this page.');
            }
            return $next($request);
        });
    }
    
    public function index()
    {
        $languages = array_keys(LaravelLocalization::getSupportedLocales());
        $translationFiles = $this->getTranslationFiles();
        
        return view('admin.translations.index', compact('languages', 'translationFiles'));
    }
    
    public function edit($file)
    {
        $languages = array_keys(LaravelLocalization::getSupportedLocales());
        $translations = $this->getTranslations($file, $languages);
        
        return view('admin.translations.edit', compact('languages', 'translations', 'file'));
    }
    
    public function update(Request $request, $file)
    {
        $translations = $request->translations;
        $languages = array_keys(LaravelLocalization::getSupportedLocales());
        
        foreach ($languages as $language) {
            if (!isset($translations[$language])) continue;
            
            $filePath = resource_path("lang/{$language}/{$file}.php");
            $content = "<?php\n\nreturn [\n";
            
            foreach ($translations[$language] as $key => $value) {
                // Properly escape single quotes
                $value = str_replace("'", "\'", $value);
                $content .= "    '{$key}' => '{$value}',\n";
            }
            
            $content .= "];\n";
            
            File::put($filePath, $content);
        }
        
        // Clear Laravel's translation cache
        $this->clearTranslationCache();
        
        return redirect(url("admin/translations/{$file}"))
            ->with('success', 'Translations updated successfully!');
    }
    
    protected function getTranslationFiles()
    {
        $languages = array_keys(LaravelLocalization::getSupportedLocales());
        $files = [];
        
        // Get all translation files across all languages
        foreach ($languages as $language) {
            $path = resource_path("lang/{$language}");
            
            if (File::exists($path)) {
                $langFiles = File::files($path);
                
                foreach ($langFiles as $file) {
                    $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    if (!in_array($fileName, $files)) {
                        $files[] = $fileName;
                    }
                }
            }
        }
        
        return $files;
    }
    
    protected function getTranslations($file, $languages)
    {
        $translations = [];
        $allKeys = [];
        
        // Collect all translations and keys
        foreach ($languages as $language) {
            $filePath = resource_path("lang/{$language}/{$file}.php");
            
            if (File::exists($filePath)) {
                $content = include $filePath;
                $translations[$language] = $content;
                $allKeys = array_merge($allKeys, array_keys($content));
            } else {
                $translations[$language] = [];
            }
        }
        
        // Ensure all languages have all keys
        $allKeys = array_unique($allKeys);
        sort($allKeys); // Sort keys alphabetically
        
        foreach ($languages as $language) {
            foreach ($allKeys as $key) {
                if (!isset($translations[$language][$key])) {
                    $translations[$language][$key] = '';
                }
            }
            
            // Sort by key
            ksort($translations[$language]);
        }
        
        return [
            'keys' => $allKeys,
            'data' => $translations
        ];
    }
    
    /**
     * Extract translations from the application
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function extract()
    {
        try {
            $output = '';
            Artisan::call('translations:extract', [], $output);
            
            // Parse the output to get statistics
            $lines = explode("\n", $output);
            $stats = [];
            $totalTranslations = 0;
            
            foreach ($lines as $line) {
                if (preg_match('/Generated (\w+)\/(\w+)\.php with (\d+) translations/', $line, $matches)) {
                    $lang = $matches[1];
                    $file = $matches[2];
                    $count = (int)$matches[3];
                    
                    if (!isset($stats[$lang])) {
                        $stats[$lang] = [];
                    }
                    
                    $stats[$lang][$file] = $count;
                    $totalTranslations += $count;
                }
            }
            
            // Find total unique strings by looking at a single language (assuming all have the same keys)
            $uniqueStrings = array_sum(reset($stats) ?: []);
            
            // Clear Laravel's translation cache
            $this->clearTranslationCache();
            
            session()->flash('extraction_stats', [
                'total' => $totalTranslations,
                'unique' => $uniqueStrings,
                'languages' => count($stats),
                'details' => $stats
            ]);
            
            return redirect()->route('translations.index')
                ->with('success', "Translations extracted successfully! Found {$uniqueStrings} unique translatable strings across " . count($stats) . " languages.");
                
        } catch (\Exception $e) {
            logger()->error('Translation extraction failed', ['error' => $e->getMessage()]);
            return redirect()->route('translations.index')
                ->with('error', 'Translation extraction failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Show the language management page
     */
    public function languages()
    {
        $languages = Language::getAllLanguages();
        
        // Get translation statistics
        $stats = [];
        foreach ($languages as $language) {
            $stats[$language->code] = $this->getLanguageStats($language->code);
        }
        
        return view('admin.translations.languages', compact('languages', 'stats'));
    }
    
    /**
     * Get translation statistics for a language
     */
    protected function getLanguageStats($langCode)
    {
        $path = resource_path("lang/{$langCode}");
        $stats = [
            'files' => 0,
            'translations' => 0,
            'translated' => 0,
            'untranslated' => 0,
            'percent_translated' => 0,
        ];
        
        if (File::exists($path)) {
            $files = File::files($path);
            $stats['files'] = count($files);
            
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $translations = include $file->getPathname();
                    $stats['translations'] += count($translations);
                    
                    // Count untranslated strings
                    foreach ($translations as $key => $value) {
                        if (strpos($value, '[UNTRANSLATED]') === 0) {
                            $stats['untranslated']++;
                        } else {
                            $stats['translated']++;
                        }
                    }
                }
            }
            
            // Calculate percentage
            if ($stats['translations'] > 0) {
                $stats['percent_translated'] = round(($stats['translated'] / $stats['translations']) * 100);
            }
        }
        
        return $stats;
    }
    
    /**
     * Show the create language form
     */
    public function createLanguage()
    {
        return view('admin.translations.create_language');
    }
    
    /**
     * Store a new language
     */
    public function storeLanguage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'max:10', 'regex:/^[a-z]{2}(-[a-z]{2})?$/', 'unique:languages,code'],
            'name' => ['required', 'string', 'max:255'],
            'native' => ['required', 'string', 'max:255'],
            'is_rtl' => ['boolean'],
            'is_active' => ['boolean'],
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('translations.create_language')
                ->withErrors($validator)
                ->withInput();
        }
        
        $language = Language::create([
            'code' => $request->code,
            'name' => $request->name,
            'native' => $request->native,
            'is_rtl' => $request->has('is_rtl'),
            'is_active' => $request->has('is_active'),
            'sort_order' => Language::count() // Add at the end
        ]);
        
        // Create language directory and copy base files
        $language->ensureLanguageDirectoryExists();
        
        // Update config
        Language::updateLocalizationConfig();
        
        // Clear Laravel's translation cache
        $this->clearTranslationCache();
        
        return redirect()->route('translations.languages')
            ->with('success', "Language '{$language->name}' added successfully!");
    }
    
    /**
     * Show the edit language form
     */
    public function editLanguage($id)
    {
        $language = Language::findOrFail($id);
        return view('admin.translations.edit_language', compact('language'));
    }
    
    /**
     * Update a language
     */
    public function updateLanguage(Request $request, $id)
    {
        $language = Language::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'max:10', 'regex:/^[a-z]{2}(-[a-z]{2})?$/', Rule::unique('languages')->ignore($language->id)],
            'name' => ['required', 'string', 'max:255'],
            'native' => ['required', 'string', 'max:255'],
            'is_rtl' => ['boolean'],
            'is_active' => ['boolean'],
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('translations.edit_language', $language->id)
                ->withErrors($validator)
                ->withInput();
        }
        
        // Check if language code changed
        $codeChanged = $language->code !== $request->code;
        $oldCode = $language->code;
        
        $language->update([
            'code' => $request->code,
            'name' => $request->name,
            'native' => $request->native,
            'is_rtl' => $request->has('is_rtl'),
            'is_active' => $request->has('is_active'),
        ]);
        
        // If language code changed, update translation directories
        if ($codeChanged) {
            $oldPath = resource_path("lang/{$oldCode}");
            $newPath = resource_path("lang/{$language->code}");
            
            if (File::exists($oldPath)) {
                // Create new directory if it doesn't exist
                if (!File::exists($newPath)) {
                    File::makeDirectory($newPath, 0755, true);
                }
                
                // Copy files to new directory
                foreach (File::files($oldPath) as $file) {
                    File::copy(
                        $file->getPathname(),
                        $newPath . '/' . $file->getFilename()
                    );
                }
                
                // Delete old directory
                File::deleteDirectory($oldPath);
            }
        }
        
        // Ensure language directory exists
        $language->ensureLanguageDirectoryExists();
        
        // Update config
        Language::updateLocalizationConfig();
        
        // Clear Laravel's translation cache
        $this->clearTranslationCache();
        
        return redirect()->route('translations.languages')
            ->with('success', "Language '{$language->name}' updated successfully!");
    }
    
    /**
     * Toggle language active status
     */
    public function toggleLanguage($id)
    {
        $language = Language::findOrFail($id);
        $language->update([
            'is_active' => !$language->is_active
        ]);
        
        // Update config
        Language::updateLocalizationConfig();
        
        // Clear Laravel's translation cache
        $this->clearTranslationCache();
        
        $status = $language->is_active ? 'activated' : 'deactivated';
        return redirect()->route('translations.languages')
            ->with('success', "Language '{$language->name}' {$status} successfully!");
    }
    
    /**
     * Delete a language
     */
    public function deleteLanguage($id)
    {
        $language = Language::findOrFail($id);
        
        // Prevent deleting English which is the default language
        if ($language->code === 'en') {
            return redirect()->route('translations.languages')
                ->with('error', "Cannot delete the default English language!");
        }
        
        $name = $language->name;
        $code = $language->code;
        
        // Delete language directory
        $path = resource_path("lang/{$code}");
        if (File::exists($path)) {
            File::deleteDirectory($path);
        }
        
        // Delete language from database
        $language->delete();
        
        // Update config
        Language::updateLocalizationConfig();
        
        // Clear Laravel's translation cache
        $this->clearTranslationCache();
        
        return redirect()->route('translations.languages')
            ->with('success', "Language '{$name}' deleted successfully!");
    }
    
    /**
     * Reorder languages
     */
    public function reorderLanguages(Request $request)
    {
        $ids = $request->ids;
        
        foreach ($ids as $index => $id) {
            Language::where('id', $id)->update(['sort_order' => $index]);
        }
        
        // Update config
        Language::updateLocalizationConfig();
        
        // Clear Laravel's translation cache
        $this->clearTranslationCache();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Create a new translation file
     */
    public function createFile()
    {
        $languages = Language::where('is_active', true)->get();
        return view('admin.translations.create_file', compact('languages'));
    }
    
    /**
     * Store a new translation file
     */
    public function storeFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'filename' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9_]+$/'],
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('translations.create_file')
                ->withErrors($validator)
                ->withInput();
        }
        
        $filename = $request->filename;
        $languages = Language::where('is_active', true)->get();
        
        // Check if file already exists
        foreach ($languages as $language) {
            $filePath = resource_path("lang/{$language->code}/{$filename}.php");
            if (File::exists($filePath)) {
                return redirect()->route('translations.create_file')
                    ->with('error', "File '{$filename}.php' already exists for language '{$language->name}'!")
                    ->withInput();
            }
        }
        
        // Create file for each language
        foreach ($languages as $language) {
            $filePath = resource_path("lang/{$language->code}/{$filename}.php");
            
            // Ensure directory exists
            $dirPath = resource_path("lang/{$language->code}");
            if (!File::exists($dirPath)) {
                File::makeDirectory($dirPath, 0755, true);
            }
            
            // Create empty translation file
            $content = "<?php\n\nreturn [\n    // Add translations here\n];\n";
            File::put($filePath, $content);
        }
        
        // Clear Laravel's translation cache
        $this->clearTranslationCache();
        
        return redirect()->route('translations.edit', $filename)
            ->with('success', "Translation file '{$filename}.php' created successfully!");
    }
    
    /**
     * Delete a translation file
     */
    public function deleteFile($file)
    {
        $languages = Language::where('is_active', true)->get();
        
        foreach ($languages as $language) {
            $filePath = resource_path("lang/{$language->code}/{$file}.php");
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }
        
        // Clear Laravel's translation cache
        $this->clearTranslationCache();
        
        return redirect()->route('translations.index')
            ->with('success', "Translation file '{$file}.php' deleted successfully!");
    }
    
    /**
     * Clear Laravel's translation cache
     */
    protected function clearTranslationCache()
    {
        Artisan::call('cache:clear');
        Cache::flush();
    }
} 