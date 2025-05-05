# Translation Guide

This document explains how to work with translations in the application.

## Overview

The application uses Laravel's built-in localization features to support multiple languages. Currently, the supported languages are:

- English (en)
- Spanish (es)
- Persian (fa)

## How Translation Works

1. In blade templates and PHP code, use the `__()` helper function to mark text for translation:

```php
{{ __('Hello World') }}
{{ __('messages.welcome') }}
```

2. For longer text or specific named keys, you can use the format `'filename.key'`:

```php
{{ __('messages.welcome') }}
```

3. The translation system will look for these keys in the corresponding language files in `resources/lang/{language}/messages.php`.

## Extracting Translations

To extract all translatable strings from the application, run:

```bash
php artisan translations:extract
```

This will:
- Scan all blade files for translatable strings
- Update the language files with any new strings
- Mark untranslated strings in non-English language files with `[UNTRANSLATED]` prefix

## Adding Translations Manually

1. Open the appropriate language file in `resources/lang/{language}/messages.php`
2. Add your translation key-value pairs

Example:
```php
// resources/lang/fa/messages.php
return [
    'welcome' => 'خوش آمدید',
    'login' => 'ورود',
    // ...
];
```

## Translating Page Content

1. When creating new pages or components, always use the `__()` helper for all user-visible text:

```php
<h1>{{ __('Profile Details') }}</h1>
<button>{{ __('Save changes') }}</button>
```

2. For longer text, use the helper the same way:

```php
<p>{{ __('This is a longer text that needs to be translated.') }}</p>
```

3. For text with variables, use the parameter replacement feature:

```php
{{ __('Hello, :name!', ['name' => $user->name]) }}
```

## Switching Languages

The application uses the [Laravel Localization](https://github.com/mcamara/laravel-localization) package to handle language switching.

URLs are prefixed with the language code, for example:
- `/en/home` for English
- `/es/home` for Spanish
- `/fa/home` for Persian

## Missing Translations

If you notice text that doesn't get translated when switching languages:

1. Make sure the text is wrapped in the `__()` helper function
2. Run the extraction command to update language files
3. Add the missing translations to the appropriate language files

## Best Practices

1. Always use the `__()` helper for user-visible text
2. Avoid hardcoding text directly in templates
3. Keep translations concise and focused on the meaning, not literal word-for-word translation
4. Regularly run the extract command to identify missing translations
5. Test your page in all supported languages to make sure everything displays correctly 