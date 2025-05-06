<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'native',
        'is_active',
        'is_rtl',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_rtl' => 'boolean',
    ];

    /**
     * Get all languages
     */
    public static function getAllLanguages()
    {
        return self::orderBy('sort_order')->get();
    }

    /**
     * Get active languages
     */
    public static function getActiveLanguages()
    {
        return self::where('is_active', true)->orderBy('sort_order')->get();
    }

    /**
     * Create or update the language config
     */
    public static function updateLocalizationConfig()
    {
        $languages = self::where('is_active', true)->orderBy('sort_order')->get();
        $supportedLocales = [];
        
        foreach ($languages as $language) {
            $supportedLocales[$language->code] = [
                'name' => $language->name,
                'script' => $language->is_rtl ? 'Arab' : 'Latn',
                'native' => $language->native,
                'regional' => $language->code . '_' . strtoupper($language->code),
            ];
        }
        
        // Update config at runtime
        Config::set('laravellocalization.supportedLocales', $supportedLocales);
        
        return $supportedLocales;
    }

    /**
     * Check if language directory exists, create if it doesn't
     */
    public function ensureLanguageDirectoryExists()
    {
        $path = resource_path("lang/{$this->code}");
        
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
            
            // Copy base files from English
            $enPath = resource_path("lang/en");
            if (File::exists($enPath)) {
                $files = File::files($enPath);
                foreach ($files as $file) {
                    $content = file_get_contents($file->getPathname());
                    $fileName = $file->getFilename();
                    
                    // For non-English languages, mark translations as untranslated
                    if ($this->code !== 'en') {
                        $translationArray = include $file->getPathname();
                        $newTranslationArray = [];
                        
                        foreach ($translationArray as $key => $value) {
                            $newTranslationArray[$key] = "[UNTRANSLATED] " . $value;
                        }
                        
                        $content = "<?php\n\nreturn [\n";
                        foreach ($newTranslationArray as $key => $value) {
                            $key = str_replace("'", "\'", $key);
                            $value = str_replace("'", "\'", $value);
                            $content .= "    '{$key}' => '{$value}',\n";
                        }
                        $content .= "];\n";
                    }
                    
                    File::put("{$path}/{$fileName}", $content);
                }
            }
            
            return true;
        }
        
        return false;
    }
} 