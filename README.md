# Backend — Sistema de Gestión de Pedidos

API REST desarrollada con Laravel 12 para gestionar productos, clientes, inventario y pedidos. Expone endpoints JSON que consume el frontend en React.

---

## Stack

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

---

## Requisitos

- PHP >= 8.2
- Composer
- MySQL

---

## Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/Sunshide12/BackendPT.git
cd BackendPT

# 2. Instalar dependencias
composer install

# 3. Configurar el entorno
cp .env.example .env
php artisan key:generate
```

Editar el `.env` con los datos de tu base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prueba_tecnica
DB_USERNAME=root
DB_PASSWORD=
```

Crear la base de datos en MySQL:

```sql
CREATE DATABASE prueba_tecnica;
```

Correr las migraciones con datos de prueba:

```bash
php artisan migrate --seed
```

Iniciar el servidor:

```bash
php artisan serve
```

La API queda disponible en `http://localhost:8000/api`.

---

## Base de datos

Las migraciones crean 5 tablas con sus relaciones. El seeder carga datos de prueba para poder usar el sistema de inmediato.

### categories
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint PK | |
| name | string | Nombre de la categoría |
| created_at / updated_at | timestamp | |

### products
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint PK | |
| name | string | Nombre del producto |
| price | decimal(10,2) | Precio unitario |
| stock | integer | Stock disponible (default 0) |
| category_id | FK → categories | Restricción: no se puede eliminar la categoría si tiene productos |
| created_at / updated_at | timestamp | |

### clients
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint PK | |
| name | string | Nombre completo |
| email | string unique | Correo electrónico |
| phone | string | Teléfono (max 20 caracteres) |
| created_at / updated_at | timestamp | |

### orders
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint PK | |
| client_id | FK → clients | Se elimina en cascada si se borra el cliente |
| date | date | Fecha del pedido |
| total | decimal(10,2) | Total calculado automáticamente |
| status | enum | `pending`, `completed`, `cancelled` (default: pending) |
| created_at / updated_at | timestamp | |

### order_details
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint PK | |
| order_id | FK → orders | Se elimina en cascada si se borra el pedido |
| product_id | FK → products | Se elimina en cascada si se borra el producto |
| quantity | integer | Cantidad pedida |
| unit_price | decimal(10,2) | Precio al momento del pedido |
| subtotal | decimal(10,2) | quantity × unit_price |
| created_at / updated_at | timestamp | |

### Datos de prueba (seeders)

| Tabla         | Registros |
|---------------|-----------|
| categories    | 10        |
| products      | 50        |
| clients       | 30        |
| orders        | 40        |
| order_details | ~100      |

---

## Endpoints

### Categorías
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/categories` | Listar todas |
| POST | `/api/categories` | Crear |
| PUT | `/api/categories/{id}` | Actualizar |
| DELETE | `/api/categories/{id}` | Eliminar |

### Productos
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/products?search=&category_id=` | Listar con filtros |
| POST | `/api/products` | Crear |
| PUT | `/api/products/{id}` | Actualizar |
| DELETE | `/api/products/{id}` | Eliminar |

### Clientes
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/clients?search=` | Listar |
| GET | `/api/clients/all` | Todos sin paginación (para dropdowns) |
| POST | `/api/clients` | Crear |
| PUT | `/api/clients/{id}` | Actualizar |
| DELETE | `/api/clients/{id}` | Eliminar |

### Inventario
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/inventory?search=` | Ver existencias |
| PATCH | `/api/inventory/{id}` | Ajustar stock manualmente |

### Pedidos
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/orders` | Listar con cliente y productos |
| POST | `/api/orders` | Registrar pedido |
| PUT | `/api/orders/{id}` | Actualizar estado |
| DELETE | `/api/orders/{id}` | Eliminar pedido |

---

## Lógica de negocio

### Pedidos
- Si se repite el mismo producto en varios ítems, las cantidades se agrupan antes de validar el stock.
- Si el stock es insuficiente para cualquier producto, la transacción completa se cancela y no se crea nada.
- El total se calcula automáticamente sumando `precio × cantidad` de cada producto.
- Al cancelar un pedido, el stock de todos los productos involucrados se restaura automáticamente.
- Al eliminar un pedido, el stock también se restaura sin importar el estado en que estaba.

### Inventario
- El stock se puede ajustar manualmente desde el módulo de inventario.
- El valor mínimo permitido es 0, no se acepta stock negativo.

### Categorías
- No se puede eliminar una categoría que tenga productos asociados (devuelve 422).

### Clientes
- No se puede eliminar un cliente que tenga pedidos registrados (devuelve 422).
- El email debe ser único por cliente.
- El teléfono tiene un máximo de 20 caracteres.

---

## Estructura del proyecto

```
app/
├── Http/Controllers/
│   ├── CategoryController.php   # CRUD de categorías — sin paginación, impide borrar si tiene productos asociados
│   ├── ProductController.php    # CRUD de productos — búsqueda por nombre, filtro por categoría, paginación de 10
│   ├── ClientController.php     # CRUD de clientes — búsqueda por nombre/email, paginación + endpoint /all sin paginar para dropdowns
│   ├── OrderController.php      # CRUD de pedidos — crea con DB::transaction, valida stock, descuenta inventario, restaura stock al cancelar/eliminar
│   └── InventoryController.php  # Listado de productos ordenado por stock + ajuste manual de stock (PATCH)
├── Models/
│   ├── Category.php             # hasMany(Product) — campos: name
│   ├── Product.php              # belongsTo(Category), hasMany(OrderDetail) — campos: name, price, stock, category_id
│   ├── Client.php               # hasMany(Order) — campos: name, email, phone
│   ├── Order.php                # belongsTo(Client), hasMany(OrderDetail) — campos: client_id, date, total, status
│   └── OrderDetail.php          # belongsTo(Order), belongsTo(Product) — campos: order_id, product_id, quantity, unit_price, subtotal
database/
├── migrations/                  # Tablas en orden: categories → products → clients → orders → order_details
├── factories/                   # Factories para Category, Client, Product y Order (con Faker)
└── seeders/                     # Seeders individuales por modelo + DatabaseSeeder que los orquesta
routes/
└── api.php                      # 4 apiResource (categories, products, clients, orders) + 2 rutas manuales para inventario + 1 para clients/all

```
