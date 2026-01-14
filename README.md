# Laravel E-commerce API

## Quick Start

### Prerequisites
- Docker and Docker Compose

### Installation

1. **Copy environment file**
```bash
cp .env.example .env
```

2. **Start Docker services**
```bash
docker-compose up -d --build
```

3. **Generate application key**
```bash
docker-compose exec app php artisan key:generate
```

4. **Run migrations**
```bash
docker-compose exec app php artisan migrate
```

5. **Access the API**
```
http://localhost:8000/api/health
```

**Note:** Dependencies are automatically installed on first container start.

### Service Access

- **API**: http://localhost:8000
- **Adminer** (Database): http://localhost:8080
  - System: PostgreSQL
  - Server: postgres
  - Username: postgres
  - Password: postgres
  - Database: laravel_development
- **MailCatcher** (Email): http://localhost:1080
- **RabbitMQ Management**: http://localhost:15672 (guest/guest)

### Useful Commands

```bash
# Stop services
docker-compose down

# View logs
docker-compose logs -f app

# Run tests
docker-compose exec app php artisan test

# Access container shell
docker-compose exec app bash

# Run scheduled command manually
docker-compose exec app php artisan tokens:clean-expired

# View scheduler logs
docker-compose logs -f scheduler
```
