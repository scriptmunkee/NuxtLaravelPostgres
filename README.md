# 🐾 Pet Listing Platform

A modern pet listing website built with Laravel, Nuxt, and PostgreSQL.

## Tech Stack

- **Frontend**: Nuxt 4 (Vue 3) - Modern, performant frontend framework
- **Backend**: Laravel 12 - Robust PHP framework for APIs
- **Database**: PostgreSQL 16 - Reliable relational database
- **Development**: Docker & Docker Compose for easy local setup

## Project Structure

```
.
├── backend/          # Laravel 12 API backend
├── frontend/         # Nuxt 4 frontend application
├── docker-compose.yml # PostgreSQL & pgAdmin setup
└── README.md         # This file
```

## Prerequisites

- PHP 8.4+
- Composer 2.8+
- Node.js 22+ & npm 10+
- Docker & Docker Compose (for PostgreSQL)

## Getting Started

### 1. Clone the Repository

```bash
git clone <repository-url>
cd NuxtLaravelPostgres
```

### 2. Start PostgreSQL Database

```bash
docker-compose up -d
```

This will start:
- PostgreSQL on `localhost:5432`
- pgAdmin on `http://localhost:5050` (optional database management UI)
  - Email: `admin@petlisting.local`
  - Password: `admin`

### 3. Backend Setup (Laravel)

```bash
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Start Laravel development server
php artisan serve
```

The Laravel API will be available at `http://localhost:8000`

### 4. Frontend Setup (Nuxt)

```bash
cd frontend

# Install dependencies
npm install

# Copy environment file (optional)
cp .env.example .env

# Start development server
npm run dev
```

The Nuxt frontend will be available at `http://localhost:3000`

## Development Workflow

### Backend (Laravel)

- **Development Server**: `php artisan serve`
- **Run Migrations**: `php artisan migrate`
- **Create Migration**: `php artisan make:migration create_table_name`
- **Create Model**: `php artisan make:model ModelName -m`
- **Create Controller**: `php artisan make:controller ControllerName`
- **Run Tests**: `php artisan test`

### Frontend (Nuxt)

- **Development Server**: `npm run dev`
- **Build for Production**: `npm run build`
- **Preview Production Build**: `npm run preview`
- **Generate Static Site**: `npm run generate`

### Database Management

**Using pgAdmin** (Web UI):
1. Navigate to `http://localhost:5050`
2. Login with credentials from docker-compose.yml
3. Add server connection:
   - Host: `postgres` (or `localhost` if connecting from host)
   - Port: `5432`
   - Database: `pet_listing`
   - Username: `postgres`
   - Password: `postgres`

**Using psql** (Command Line):
```bash
docker exec -it pet_listing_postgres psql -U postgres -d pet_listing
```

## Environment Variables

### Backend (.env)

```env
APP_NAME="Pet Listing"
APP_URL=http://localhost:8000
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=pet_listing
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

### Frontend (.env)

```env
NUXT_PUBLIC_API_BASE=http://localhost:8000/api
```

## Next Steps

- [ ] Database schema planning
- [ ] Model and migration creation
- [ ] API endpoint development
- [ ] Frontend component development
- [ ] Authentication system
- [ ] Image upload functionality
- [ ] Search and filter features

## Useful Commands

### Docker

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs -f

# Restart PostgreSQL
docker-compose restart postgres
```

### Laravel Artisan

```bash
# List all routes
php artisan route:list

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Run tinker (interactive console)
php artisan tinker
```

## Contributing

This is a work in progress. Database planning and feature development coming soon!

## License

This project is open-source and available under the MIT License.
