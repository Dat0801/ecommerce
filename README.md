# E-Commerce Platform

A production-ready e-commerce platform built with **Laravel 11+ REST API** (Backend) and **Vue 3 + Vite** (Frontend). This project provides a clean, scalable architecture ready for implementation of business features.

## Project Structure

```
ecommerce/
├── backend/              # Laravel 11+ REST API
│   ├── app/
│   │   ├── Services/     # Business logic services
│   │   ├── Repositories/ # Data access layer
│   │   ├── Interfaces/   # Repository & service contracts
│   │   ├── Policies/     # Authorization policies
│   │   └── Http/
│   │       └── Controllers/Api/V1/ # API controllers
│   ├── routes/
│   │   └── api.php       # API routes (v1)
│   ├── config/
│   │   ├── cors.php      # CORS configuration
│   │   ├── sanctum.php   # Authentication config
│   │   └── l5-swagger.php # Swagger/OpenAPI config
│   └── .env              # Environment configuration
│
└── frontend/             # Vue 3 + Vite SPA
    ├── src/
    │   ├── pages/        # Page components (Home, Login, Register, Cart)
    │   ├── components/   # Reusable components
    │   ├── layouts/      # Layout components
    │   ├── services/     # API client & HTTP utilities
    │   ├── stores/       # Pinia stores (auth, cart)
    │   ├── router/       # Vue Router configuration
    │   ├── App.vue       # Root component
    │   └── main.js       # Application entry point
    ├── tailwind.config.js   # Tailwind configuration
    ├── postcss.config.js    # PostCSS configuration
    ├── .env             # Environment configuration
    └── vite.config.js   # Vite configuration
```

## Prerequisites

- **PHP** 8.2+
- **Composer** 2.2+
- **Node.js** 18+
- **npm** or **yarn**
- **MySQL** 8.0+ or SQLite (configured by default)

## Backend Setup

### 1. Install Dependencies

```bash
cd ecommerce/backend
composer install
```

### 2. Configure Environment

```bash
# .env is already copied, but update if needed
cp .env.example .env
```

Key configurations in `.env`:
- `APP_NAME=ECommerce`
- `APP_URL=http://localhost:8000`
- `APP_KEY` - Auto-generated during installation
- `DB_CONNECTION=sqlite` (default, change to mysql if needed)

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Run Migrations

```bash
php artisan migrate
```

## Frontend Setup

### 1. Install Dependencies

```bash
cd ecommerce/frontend
npm install
```

### 2. Configure Environment

Create `.env` file (already created with default values):

```env
VITE_API_BASE_URL=http://localhost:8000/api/v1
```

## Running the Project

### Start Backend (Laravel Development Server)

```bash
cd ecommerce/backend
php artisan serve
```

Backend will be available at: `http://localhost:8000`

### Start Frontend (Vite Development Server)

```bash
cd ecommerce/frontend
npm run dev
```

Frontend will be available at: `http://localhost:5173`

## Available Endpoints

### Health Check

- **GET** `/api/v1/ping` - API health status

Response:
```json
{
  "status": "healthy",
  "timestamp": "2024-01-11T12:00:00Z",
  "api_version": "v1"
}
```

### API Documentation

Swagger/OpenAPI documentation is available at:
- **URL**: `http://localhost:8000/api/documentation`

## Frontend Routes

| Route | Component | Purpose |
|-------|-----------|---------|
| `/` | Home.vue | Home page with navigation |
| `/login` | Login.vue | User login form |
| `/register` | Register.vue | User registration form |
| `/cart` | Cart.vue | Shopping cart view |

## Installed Packages

### Backend

- **laravel/framework** (11) - Core framework
- **laravel/sanctum** (4.2) - Token-based authentication
- **darkaonline/l5-swagger** (10) - Swagger/OpenAPI documentation
- **guzzlehttp/guzzle** (7.10) - HTTP client

### Frontend

- **vue** (3) - JavaScript framework
- **vue-router** (4) - Routing
- **pinia** - State management
- **axios** - HTTP client
- **tailwindcss** (3) - CSS utility framework

## Architecture

### Backend Architecture

#### Service Layer Pattern
- **Services**: Business logic and operations
- **Repositories**: Data access abstraction
- **Interfaces**: Service and Repository contracts
- **Policies**: Authorization logic

#### API Versioning
- All API routes are prefixed with `/api/v1`
- Future versions can be added as `/api/v2`, etc.

#### Authentication
- Uses Laravel Sanctum for token-based authentication
- Tokens stored in `auth_token` localStorage on frontend

#### CORS Configuration
- Enabled for `http://localhost:5173` (frontend)
- Configured in `config/cors.php` and `bootstrap/app.php`

### Frontend Architecture

#### State Management (Pinia)
- **Auth Store**: User authentication state and token management
- **Cart Store**: Shopping cart items and operations

#### Service Layer
- **API Client**: Axios instance with interceptors
  - Automatic token injection for authenticated requests
  - Response error handling (401 redirects to login)

#### Routing
- Vue Router with lazy-loaded routes
- Base routes for authentication and shopping flow

#### Styling
- TailwindCSS for utility-first styling
- Responsive design on all components

## Environment Configuration

### Backend (.env)

```env
APP_NAME=ECommerce              # Application name
APP_ENV=local                   # Environment (local, production)
APP_DEBUG=true                  # Debug mode
APP_URL=http://localhost:8000   # Backend URL
APP_KEY=base64:...              # Encryption key (auto-generated)

# Database (SQLite by default)
DB_CONNECTION=sqlite

# Cache & Queue
CACHE_STORE=database
QUEUE_CONNECTION=database
```

For MySQL, uncomment and configure:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce
DB_USERNAME=root
DB_PASSWORD=
```

### Frontend (.env)

```env
VITE_API_BASE_URL=http://localhost:8000/api/v1
```

## Development Workflow

### Adding a New API Endpoint

1. Create a controller in `app/Http/Controllers/Api/V1/`
2. Create methods with Swagger annotations for documentation
3. Add routes in `routes/api.php`
4. Create repository interface in `app/Interfaces/`
5. Create repository implementation in `app/Repositories/`
6. Register binding in `app/Providers/RepositoryServiceProvider.php`

### Adding a Pinia Store

1. Create store file in `src/stores/`
2. Use `defineStore()` with composition API
3. Define state, getters, actions
4. Import and use in components with `useStore()`

### Adding a New Page

1. Create `.vue` file in `src/pages/`
2. Add route in `src/router/index.js`
3. Create components in `src/components/` as needed

## CORS Configuration

CORS is pre-configured to allow:
- **Origin**: `http://localhost:5173` (frontend development)
- **Methods**: GET, POST, PUT, DELETE, PATCH, OPTIONS
- **Headers**: All headers allowed
- **Credentials**: Enabled for authentication

To add production frontend URL, update `config/cors.php`:

```php
'allowed_origins' => [
    'http://localhost:5173',      // Development
    'https://example.com',         // Production
],
```

## API Authentication Flow

1. User submits credentials to login endpoint
2. Backend returns authentication token
3. Frontend stores token in localStorage
4. Axios interceptor adds token to all requests: `Authorization: Bearer {token}`
5. Backend validates token via Sanctum middleware
6. Expired/invalid tokens redirect to login

## Building for Production

### Backend

1. Update `.env` for production:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://api.example.com
   ```

2. Install dependencies:
   ```bash
   composer install --no-dev
   ```

3. Run migrations:
   ```bash
   php artisan migrate --force
   ```

4. Cache configuration:
   ```bash
   php artisan config:cache
   php artisan route:cache
   ```

### Frontend

1. Build for production:
   ```bash
   npm run build
   ```

2. Output will be in `dist/` directory
3. Deploy `dist/` contents to your web server
4. Configure your server for SPA routing (all routes to index.html)

## Testing

### Backend Tests

```bash
cd ecommerce/backend
php artisan test
```

### Frontend Tests

```bash
cd ecommerce/frontend
npm run test
```

## Troubleshooting

### CORS Errors
- Ensure frontend URL is in `config/cors.php` `allowed_origins`
- Check that CORS middleware is registered in `bootstrap/app.php`

### API Not Responding
- Ensure backend is running: `php artisan serve`
- Check `VITE_API_BASE_URL` in frontend `.env`
- Verify no port conflicts (8000, 5173)

### Token Not Persisting
- Check browser localStorage for `auth_token`
- Verify Axios interceptor is configured correctly
- Check CORS credentials setting

## Best Practices

1. **Services**: Keep business logic in Service classes
2. **Repositories**: Use repositories for all database queries
3. **Validation**: Validate on both backend and frontend
4. **Error Handling**: Return proper HTTP status codes
5. **Documentation**: Keep Swagger annotations updated
6. **State**: Use Pinia stores for global state only
7. **Components**: Keep components focused and reusable

## Security Notes

- Never commit `.env` files with real credentials
- Use strong `APP_KEY` in production
- Implement rate limiting for authentication endpoints
- Sanitize user inputs in backend
- Use HTTPS in production
- Keep dependencies updated: `composer update`, `npm update`

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Vue 3 Documentation](https://vuejs.org)
- [Pinia Documentation](https://pinia.vuejs.org)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Swagger/OpenAPI](https://swagger.io)
- [TailwindCSS](https://tailwindcss.com)

## License

This project is open-source software licensed under the MIT license.

## Support

For issues or questions, please refer to the documentation or create an issue in the repository.

---

**Last Updated**: January 11, 2024
**Version**: 1.0.0
