# Pet Listings Marketplace

## Overview
A full-stack pet listings marketplace built with Nuxt.js frontend and Laravel backend API. The platform allows users to browse dog listings with SEO-friendly URLs (domain/{breed}/{location}), with separate buyer and seller roles for managing pets for sale.

**Current State**: Full authentication system, dynamic routing, and deployment configuration complete. Ready for production deployment.

## Recent Changes (October 14, 2025)
- ✅ Laravel 12 backend with PostgreSQL database configured
- ✅ Database schema: users (buyer/seller roles), pet_listings (with location), favorites
- ✅ Laravel Sanctum API authentication system implemented
- ✅ Complete authentication pages (login/register) with composables
- ✅ Dynamic SEO-friendly routing: /{breed}/{location}
- ✅ API proxy configuration for production deployment
- ✅ Deployment configuration set up for Autoscale with dependency installation

## Architecture

### Backend (Laravel 12)
- **Framework**: Laravel 12 with PHP 8.2
- **Database**: PostgreSQL (Replit managed)
- **Authentication**: Laravel Sanctum (token-based API auth)
- **Production Port**: 8000 (internal)
- **Production Server**: php artisan serve (basic, consider upgrading to php-fpm for high traffic)

### Frontend (Nuxt.js 3)
- **Framework**: Nuxt.js 3 with Vue 3
- **Styling**: Tailwind CSS
- **TypeScript**: Enabled
- **Production Port**: 5000 (public-facing)
- **API Proxy**: /api/* proxies to Laravel backend (localhost:8000)

### Database Schema
1. **users**: id, name, email, password, role (buyer/seller), timestamps
2. **pet_listings**: id, user_id, title, description, breed, **location**, age_months, price, gender, images (JSON), is_active, timestamps
3. **favorites**: id, user_id, pet_listing_id, timestamps

### API Routes
**Public Routes:**
- GET /api/pet-listings - Browse all active listings (supports breed & location filters)
- GET /api/pet-listings/{id} - View specific listing
- POST /api/register - User registration
- POST /api/login - User login

**Protected Routes (require auth token):**
- POST /api/logout - User logout
- GET /api/user - Get current user
- POST /api/pet-listings - Create new listing (sellers)
- PUT /api/pet-listings/{id} - Update listing (sellers)
- DELETE /api/pet-listings/{id} - Delete listing (sellers)
- GET /api/my-listings - Get user's listings
- GET /api/favorites - Get user's favorites
- POST /api/favorites/{id}/toggle - Add/remove favorite

## Frontend Routes
- `/` - Homepage with search and all listings
- `/auth/login` - Login page
- `/auth/register` - Register page  
- `/{breed}/{location}` - Dynamic listing page (e.g., /golden-retriever/new-york)

## Project Structure
```
├── backend/               # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   └── Models/
│   ├── database/migrations/
│   └── routes/api.php
├── frontend/              # Nuxt.js app
│   ├── pages/
│   │   ├── index.vue
│   │   ├── [breed]/[location].vue
│   │   └── auth/
│   ├── composables/
│   │   ├── useApi.ts
│   │   └── useAuth.ts
│   └── nuxt.config.ts
├── build.sh              # Production build script (installs deps + builds)
├── run-production.sh     # Production run script (starts both servers)
└── start.sh              # Development startup script
```

## Development
- **Start App**: Workflow "Server" runs both Laravel and Nuxt in dev mode
- **Laravel**: http://0.0.0.0:8000
- **Nuxt**: http://0.0.0.0:5000
- **Database Migrations**: `cd backend && php artisan migrate`

## Deployment
- **Type**: Autoscale Deployment
- **Build**: `bash build.sh`
  - Installs backend dependencies: `composer install --no-dev --optimize-autoloader`
  - Installs frontend dependencies: `npm ci`
  - Builds Nuxt for production: `npm run build`
- **Run**: `bash run-production.sh`
  - Starts Laravel on port 8000
  - Starts Nuxt on port 5000
- **Public Port**: 5000 (Nuxt frontend with API proxy to Laravel)
- **Environment**: Production database configured via Replit secrets

## Notes
- Laravel uses `php artisan serve` for simplicity. For high-traffic production, consider upgrading to php-fpm with nginx/caddy.
- Both servers run in parallel; Nuxt proxies all /api requests to the Laravel backend.

## Next Steps
1. ✅ Authentication system complete
2. ✅ Dynamic breed/location routing implemented
3. ✅ Deployment configuration complete
4. Implement seller dashboard for managing listings
5. Build buyer dashboard with favorites functionality
6. Add image upload capability
7. Implement advanced search and filter features
