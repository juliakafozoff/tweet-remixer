## Remixer

Remixer helps you transform long-form content into ready-to-post tweet batches. The app now defaults to PostgreSQL so it can run smoothly on Laravel Cloud.

## Local Setup

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate
npm run dev # or npm run build
php artisan serve
```

Update the `.env` file with your local PostgreSQL credentials or a `DATABASE_URL`. SQLite still works if you change `DB_CONNECTION`, but Postgres is now the default.

## Deploying to Laravel Cloud with Postgres

1. **Create a managed Postgres database** in the Laravel Cloud dashboard.
2. **Populate environment variables** (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` or `DATABASE_URL`) with the credentials Laravel Cloud supplies.
3. **Deploy** your application and run migrations, e.g. `php artisan migrate --force`.

Sessions, cache, and queues already use database drivers, so the migrations will create the necessary tables automatically.

If you need to verify the connection locally before deploying, run:

```bash
php artisan migrate --database=pgsql
```

That command targets the Postgres configuration defined in `config/database.php`.

## Testing

```bash
php artisan test
```

Feature tests cover the landing page redirects and authentication workflow.

## Tweet Intent Handle

Set `X_HANDLE` in your environment file if you want to change the attribution added to the “Tweet it” button URLs.
