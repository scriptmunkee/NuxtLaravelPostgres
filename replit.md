# Pet Listings Marketplace

## Overview
A full-stack pet listings marketplace built with Nuxt.js frontend and Laravel backend API. The platform allows users to browse dog listings, with separate buyer and seller roles for managing pets for sale.

**Current State**: Core backend infrastructure and basic frontend setup complete. Ready for frontend feature development.

## Recent Changes (October 14, 2025)
- ✅ Laravel 12 backend with PostgreSQL database configured
- ✅ Database schema: users (buyer/seller roles), pet_listings, favorites
- ✅ Laravel Sanctum API authentication system implemented
- ✅ API endpoints: Auth (register/login), Pet Listings CRUD, Favorites
- ✅ Nuxt.js 3 frontend with Tailwind CSS installed
- ✅ Workflow configured: Laravel (port 8000) + Nuxt (port 5000)
- ✅ Fixed critical routing bug to allow public listing access

## Architecture

### Backend (Laravel 12)
- **Framework**: Laravel 12 with PHP 8.2
- **Database**: PostgreSQL (Replit managed)
- **Authentication**: Laravel Sanctum (token-based API auth)
- **API Base URL**: `http://localhost:8000/api`

### Frontend (Nuxt.js 3)
- **Framework**: Nuxt.js 3 with Vue 3
- **Styling**: Tailwind CSS
- **TypeScript**: Enabled
- **Dev Server**: Port 5000 (0.0.0.0)

### Database Schema
1. **users**: id, name, email, password, role (buyer/seller), timestamps
2. **pet_listings**: id, user_id, title, description, breed, age_months, price, gender, images (JSON), is_active, timestamps
3. **favorites**: id, user_id, pet_listing_id, timestamps

### API Routes
**Public Routes:**
- GET /api/pet-listings - Browse all active listings
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
│   ├── nuxt.config.ts
│   └── app/
└── start.sh              # Startup script
```

## Development
- **Start App**: Workflow "Server" runs both Laravel and Nuxt
- **Laravel**: http://0.0.0.0:8000
- **Nuxt**: http://0.0.0.0:5000
- **Database Migrations**: `cd backend && php artisan migrate`

## Next Steps
1. Build authentication pages (login/register) in Nuxt
2. Create pet listing browse/detail pages with SEO optimization
3. Implement seller dashboard for managing listings
4. Build buyer dashboard with favorites functionality
5. Add image upload capability
6. Implement search and filter features
