# Virgo API

Trading platform API with real-time capabilities built with Laravel 12.

> **Note:** This is the backend API only. The frontend application is in a separate repository.
>
> Frontend Repository: https://github.com/lemaia/virgo-app

---

## Requirements

- Docker & Docker Compose
- Git

---

## Quick Start

Run these commands in order:

```bash
# 1. Clone the repository
git clone git@github.com:lemaia/virgo-api.git
cd virgo-api

# 2. Copy environment file
cp .env.example .env

# 3. Install PHP dependencies (first time only, to get Sail)
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs

# 4. Start Docker containers (PostgreSQL, Redis, App)
./vendor/bin/sail up -d

# 5. Generate application key
./vendor/bin/sail artisan key:generate

# 6. Run database migrations
./vendor/bin/sail artisan migrate

# 7. Start queue worker (in a separate terminal or background)
./vendor/bin/sail artisan queue:work

# 8. Start WebSocket server (in a separate terminal)
./vendor/bin/sail artisan reverb:start
```

The application should now be running at http://localhost

---

## Environment Variables

### Reverb (WebSocket Server)

Located in `.env` file:

```env
REVERB_APP_ID=940360
REVERB_APP_KEY=<your-key>
REVERB_APP_SECRET=<your-secret>
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### Trading Fee

Located in `.env` file:

- `TRADE_FEE_PERCENT=150` - This means 1.5% fee (value in basis points / 100)
- Config file: `config/trading.php`

---

## Business Rules

### Initial User Credits

When a new user registers, they automatically receive:

- **$50,000 USD** balance
- **0.5 BTC**
- **0.5 ETH**

---

## API Endpoints

### Public Routes

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register new user |
| POST | `/api/login` | User login |

### Authenticated Routes

Requires Bearer token from login.

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/logout` | Logout |
| GET | `/api/user` | Get current user info |
| GET | `/api/orders` | List user orders |
| POST | `/api/orders/buy` | Create buy order |
| POST | `/api/orders/sell` | Create sell order |
| POST | `/api/orders/{order}/cancel` | Cancel order |
| GET | `/api/orderbook/{symbol}` | Get order book for symbol |

---

## Useful Commands

| Command | Description |
|---------|-------------|
| `./vendor/bin/sail up -d` | Start containers |
| `./vendor/bin/sail down` | Stop containers |
| `./vendor/bin/sail artisan migrate` | Run migrations |
| `./vendor/bin/sail artisan queue:work` | Process queue jobs |
| `./vendor/bin/sail artisan horizon` | Start Horizon dashboard |
| `./vendor/bin/sail artisan reverb:start` | Start WebSocket server |
| `./vendor/bin/sail artisan telescope` | Debug dashboard |
| `./vendor/bin/sail composer test` | Run tests |

---

## URLs

| Service | URL |
|---------|-----|
| API | http://localhost |
| WebSocket (Reverb) | ws://localhost:8080 |
| Horizon Dashboard | http://localhost/horizon |
| Telescope Debug | http://localhost/telescope |

---

## Troubleshooting

### Port Conflicts

If you get port errors, check if these ports are free:
- `80` - Web server
- `5432` - PostgreSQL
- `6379` - Redis
- `8080` - Reverb WebSocket

### Permission Issues

If you get permission errors on `storage/` or `logs/`:

```bash
./vendor/bin/sail artisan storage:link
chmod -R 775 storage bootstrap/cache
```

### Database Connection Errors

- Make sure containers are running: `./vendor/bin/sail ps`
- Check `.env` has correct database credentials
- Wait a few seconds after `sail up` for PostgreSQL to initialize

### WebSocket Connection Issues

- Make sure Reverb is running: `./vendor/bin/sail artisan reverb:start`
- Check `REVERB_*` variables in `.env`
- Verify port 8080 is not blocked
