# Environment File Management

## Overview

This project uses Laravel's `.env` file to store environment-specific configuration. When updating certain settings through the admin interface (e.g., payment gateway configurations), the system creates backups of the `.env` file to ensure data safety.

## Backup System

- Each time an environment variable is changed via the admin interface, a timestamped backup is created (format: `.env.backup-YYYY-MM-DD-HH-MM-SS`)
- The system automatically keeps only the 3 most recent backups
- A daily scheduled task (`env:cleanup`) runs to ensure old backups are properly cleaned up

## "ENV Locked" Message

If you receive an "ENV Locked" message, it means that another process is currently trying to update the `.env` file. The system uses file locking to prevent concurrent modifications to environment variables.

### What to do if you get "ENV Locked" message

1. Wait a few seconds and try again - the lock is temporary and should be released after the other process completes
2. If the issue persists, check if any PHP processes have crashed and are still holding the lock
3. As a last resort, you can restart the web server

## Managing Environment Files

### Manual Cleanup

If you need to manually clean up backup files, you can run:

```bash
php artisan env:cleanup
```

To keep more or fewer backups, use the `--keep` option:

```bash
php artisan env:cleanup --keep=5  # Keep 5 most recent backups
```

### Multiple Environment Files

The project should only use one active `.env` file. Other files should be:

- `.env` - The active environment file
- `.env.example` - Example configuration template
- `.env.backup-*` - Automated backups (kept for safety)

Any other `.env.*` files should be deleted to avoid confusion.

## Best Practices

1. Never edit the `.env` file directly in production
2. Use the admin interface for changes whenever possible
3. Make sure your `.env` file is properly secured and not exposed to the web
4. Keep your Laravel configuration cache updated (`php artisan config:clear` after `.env` changes) 