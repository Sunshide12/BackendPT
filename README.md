# Backend — Sistema de Gestión de Pedidos

API REST desarrollada con Laravel 12 para la gestión de productos, clientes, inventario y pedidos.

## Stack

- **Laravel 12** + PHP 8.5
- **MySQL** (prueba_tecnica)
- **Eloquent ORM**

## Requisitos

- PHP >= 8.2
- Composer
- MySQL

## Instalación

```bash
git clone https://github.com/Sunshide12/BackendPT.git
cd BackendPT
composer install
cp .env.example .env
php artisan key:generate
```

Configura la base de datos en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prueba_tecnica
DB_USERNAME=root
DB_PASSWORD=
```

Crea la base de datos en MySQL:

```sql
CREATE DATABASE prueba_tecnica;
```

```bash
php artisan migrate --seed
php artisan serve
```

El servidor queda disponible en `http://localhost:8000`.

## Base de datos

Las migraciones crean 5 tablas con sus relaciones:

| Tabla         | Registros seeder |
| ------------- | ---------------- |
| categories    | 10               |
| products      | 50               |
| clients       | 30               |
| orders        | 40               |
| order_details | ~100             |

## Endpoints

| Método | Endpoint                             | Descripción                            |
| ------ | ------------------------------------ | -------------------------------------- |
| GET    | `/api/categories`                    | Listar categorías                      |
| POST   | `/api/categories`                    | Crear categoría                        |
| PUT    | `/api/categories/{id}`               | Actualizar categoría                   |
| DELETE | `/api/categories/{id}`               | Eliminar categoría                     |
|        |                                      |                                        |
| GET    | `/api/products?search=&category_id=` | Listar productos con filtros           |
| POST   | `/api/products`                      | Crear producto                         |
| PUT    | `/api/products/{id}`                 | Actualizar producto                    |
| DELETE | `/api/products/{id}`                 | Eliminar producto                      |
|        |                                      |                                        |
| GET    | `/api/clients?search=`               | Listar clientes                        |
| POST   | `/api/clients`                       | Crear cliente                          |
| PUT    | `/api/clients/{id}`                  | Actualizar cliente                     |
| DELETE | `/api/clients/{id}`                  | Eliminar cliente                       |
|        |                                      |                                        |
| GET    | `/api/inventory?search=`             | Ver existencias ordenadas por stock    |
| PATCH  | `/api/inventory/{id}`                | Ajustar stock manualmente              |
| GET    | `/api/orders`                        | Listar pedidos con cliente y productos |
| POST   | `/api/orders`                        | Registrar pedido                       |
| PUT    | `/api/orders/{id}`                   | Actualizar status del pedido           |
| DELETE | `/api/orders/{id}`                   | Eliminar pedido                        |

## Lógica de negocio

- Al registrar un pedido se valida stock disponible y se descuenta automáticamente del inventario.
- Al cancelar o eliminar un pedido el stock se restaura.
- No se puede eliminar una categoría con productos asociados ni un cliente con pedidos activos.
- Los pedidos cancelados no pueden ser modificados.

## Estructura del proyecto

```
app/
├── Http/Controllers/
│   ├── CategoryController.php
│   ├── ProductController.php
│   ├── ClientController.php
│   ├── OrderController.php
│   └── InventoryController.php
├── Models/
│   ├── Category.php
│   ├── Product.php
│   ├── Client.php
│   ├── Order.php
│   └── OrderDetail.php
database/
├── migrations/
├── factories/
└── seeders/
routes/
└── api.php
```
