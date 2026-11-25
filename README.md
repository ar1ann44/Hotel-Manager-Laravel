# Penglai - Gestión Hotelera

Sistema para controlar las habitaciones y reservaciones de un hotel, hecho con Laravel.

## Requisitos

- PHP 8.3 o superior
- Composer
- Node.js
- MySQL o MariaDB

## Cómo ejecutarlo

1. Instalar dependencias:

   ```
   composer install
   npm install
   ```

2. Copiar el archivo de configuración y generar la llave:

   ```
   cp .env.example .env
   php artisan key:generate
   ```

3. En el archivo `.env` poner los datos de la base de datos:

   ```
   DB_CONNECTION=mysql
   DB_DATABASE=hotel_manager
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. Crear las tablas con datos de ejemplo:

   ```
   php artisan migrate --seed
   ```

5. Iniciar el sistema:

   ```
   npm run build
   php artisan serve
   ```

6. Abrir http://127.0.0.1:8000 en el navegador.

## Usuarios de prueba

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | admin@penglai.com | admin123 |
| Recepcionista | recepcion@penglai.com | recepcion123 |

## Pruebas

```
php artisan test
```

## Medición de rendimiento

Usa una base de datos aparte llamada `hotel_manager_rendimiento`:

```
DB_DATABASE=hotel_manager_rendimiento php artisan medir:rendimiento
```
