# Notification Queue System

This document provides instructions for setting up and running the notification queue system for the application.

## Overview

The notification system uses Laravel's queue system to process notifications asynchronously. This allows for better performance, retrying capabilities, and tracking of notification delivery status.

## Configuration

The notification queue uses the following configuration:

1. Queue Connection: `database` (configurable in `.env`)
2. Queue Name: `notifications` (configurable in `.env`)
3. Retry Attempts: 3 (configurable in `.env`)
4. Retry Delay: 60 seconds (configurable in `.env`)

### Environment Configuration

Add the following to your `.env` file:

```
QUEUE_CONNECTION=database
NOTIFICATION_QUEUE_NAME=notifications
NOTIFICATION_QUEUE_TRIES=3
NOTIFICATION_QUEUE_BACKOFF=60
```

## Database Setup

Ensure you've run the migrations to create the necessary database tables:

```bash
php artisan migrate
```

This will create:
- `jobs` table - For storing queued jobs
- `failed_jobs` table - For storing failed jobs

## Processing Notifications

### Command-line

To process notifications that are pending in the system:

```bash
php artisan notifications:process
```

By default, this will process up to 10 pending notifications. You can specify a different number:

```bash
php artisan notifications:process --count=20
```

### Queue Worker

To start a dedicated worker for processing notification jobs:

```bash
php artisan notifications:worker
```

For production environments, use the daemon mode with a process manager like Supervisor:

```bash
php artisan notifications:worker --daemon
```

## Worker Options

- `--queue=notifications` - The queue to listen on
- `--daemon` - Run the worker in daemon mode (continuously)
- `--sleep=3` - Number of seconds to sleep when no job is available
- `--tries=3` - Number of times to attempt a job before logging it failed

## Supervisor Configuration (Production)

For production environments, it's recommended to use Supervisor to manage the queue workers.

Example Supervisor configuration:

```ini
[program:club-notification-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/project/artisan notifications:worker --daemon
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/project/storage/logs/worker.log
stopwaitsecs=3600
```

## Monitoring

You can monitor the notification queue using Laravel Horizon if installed, or directly checking the database tables:

- Check pending notifications: `SELECT * FROM notifications WHERE status = 'pending';`
- Check processing notifications: `SELECT * FROM notifications WHERE status = 'processing';`
- Check failed notifications: `SELECT * FROM notifications WHERE status = 'failed';`
- Check completed notifications: `SELECT * FROM notifications WHERE status = 'sent';`

## Notification Statuses

The notification system supports the following statuses:

- `pending` - The notification is waiting to be processed
- `processing` - The notification is currently being processed
- `sent` - The notification has been successfully delivered
- `failed` - The notification has failed to be delivered after all retry attempts
- `draft` - The notification is saved as a draft
- `scheduled` - The notification is scheduled to be sent at a later time
- `archived` - The notification has been archived
- `canceled` - The notification has been canceled 