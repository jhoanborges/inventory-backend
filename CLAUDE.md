# CLAUDE.md - Backend

## Project overview

Laravel 12 REST API for a warehouse inventory system. Uses Laravel Sail (Docker) with PostgreSQL and Redis. Includes a Filament v3 admin panel.

## Tech stack

- Laravel 12, PHP 8.2+, Laravel Sail
- PostgreSQL 18, Redis
- Filament v3.2 (admin panel)
- Laravel Sanctum (API token auth)
- Spatie Laravel Permission (roles: admin, supervisor, operador)

## Development commands

```bash
sail up -d                      # Start containers
sail down                       # Stop containers
sail artisan migrate --seed     # Run migrations + seeders
sail artisan migrate:fresh --seed  # Reset DB
sail artisan config:clear       # Clear config cache
sail artisan route:list         # List routes
sail test                       # Run tests
```

## Architecture

- **Models**: `app/Models/` - Producto, Lote, Ruta, MovimientoInventario, User
- **Enums**: `app/Enums/` - EstadoLote, EstadoRuta, TipoMovimiento
- **API Controllers**: `app/Http/Controllers/Api/` - Auth, Producto, Lote, Ruta, Movimiento, Scan
- **API Resources**: `app/Http/Resources/` - JSON transformers
- **Filament Resources**: `app/Filament/Resources/` - Admin panel CRUD
- **Routes**: `routes/api.php` - All API endpoints
- **Migrations**: `database/migrations/`
- **Seeders**: `database/seeders/` - RoleSeeder, AdminUserSeeder

## Key patterns

- All API endpoints (except login) require `auth:sanctum` middleware
- API responses use Laravel API Resources for consistent JSON structure
- Enums are PHP 8.1 backed enums, cast in models via Laravel casts
- Filament resources auto-generate admin CRUD from model definitions
- CORS configured for desktop (localhost:3000, localhost:1420, tauri://localhost)

## Database tables

- `productos` - SKU, nombre, stock_actual, stock_minimo, barcode, precio
- `lotes` - numero_lote, cantidad, fecha_fabricacion, fecha_vencimiento, estado
- `rutas` - nombre, origen, destino, operador_id, vehiculo, estado
- `movimiento_inventarios` - producto_id, lote_id, ruta_id, user_id, tipo, cantidad
- `users` - Standard Laravel users with HasRoles trait

## Default credentials

- Email: `admin@bodega.com` / Password: `password`
- Admin panel: http://localhost/admin

## Conventions

- Language: Spanish for domain terms (producto, lote, ruta, movimiento), English for code structure
- Use `sail` alias for artisan commands (requires Sail shell alias or `./vendor/bin/sail`)
- Follow Laravel conventions for naming (snake_case DB columns, camelCase methods)
- API routes prefixed with `/api/`
