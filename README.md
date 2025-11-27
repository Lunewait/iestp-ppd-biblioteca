# 📚 IESTP Library - Sistema de Gestión de Biblioteca

Sistema híbrido de gestión de biblioteca desarrollado con Laravel 11, que incluye gestión de préstamos, multas, repositorio digital y control de usuarios.

## 🚀 Características

- ✅ **Gestión de Materiales**: Catálogo completo de libros y recursos
- ✅ **Sistema de Préstamos**: Solicitud, aprobación y devolución de materiales
- ✅ **Control de Multas**: Cálculo automático y gestión de pagos
- ✅ **Repositorio Digital**: Almacenamiento de tesis e investigaciones
- ✅ **Gestión de Usuarios**: Roles y permisos (Admin, Trabajador, Jefe de Área, Estudiante)
- ✅ **Importación de Usuarios**: Carga masiva desde Excel/CSV
- ✅ **Panel Administrativo**: Interfaz moderna con sidebar vertical

## 📋 Requisitos del Sistema

- PHP >= 8.2
- Composer
- MySQL >= 8.0 o MariaDB
- Node.js >= 18.x y NPM
- Git

## 🔧 Instalación en un Nuevo Sistema

### 1️⃣ Clonar el Repositorio

```bash
# Clonar desde GitHub
git clone https://github.com/TU_USUARIO/iestp-library.git

# Entrar al directorio del proyecto
cd iestp-library
```

### 2️⃣ Instalar Dependencias de PHP

```bash
# Instalar dependencias de Composer
composer install
```

### 3️⃣ Configurar Variables de Entorno

```bash
# Copiar el archivo de ejemplo
copy .env.example .env

# Generar la clave de aplicación
php artisan key:generate
```

### 4️⃣ Configurar Base de Datos

Edita el archivo `.env` y configura tu base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=iestp_library
DB_USERNAME=root
DB_PASSWORD=tu_contraseña
```

### 5️⃣ Crear la Base de Datos

```bash
# Crear la base de datos (si usas MySQL desde línea de comandos)
mysql -u root -p
CREATE DATABASE iestp_library CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 6️⃣ Ejecutar Migraciones y Seeders

```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders (crea roles, permisos y usuarios de prueba)
php artisan db:seed
```

### 7️⃣ Crear Enlace Simbólico para Storage

```bash
php artisan storage:link
```

### 8️⃣ Instalar Dependencias de Node.js (Opcional)

```bash
# Instalar dependencias
npm install

# Compilar assets (si es necesario)
npm run build
```

### 9️⃣ Iniciar el Servidor de Desarrollo

```bash
php artisan serve
```

El sistema estará disponible en: `http://127.0.0.1:8000`

## 👤 Usuarios de Prueba

Después de ejecutar los seeders, puedes acceder con:

| Rol | Email | Contraseña |
|-----|-------|------------|
| **Administrador** | admin@iestp.local | password |
| **Trabajador** | trabajador@iestp.local | password |
| **Jefe de Área** | jefe@iestp.local | password |
| **Estudiante** | estudiante@iestp.local | password |

## 📤 Subir el Proyecto a GitHub (Primera Vez)

### 1️⃣ Inicializar Git (si no está inicializado)

```bash
git init
```

### 2️⃣ Agregar Archivos al Staging

```bash
# Agregar todos los archivos
git add .
```

### 3️⃣ Crear el Primer Commit

```bash
git commit -m "Initial commit: IESTP Library System"
```

### 4️⃣ Crear Repositorio en GitHub

1. Ve a [GitHub](https://github.com)
2. Haz clic en **"New repository"**
3. Nombra tu repositorio: `iestp-library`
4. **NO** marques "Initialize with README"
5. Haz clic en **"Create repository"**

### 5️⃣ Conectar con GitHub y Subir

```bash
# Agregar el repositorio remoto
git remote add origin https://github.com/TU_USUARIO/iestp-library.git

# Cambiar a la rama main
git branch -M main

# Subir el código
git push -u origin main
```

## 🔄 Comandos Git para Desarrollo Diario

### Guardar Cambios

```bash
# Ver archivos modificados
git status

# Agregar archivos específicos
git add archivo1.php archivo2.blade.php

# O agregar todos los cambios
git add .

# Crear commit con mensaje descriptivo
git commit -m "Descripción de los cambios realizados"

# Subir cambios a GitHub
git push
```

### Descargar Cambios en Otro Sistema

```bash
# Descargar últimos cambios
git pull
```

### Trabajar en Diferentes Computadoras

**En la computadora 1:**
```bash
git add .
git commit -m "Cambios realizados en PC 1"
git push
```

**En la computadora 2:**
```bash
# Descargar cambios
git pull

# Instalar dependencias (si hay nuevas)
composer install
npm install

# Ejecutar migraciones (si hay nuevas)
php artisan migrate
```

## 📁 Archivos que NO se Suben a GitHub

El archivo `.gitignore` ya está configurado para excluir:

- `/vendor/` - Dependencias de Composer
- `/node_modules/` - Dependencias de Node.js
- `.env` - Configuración local (contraseñas, etc.)
- `/storage/` - Archivos temporales y logs
- `/public/hot` - Archivos de desarrollo

## 🛠️ Comandos Útiles de Laravel

```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Refrescar base de datos (CUIDADO: borra todos los datos)
php artisan migrate:fresh --seed

# Ver rutas disponibles
php artisan route:list

# Crear nuevo controlador
php artisan make:controller NombreController

# Crear nuevo modelo con migración
php artisan make:model NombreModelo -m
```

## 🔐 Permisos y Roles

El sistema utiliza Spatie Laravel Permission con los siguientes roles:

- **Admin**: Acceso total al sistema
- **Trabajador**: Gestión de préstamos y materiales
- **Jefe_Area**: Aprobación de documentos del repositorio
- **Estudiante**: Solicitud de préstamos y acceso al repositorio

## 📞 Soporte

Para problemas o preguntas, contacta al administrador del sistema.

## 📝 Licencia

Este proyecto es de uso interno del IESTP.

---

**Desarrollado con ❤️ para IESTP**
