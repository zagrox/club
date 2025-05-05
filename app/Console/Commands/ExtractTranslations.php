<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ExtractTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:extract {--model= : Generate translations for a specific model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract translation strings from blade files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Extracting translations from blade files...');
        
        $viewPaths = [
            resource_path('views'),
        ];
        
        $translationStrings = [];
        
        foreach ($viewPaths as $path) {
            $this->scanDirectory($path, $translationStrings);
        }
        
        // Sort unique translation strings
        $translationStrings = array_unique($translationStrings);
        sort($translationStrings);
        
        $this->info('Found ' . count($translationStrings) . ' unique translation strings.');
        
        // Get existing translations
        $existingTranslations = $this->getExistingTranslations();
        
        // Generate translation arrays for each language
        $this->generateTranslationFiles($translationStrings, $existingTranslations);
        
        $this->info('Translations extracted successfully!');
        
        return 0;
    }
    
    /**
     * Scan directory for blade files
     *
     * @param string $dir
     * @param array $translationStrings
     * @return void
     */
    private function scanDirectory($dir, &$translationStrings)
    {
        $files = File::allFiles($dir);
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                
                // Extract __('...') pattern
                preg_match_all("/__\s*\(\s*[\"']([^\"']+)[\"']\s*[,)]/", $content, $matches);
                if (!empty($matches[1])) {
                    $translationStrings = array_merge($translationStrings, $matches[1]);
                }
                
                // Extract trans('...') pattern
                preg_match_all("/trans\s*\(\s*[\"']([^\"']+)[\"']\s*[,)]/", $content, $matches);
                if (!empty($matches[1])) {
                    $translationStrings = array_merge($translationStrings, $matches[1]);
                }
                
                // Extract @lang('...') pattern
                preg_match_all("/@lang\s*\(\s*[\"']([^\"']+)[\"']\s*[,)]/", $content, $matches);
                if (!empty($matches[1])) {
                    $translationStrings = array_merge($translationStrings, $matches[1]);
                }
                
                // Extract {{ __('...') }} pattern from blade files
                preg_match_all("/\{{\s*__\s*\(\s*[\"']([^\"']+)[\"']\s*[,)]/", $content, $matches);
                if (!empty($matches[1])) {
                    $translationStrings = array_merge($translationStrings, $matches[1]);
                }
            }
        }
    }
    
    /**
     * Get existing translations from language files
     *
     * @return array
     */
    private function getExistingTranslations()
    {
        $existingTranslations = [];
        $langPath = resource_path('lang');
        $languages = File::directories($langPath);
        
        foreach ($languages as $language) {
            $language = basename($language);
            $existingTranslations[$language] = [];
            
            $messagesPath = resource_path("lang/{$language}/messages.php");
            if (File::exists($messagesPath)) {
                $existingTranslations[$language] = require $messagesPath;
            }
        }
        
        return $existingTranslations;
    }
    
    /**
     * Generate translation files
     *
     * @param array $translationStrings
     * @param array $existingTranslations
     * @return void
     */
    private function generateTranslationFiles($translationStrings, $existingTranslations)
    {
        $langPath = resource_path('lang');
        $languages = array_keys($existingTranslations);
        
        foreach ($languages as $language) {
            $translations = $existingTranslations[$language];
            $messagesPath = resource_path("lang/{$language}/messages.php");
            
            // Add new translations
            foreach ($translationStrings as $string) {
                // Skip if the string already exists in translation file
                if (!isset($translations[$string])) {
                    // For English, use the string as translation
                    if ($language === 'en') {
                        $translations[$string] = $string;
                    } else {
                        // For other languages, mark as untranslated
                        $translations[$string] = "[UNTRANSLATED] " . $string;
                        $this->info("Added untranslated string for {$language}: {$string}");
                    }
                }
            }
            
            // Write translation file
            $content = "<?php\n\nreturn [\n";
            
            foreach ($translations as $key => $value) {
                $key = str_replace("'", "\'", $key);
                $value = str_replace("'", "\'", $value);
                $content .= "    '{$key}' => '{$value}',\n";
            }
            
            $content .= "];\n";
            
            File::put($messagesPath, $content);
            $this->info("Updated translation file: {$messagesPath}");
        }
    }
} 