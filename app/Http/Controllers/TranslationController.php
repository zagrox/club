<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

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
} 