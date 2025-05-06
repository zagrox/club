# Settings Management System

## Overview

This project now uses a database-backed settings management system instead of storing configuration in the `.env` file. This approach provides several advantages:

1. No more file locking issues or "env locked" messages
2. No need to create backup files for every setting change
3. Improved performance through caching
4. Better security as settings are not stored in plain text files
5. Ability to audit setting changes through database records

## How It Works

The settings system consists of:

1. A `settings` table in the database with `key`, `value`, and `group` columns
2. A `Setting` model to interact with settings data
3. A service provider that loads settings at application boot time
4. Integration with Laravel's config system

## Using the Settings System

### Retrieving Settings

```php
// Get a single setting with a default value
$value = \App\Models\Setting::get('key_name', 'default_value');

// Get all settings in a group
$groupSettings = \App\Models\Setting::getGroup('group_name');
```

### Storing Settings

```php
// Set a setting value
\App\Models\Setting::set('key_name', 'value', 'group_name');
```

### Accessing Settings via Config

Settings are integrated with Laravel's config system, so you can access them using the standard config helpers:

```php
$value = config('zibal.merchant');
```

## Migrating from .env to Settings

If you need to migrate existing `.env` settings to the database, run:

```bash
php artisan settings:migrate-env
```

## Configuration Groups

The system supports organizing settings into groups:

- `zibal` - Payment gateway settings
- More groups can be added as needed

## Best Practices

1. Always use the admin interface for changing settings
2. Group related settings together
3. Use descriptive key names (e.g., `zibal_merchant` instead of just `merchant`)
4. For sensitive data, consider using encryption
5. Clear cache after bulk setting changes with `\App\Models\Setting::clearGroupCache('group_name')` 