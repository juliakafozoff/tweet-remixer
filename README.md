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

## Stripe Membership & Billing

Remixer now requires a $10/month Stripe subscription with a 7-day free trial. To finish configuring billing:

1. Create a recurring $10 price in Stripe (or reuse an existing one) and enable a 7-day trial on that price, or rely on the in-app `STRIPE_TRIAL_DAYS` override.
2. Update these environment variables locally and on Laravel Cloud:
   - `STRIPE_KEY` / `STRIPE_SECRET`
   - `STRIPE_PRICE_ID` (the price identifier that represents the $10 plan)
   - `STRIPE_WEBHOOK_SECRET` (optional but recommended for webhook verification)
   - `STRIPE_TRIAL_DAYS` (defaults to 7 if omitted)
3. Run the new Cashier migrations: `php artisan migrate` (include `--force` during deploys).
4. Configure a Stripe webhook endpoint that points to `/stripe/webhook` and subscribes to `customer.subscription.*` along with `invoice.payment_*` events so Cashier keeps local records in sync.

Existing users are redirected to `/billing` until they start a subscription or continue within their trial/grace period. The Billing page also links to the hosted Stripe customer portal for self-service management.
When a member clicks “Continue on Stripe” we generate a Checkout Session and send them to Stripe’s hosted subscription flow; on success, Stripe redirects back to `/billing/success`.
