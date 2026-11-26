# PENGLAI — Sistema de Gestión Hotelera

**PENGLAI (蓬莱客栈)** es una aplicación web integral diseñada para administrar la operación diaria de un hotel boutique o propiedad pequeña desde un solo lugar: desde la consulta de disponibilidad en tiempo real y cotización en divisas extranjeras, hasta el registro de reservaciones, *check-in*, *check-out* y control de limpieza/mantenimiento de habitaciones.

---

## Equipo y Contexto Académico

* **Institución:** Universidad Autónoma de Baja California (UABC) — Facultad de Ciencias de la Ingeniería y Tecnología (FCITEC)
* **Materia:** Herramientas de Software
* **Profesor:** Emmanuel Gómez Gutiérrez
* **Integrantes:**
  * Jiselle Millán Núñez
  * Ariana Osuna Aguilar
* **Prototipo UI/UX (Figma):** [Ver diseño en Figma](https://www.figma.com/design/DjLvHqg1YuA7i7qd6IBHZL/Sin-t%C3%ADtulo?node-id=0-1&p=f&t=pVdBViAgpbU37HTm-0)

---

## Stack Tecnológico

* **Backend:** Laravel 13 (PHP 8.5)
* **Frontend:** Vistas Blade + Tailwind CSS (Diseño responsivo)
* **Base de Datos:** MySQL / MariaDB 13
* **API Externa:** [Frankfurter API](https://frankfurter.dev/) (Conversión informativa en vivo de tarifas de MXN a otras monedas)
* **Pruebas Automatizadas:** PHPUnit 12.5 (con base de datos SQLite en memoria)

---

## Guía de Instalación y Ejecución Local

Sigue estos pasos para levantar el proyecto en tu entorno local:

### 1. Requisitos previos
* PHP $\ge 8.3$ (probado en PHP 8.5)
* Composer
* MySQL o MariaDB
* Node.js y NPM (opcional, si deseas recompilar assets)

### 2. Clonar el repositorio e instalar dependencias
```bash
git clone https://github.com/ar1ann44/Hotel-Manager-Laravel.git
cd Hotel-Manager-Laravel
composer install
```

### 3. Configurar el entorno y la base de datos
Copia el archivo de configuración de ejemplo y genera la clave de la aplicación:
```bash
cp .env.example .env
php artisan key:generate
```

Edita el archivo `.env` con las credenciales de tu base de datos MySQL/MariaDB (asegúrate de crear previamente la base de datos vacía, por ejemplo `hotel_manager`):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotel_manager
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Ejecutar migraciones y datos de prueba (Seeders)
Este comando creará todas las tablas, índices de rendimiento y cargará los usuarios de demostración, habitaciones y reservaciones iniciales:
```bash
php artisan migrate --seed
```

### 5. Iniciar el servidor de desarrollo
```bash
php artisan serve
```
Accede al sistema desde tu navegador en: `http://127.0.0.1:8000`

---

## Roles y Cuentas de Demostración

El sistema cuenta con control de acceso basado en roles (*Middleware* y validación en servidor con error `403` ante accesos no autorizados):

| Rol | Correo Electrónico | Contraseña | Permisos Principales |
| :--- | :--- | :--- | :--- |
| **Administrador** | `admin@penglai.com` | `admin123` | Acceso total: crear/editar habitaciones y tarifas, gestionar limpieza/mantenimiento, y operar reservaciones y huéspedes. |
| **Recepcionista** | `recepcion@penglai.com` | `recepcion123` | Operación diaria: consultar disponibilidad, registrar huéspedes, crear/cancelar reservaciones, realizar *check-in*/*check-out* y cambiar estado de limpieza. |

---

## Proceso de Negocio y Módulos

1. **Panel de Inicio:** Indicadores operativos en tiempo real (ocupación actual, habitaciones disponibles y llegadas programadas para hoy).
2. **Disponibilidad y Ocupación:** Mapa visual por pisos/estados y buscador por rango de fechas, tipo de habitación y capacidad, mostrando tarifas en MXN y su conversión informativa en vivo (USD/EUR) mediante **Frankfurter API**.
3. **Reservaciones (Ciclo de vida completo):**
   * **Registrar reservación:** Estado `confirmada` $\rightarrow$ Habitación `reservada` (genera folio único, ej. `R-1006`).
   * **Check-in:** Estado `en casa` $\rightarrow$ Habitación `ocupada`.
   * **Check-out:** Estado `finalizada` $\rightarrow$ Habitación `limpieza`.
   * **Fin de limpieza:** Libera la habitación a `disponible`.
   * **Cancelación:** Estado `cancelada` $\rightarrow$ Libera la habitación a `disponible`.
4. **Directorio de Huéspedes:** Registro con validación de documento de identidad único, historial de estancias y situación actual.
5. **Catálogo de Habitaciones:** Administración de inventario (Estándar, Doble, Suite), precios por noche y estados operativos. Incluye un selector "Ver tarifas en" para ver las tarifas convertidas a otra moneda.

---

## API Externa: Conversión de Tarifas (Frankfurter)

En **Habitaciones**, el selector "Ver tarifas en" muestra las tarifas convertidas a otra moneda usando la API pública de Frankfurter (tipo de cambio del Banco Central Europeo). La conversión es solo informativa: el cobro siempre se hace en MXN.

* **Monedas:** dólar estadounidense (USD), euro (EUR), dólar canadiense (CAD), libra esterlina (GBP), yen japonés (JPY) y yuan chino (CNY).
* **Caché:** la respuesta se guarda 6 horas para no consultar la API en cada página.
* **Respaldo:** si no hay internet o la API falla, se usan tasas de referencia y se indica en pantalla.
* **Código:** `app/services/CurrencyService.php`, `app/Http/Controllers/HabitacionController.php` y `resources/views/habitaciones/index.blade.php`.

**Petición que hace el sistema:**
```bash
GET https://api.frankfurter.dev/v1/latest?from=MXN&to=USD,EUR,CAD,GBP,JPY,CNY
```

---

## Pruebas Unitarias y de Integración

El proyecto incluye **5 pruebas automatizadas** en PHPUnit que se ejecutan sobre una base de datos SQLite en memoria (sin alterar datos reales):

```bash
php artisan test
# o con formato detallado:
vendor/bin/phpunit --testdox
```

1. `Tests\Unit\CalculoTotalTest`: Verifica que el total sea `precio * noches` y que siempre se cobre al menos 1 noche.
2. `Tests\Feature\DisponibilidadTest`: Garantiza que una habitación con reservación activa no aparezca disponible en fechas traslapadas.
3. `Tests\Feature\RolesTest`: Valida que un recepcionista reciba `403 Forbidden` al intentar crear habitaciones, mientras que el administrador sí tiene acceso.

---

## Optimización y Medición de Rendimiento

Se implementó el comando artesanal `php artisan medir:rendimiento` para evaluar el sistema bajo estrés con **5,006 reservaciones** y **1,005 huéspedes** en una base de datos aislada (`hotel_manager_rendimiento`):

```bash
DB_DATABASE=hotel_manager_rendimiento php artisan medir:rendimiento
```

### Mejoras aplicadas:
* **Paginación en servidor** (25 registros por página en Reservaciones y Huéspedes).
* **Filtrado SQL (`WHERE` y `GROUP BY`)** en lugar de procesar colecciones completas en memoria con PHP.
* **Índices compuestos en MySQL** sobre `reservaciones.estado` y `(habitacion_id, estado, fecha_entrada, fecha_salida)`.

### Comparativa de resultados (Promedio de 5 ejecuciones):

| Pantalla | Tiempo Antes | Tiempo Después | Tamaño HTML Antes | Tamaño HTML Después |
| :--- | :---: | :---: | :---: | :---: |
| **Reservaciones** | `315.5 ms` | **`83.0 ms`** | `6,012 KB` | **`222 KB`** |
| **Huéspedes** | `119.9 ms` | **`6.3 ms`** | `915 KB` | **`42 KB`** |
| **Disponibilidad** | `12.7 ms` | **`7.1 ms`** | `29 KB` | `29 KB` |
| **Inicio** | `9.0 ms` | **`6.4 ms`** | `7 KB` | `10 KB` |