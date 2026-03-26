# Inventario Bodega - Backend API

API REST para el sistema de inventario de bodega. Gestiona productos, lotes, rutas de distribución y movimientos de inventario.

## Tech Stack

- **Laravel 12** + **Laravel Sail** (Docker)
- **PostgreSQL 18** (base de datos)
- **Redis** (cache y colas)
- **Filament v3.2** (panel administrativo)
- **Laravel Sanctum** (autenticación por tokens)
- **Spatie Permission** (roles y permisos)
- **PHP 8.2+**

## Requisitos

- Docker Desktop
- PHP 8.2+ y Composer (para comandos locales)

## Instalación

```bash
# Clonar e instalar dependencias
composer install

# Copiar archivo de entorno
cp .env.example .env

# Levantar contenedores
./vendor/bin/sail up -d

# Generar clave de aplicación
./vendor/bin/sail artisan key:generate

# Ejecutar migraciones con seeders
./vendor/bin/sail artisan migrate --seed
```

## Comandos de desarrollo

```bash
# Levantar / detener contenedores
sail up -d
sail down

# Migraciones
sail artisan migrate
sail artisan migrate:fresh --seed

# Crear usuario admin de Filament
sail artisan make:filament-user

# Limpiar caché
sail artisan config:clear
sail artisan cache:clear
sail artisan route:clear

# Reconstruir contenedores
sail build --no-cache
sail up -d
```

## Accesos

| Recurso | URL |
|---------|-----|
| Panel admin (Filament) | http://localhost/admin |
| API | http://localhost/api |

**Credenciales por defecto:** `admin@bodega.com` / `password`

## Endpoints API

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/api/auth/login` | Login, devuelve token |
| POST | `/api/auth/logout` | Logout, revoca token |
| GET | `/api/auth/user` | Usuario autenticado |
| GET/POST | `/api/productos` | Listar / crear productos |
| GET/PUT/DELETE | `/api/productos/{id}` | Detalle / actualizar / eliminar |
| POST | `/api/productos/sync` | Bulk import desde Excel |
| GET/POST | `/api/lotes` | Listar / crear lotes |
| GET/PUT/DELETE | `/api/lotes/{id}` | Detalle / actualizar / eliminar |
| GET/POST | `/api/rutas` | Listar / crear rutas |
| GET/PUT | `/api/rutas/{id}` | Detalle / actualizar |
| GET | `/api/movimientos` | Listar movimientos |
| POST | `/api/movimientos` | Registrar movimiento |
| GET | `/api/scan/{barcode}` | Buscar producto por barcode |

Todos los endpoints (excepto login) requieren header `Authorization: Bearer {token}`.

## Roles

| Rol | Acceso |
|-----|--------|
| admin | Panel Filament + API completa |
| supervisor | Panel Filament + API completa |
| operador | Solo API (mobile) |

## Estructura del proyecto

```
app/
├── Enums/                  # EstadoLote, EstadoRuta, TipoMovimiento
├── Filament/Resources/     # Recursos del panel admin
├── Http/Controllers/Api/   # Controladores REST
├── Http/Resources/         # API Resources (transformers)
└── Models/                 # Producto, Lote, Ruta, MovimientoInventario, User
database/
├── migrations/             # Esquema de base de datos
└── seeders/                # Roles y usuario admin
routes/
└── api.php                 # Rutas de la API
```

## Base de datos

- **productos** - Inventario con SKU, nombre, precio, stock
- **lotes** - Tracking de lotes con fechas de fabricación/vencimiento
- **rutas** - Rutas de distribución con operador y estado
- **movimiento_inventarios** - Log de entradas/salidas de inventario
- **users** - Usuarios con roles (admin, supervisor, operador)

## Docker (compose.yaml)

| Servicio | Puerto |
|----------|--------|
| Laravel (PHP 8.5) | 80 |
| PostgreSQL 18 | 5432 |
| Redis | 6379 |
| Vite dev server | 5173 |
