<p align="center">
  <img src="logo.png" alt="Sistema de Oficios" width="200px">
</p>

# Sistema de Gestión de Oficios y Correspondencia

[![Laravel](https://img.shields.io/badge/Laravel-8.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)
[![JS](https://img.shields.io/badge/JavaScript-ES6%2B-F7DF1E?style=for-the-badge&logo=javascript)](https://developer.mozilla.org/es/docs/Web/JavaScript)
[![License](https://img.shields.io/badge/License-MIT-blue.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)

El **Sistema de Gestión de Oficios y Correspondencia** es una plataforma moderna construida sobre **Laravel 8** diseñada para optimizar, controlar y dar seguimiento al flujo de documentos oficiales, oficios y correspondencia (tanto interna como externa) dentro de una organización. Permite la delegación de responsabilidades a través de "turnos", el control de comisiones de personal, la difusión de avisos oficiales y la atención de solicitudes mediante un módulo integrado de soporte técnico.

---

## 🚀 Características Principales

El sistema está dividido en módulos clave diseñados para cubrir las necesidades operativas y administrativas de correspondencia:

*   **✉️ Gestión de Correspondencia y Oficios**:
    *   Recepción y registro detallado de correspondencia externa y folios.
    *   Generación de oficios oficiales listos para su impresión y firma.
    *   **Módulo de Oficios Internos**: Control y archivo de la correspondencia emitida de manera interna.
*   **🔄 Bandeja de Gestión y Seguimiento de Turnos (Delegación)**:
    *   Asignación de turnos a áreas y subáreas específicas.
    *   Acuse de recibido digital: Confirmación de recepción y notificación del operativo.
    *   Solventación y atención: Formulario para registrar las respuestas y soluciones a cada turno.
    *   Seguimiento en tiempo real del estado de cada turno.
*   **📊 Reportes Dinámicos**:
    *   Reporte diario de turnos generados.
    *   Reporte diario de entradas/recepción.
    *   Reporte detallado de folios internos.
*   **💼 Módulo de Órdenes de Comisión**:
    *   Creación y seguimiento de órdenes de comisiones para el personal.
    *   Validación e integración con Recursos Humanos con acuses de recibido.
*   **📢 Avisos y Circulares**:
    *   Publicación de circulares generales y avisos oficiales.
    *   Control de avisos pendientes y seguimiento de lectura de los usuarios.
*   **🎫 Tickets de Soporte**:
    *   Mesa de ayuda integrada para resolver dudas técnicas del sistema.
*   **🔔 Notificaciones Web Push**:
    *   Notificaciones en tiempo real directo al navegador mediante el protocolo WebPush.

---

## 🛠️ Stack Tecnológico

El sistema utiliza tecnologías estables y de alto rendimiento:

*   **Backend**: [Laravel 8](https://laravel.com/) (compatible con PHP 7.3 y PHP 8.0+)
*   **Base de Datos**: [MySQL](https://www.mysql.com/) / MariaDB
*   **Frontend**: [TailwindCSS 3](https://tailwindcss.com/), [Alpine.js 3](https://alpinejs.dev/), Laravel Mix (Webpack)
*   **Notificaciones**: [Laravel Notification Channels - WebPush](https://github.com/laravel-notification-channels/webpush)
*   **Autenticación**: [Laravel Breeze](https://laravel.com/docs/8.x/breeze) (con Tailwind CSS)

---

## 📋 Requisitos del Sistema

Antes de iniciar la instalación, asegúrate de contar con lo siguiente:

*   **PHP** >= 7.3 (Recomendado 8.0 o superior)
*   **Composer** (Gestor de dependencias de PHP)
*   **Node.js** (v14 o superior) & **NPM**
*   **MySQL** >= 5.7 o MariaDB equivalente

---

## ⚙️ Instalación y Configuración

Sigue estos pasos para levantar el entorno de desarrollo localmente:

### 1. Clonar el repositorio
```bash
git clone <URL_DEL_REPOSITORIO> sistema-oficios
cd sistema-oficios
```

### 2. Instalar dependencias
Instala los paquetes de PHP y JavaScript necesarios:
```bash
composer install
npm install
```

### 3. Configurar variables de entorno
Copia el archivo `.env.example` a `.env` (si no existe) y configura tu base de datos y la URL del sistema:
```bash
cp .env.example .env
```
Abre el archivo `.env` y define tus credenciales:
```env
APP_NAME="Sistema de Oficios"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=oficio
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 4. Generar la clave de la aplicación
```bash
php artisan key:generate
```

### 5. Generar llaves VAPID (Web Push Notifications)
Para habilitar las notificaciones push en tiempo real, genera las claves VAPID y agrégalas a tu archivo `.env`:
```bash
php artisan webpush:vapid
```
Esto generará los campos `VAPID_PUBLIC_KEY` y `VAPID_PRIVATE_KEY` en tu `.env`.

### 6. Ejecutar migraciones y seeders
Crea la estructura de la base de datos y carga los datos de prueba (roles, áreas, y usuarios iniciales):
```bash
php artisan migrate --seed
```

### 7. Compilar recursos
Compila los estilos y scripts frontend con Laravel Mix:
```bash
# Para compilar una vez
npm run dev

# Para compilar en producción
npm run prod

# Para desarrollo activo (observador de cambios)
npm run watch
```

### 8. Iniciar el servidor local
```bash
php artisan serve
```
El sistema estará disponible en [http://localhost:8000](http://localhost:8000).

---

## 🔑 Usuarios de Prueba

El sistema cuenta con seeders que generan los siguientes usuarios iniciales para pruebas (la contraseña predeterminada para todos es `password`):

| Nombre | Correo Electrónico | Rol | Permisos |
| :--- | :--- | :--- | :--- |
| **Admin General** | `admin@sistema.com` | `admin` | Acceso total y administración de usuarios |
| **Jefe de Jurídico** | `jefe.juridico@sistema.com` | `jefe_area` | Turnar y delegar correspondencia de su área |
| **Analista Jurídico** | `analista.juridico@sistema.com` | `user` | Solventar y dar seguimiento a sus turnos |

---

## 📂 Estructura Clave del Proyecto

*   **`app/Http/Controllers/`**: Controladores principales (`OficioController`, `UserController`, `TicketController`, `ComisionController`, `AvisoController`).
*   **`app/Models/`**: Modelos Eloquent para la base de datos.
*   **`routes/web.php`**: Definición de rutas del sistema (protegidas por el middleware `auth`).
*   **`database/seeders/`**: Seeders para inicializar áreas, subáreas y usuarios de prueba.
*   **`resources/views/`**: Vistas construidas con Blade y estilos TailwindCSS.

---

## 📝 Licencia

Este proyecto está bajo la licencia **MIT**. Consulta el archivo `LICENSE` para más información.