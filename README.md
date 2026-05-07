# StackWise-V2 (StackWise AI)

StackWise-V2 is a Laravel application that helps students decide on a **programming language**, **framework**, and **SDLC model** based on project inputs. It also includes an **AI assistant** powered by **Ollama** via API.

## Features

- Authentication (Laravel Breeze)
- Project stack recommendation engine + history
- Dashboard + documentation explorer
- AI chatbot using Ollama API (env-configurable)

## Tech stack

- PHP 8.3, Laravel 13
- Blade + Alpine.js + TailwindCSS (Vite)
- Pest (feature tests)

## Local setup

1. Install dependencies

```bash
composer install
npm install
```

2. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database settings.

3. Run migrations

```bash
php artisan migrate
```

4. Run the app

```bash
composer run dev
```

## Ollama chatbot configuration

Set these environment variables:

- `OLLAMA_API_URL` (default in `.env.example`): `http://localhost:11434/api/generate`
- `OLLAMA_MODEL` (example): `llama3.1`
- `OLLAMA_TIMEOUT` (seconds)
- `OLLAMA_API_KEY` (optional, only if your proxy requires it)

If Ollama is not configured or is offline, the UI will show a graceful error message.

## Production deployment (Laravel Cloud)

StackWise-V2 is designed to run with **environment variables only**. For Laravel Cloud:

- Set `APP_ENV=production` and `APP_DEBUG=false`
- Set `APP_KEY` to a generated key
- Configure database env vars (`DB_*`)
- Run migrations during deploy
- Build assets during deploy (`npm ci && npm run build`)

Recommended optimizations (Laravel Cloud handles this well, but these commands are safe):

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Testing

```bash
php artisan test --compact
```
