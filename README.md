# Pulse DevOps

Laravel Pulse cards for managing GCP-deployed Laravel applications. Adds migration management, database inspection, GCP Secret Manager, Cloud Run job execution, queue health monitoring, and environment overview — all directly inside your Pulse dashboard.

## Screenshots

<!-- TODO: Add screenshots -->
| Migrations Card | Database Card | Environment Card |
|:---:|:---:|:---:|
| ![Migrations](screenshots/migrations.png) | ![Database](screenshots/database.png) | ![Environment](screenshots/environment.png) |

| Queue Health | GCP Secrets | Cloud Run Jobs |
|:---:|:---:|:---:|
| ![Queue](screenshots/queue.png) | ![Secrets](screenshots/secrets.png) | ![Jobs](screenshots/jobs.png) |

## Requirements

- PHP ^8.2
- Laravel ^11.0 or ^12.0
- Laravel Pulse ^1.0 (installed in the host application)
- Google Cloud SDK credentials (for GCP features)

## Installation

```bash
composer require langtonmwanza/pulse-devops
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=pulse-devops-config
```

Add the cards to your Pulse dashboard at `resources/views/vendor/pulse/dashboard.blade.php`:

```blade
<x-pulse>
    {{-- Your existing Pulse cards --}}

    {{-- DevOps Cards --}}
    <livewire:pulse-devops.migrations cols="2" />
    <livewire:pulse-devops.database-tables cols="2" />
    <livewire:pulse-devops.queue-health cols="2" />
    <livewire:pulse-devops.gcp-secrets cols="2" />
    <livewire:pulse-devops.cloud-run-jobs cols="2" />
    <livewire:pulse-devops.environment cols="6" />
</x-pulse>
```

## Configuration

### Environment Variables

Add these to your `.env`:

```dotenv
# Enable/disable the entire package
PULSE_DEVOPS_ENABLED=true

# Comma-separated list of emails allowed in production
PULSE_DEVOPS_EMAILS=admin@example.com,ops@example.com

# GCP configuration (required for Secrets & Cloud Run cards)
GOOGLE_CLOUD_PROJECT_ID=my-gcp-project
GOOGLE_CLOUD_REGION=us-central1
```

### Config Reference

```php
// config/pulse-devops.php

return [
    'authorize' => env('PULSE_DEVOPS_ENABLED', true),

    'allowed_emails' => array_filter(explode(',', env('PULSE_DEVOPS_EMAILS', ''))),

    'gcp' => [
        'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
        'region' => env('GOOGLE_CLOUD_REGION', 'us-central1'),
    ],

    'cards' => [
        'migrations'     => true,
        'database_tables' => true,
        'gcp_secrets'    => true,
        'cloud_run_jobs' => true,
        'queue_health'   => true,
        'environment'    => true,
    ],
];
```

## Authorization

By default, all cards and routes are accessible in `local` environment only. For production, the package defines a `viewPulseDevops` gate.

**Option 1**: Set allowed emails via `.env`:

```dotenv
PULSE_DEVOPS_EMAILS=admin@example.com,devops@example.com
```

**Option 2**: Override the gate in your `AppServiceProvider`:

```php
use Illuminate\Support\Facades\Gate;

Gate::define('viewPulseDevops', function ($user) {
    return $user->hasRole('admin');
});
```

## Cards

### Migrations

**Dashboard card**: Shows total, ran, and pending migration counts with a status badge.

**Full page** (`/pulse/devops/migrations`):
- View all migrations with their status and batch number
- Run pending migrations with `--force`
- Rollback the last batch with confirmation
- View command output inline

### Database Tables

**Dashboard card**: Shows total table count, total database size, and top 3 largest tables.

**Full page** (`/pulse/devops/database`):
- Full table listing with row counts, data size, index size, total size
- Sorted by size descending
- Real-time search/filter by table name

### GCP Secrets

**Dashboard card**: Shows number of accessible secrets in the GCP project.

**Full page** (`/pulse/devops/secrets`):
- List all secrets with creation dates
- Show/hide secret values (masked by default)
- Create new secrets
- Update existing secrets (adds a new version)

### Cloud Run Jobs

**Dashboard card**: Shows number of Cloud Run jobs and last execution time.

**Full page** (`/pulse/devops/cloud-run-jobs`):
- List all jobs with execution counts and last run time
- Execute any job with a single click
- View execution history

### Queue Health

**Dashboard card** (no full page):
- Shows queue driver, pending job count, and failed job count
- Green badge when zero failures, red badge otherwise
- Retry All Failed and Flush Failed buttons with confirmation

### Environment

**Dashboard card** (wide, no full page):
- Read-only grid of environment info: app name, environment, debug mode, PHP/Laravel versions, database, cache, session, filesystem, mail, and queue configuration

## Routes

All routes are prefixed with `/pulse/devops` and protected by the `AuthorizeDevops` middleware:

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/pulse/devops/migrations` | Migrations full page |
| POST | `/pulse/devops/migrations/run` | Run pending migrations |
| POST | `/pulse/devops/migrations/rollback` | Rollback last batch |
| GET | `/pulse/devops/database` | Database tables full page |
| GET | `/pulse/devops/secrets` | GCP Secrets full page |
| POST | `/pulse/devops/secrets` | Create a new secret |
| PUT | `/pulse/devops/secrets/{name}` | Update a secret (new version) |
| GET | `/pulse/devops/secrets/{name}/value` | Fetch secret value (JSON) |
| GET | `/pulse/devops/cloud-run-jobs` | Cloud Run Jobs full page |
| POST | `/pulse/devops/cloud-run-jobs/{name}/execute` | Execute a Cloud Run job |

## GCP Permissions

The service account running your application needs these IAM permissions:

### Secret Manager
- `secretmanager.secrets.list`
- `secretmanager.secrets.create`
- `secretmanager.versions.access`
- `secretmanager.versions.add`

### Cloud Run
- `run.jobs.list`
- `run.jobs.run`
- `run.executions.list`

### Authentication

**On Cloud Run**: Uses Application Default Credentials (ADC) automatically via the attached service account. No key file needed.

**Local development**: Run `gcloud auth application-default login` to set up credentials.

## Testing

```bash
composer test
```

Or with Pest directly:

```bash
./vendor/bin/pest
```

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Commit your changes: `git commit -am 'Add my feature'`
4. Push to the branch: `git push origin feature/my-feature`
5. Open a Pull Request

Please ensure all tests pass and follow PSR-12 coding standards.

## License

MIT License. See [LICENSE](LICENSE) for details.
