# 🚀 Guía Completa: Desplegar Laravel en Render

## 🔗 URL Oficial
**https://render.com**

---

## 🎁 ¿Por qué Render?

### ✅ Ventajas

- 🆓 **100% GRATIS** para empezar
- ⚡ **Muy fácil de usar** - Deploy en minutos
- 🔄 **Deploy automático** desde GitHub
- 🔒 **SSL gratis** automático
- 🌍 **CDN global** incluido
- 📊 **Logs en tiempo real**
- 💾 **Base de datos PostgreSQL gratis** (90 días)
- 🎯 **Perfecto para Laravel**

### ⚠️ Limitaciones del Plan Gratuito

- 🐌 **Se duerme después de 15 minutos** de inactividad
- ⏱️ **Tarda ~30 segundos** en despertar
- 💾 **Base de datos expira** después de 90 días (pero puedes crear otra)
- 🔄 **750 horas/mes** de uso (suficiente para 1 servicio 24/7)

**Ideal para:** Proyectos de demostración, portafolio, pruebas

---

## 📋 Tabla de Contenidos

1. [Preparar tu Proyecto](#paso-1-preparar-tu-proyecto)
2. [Crear Cuenta en Render](#paso-2-crear-cuenta-en-render)
3. [Crear Base de Datos MySQL](#paso-3-crear-base-de-datos-mysql)
4. [Crear Web Service](#paso-4-crear-web-service)
5. [Configurar Variables de Entorno](#paso-5-configurar-variables-de-entorno)
6. [Primer Deploy](#paso-6-primer-deploy)
7. [Ejecutar Migraciones](#paso-7-ejecutar-migraciones)
8. [Verificar que Funciona](#paso-8-verificar-que-funciona)
9. [Configurar Dominio Personalizado](#paso-9-configurar-dominio-personalizado-opcional)
10. [Mantenimiento](#paso-10-mantenimiento)

---

## 📦 Paso 1: Preparar tu Proyecto

Antes de desplegar en Render, necesitas agregar algunos archivos a tu proyecto.

### 1️⃣ Verificar que tu proyecto esté en GitHub

```powershell
# Verificar estado de Git
git status

# Si hay cambios, hacer commit
git add .
git commit -m "Preparando para deployment en Render"
git push
```

### 2️⃣ Crear archivo `render-build.sh`

Este script se ejecutará cada vez que despliegues.

**Ya lo he creado para ti** - Revisa el archivo `render-build.sh` en la raíz de tu proyecto.

### 3️⃣ Crear archivo `render.yaml` (Opcional)

**Ya lo he creado para ti** - Revisa el archivo `render.yaml` en la raíz de tu proyecto.

### 4️⃣ Verificar archivo `.env.example`

Asegúrate de que `.env.example` tenga todas las variables necesarias.

### 5️⃣ Subir cambios a GitHub

```powershell
# Agregar nuevos archivos
git add .

# Hacer commit
git commit -m "Agregar archivos de configuración para Render"

# Subir a GitHub
git push
```

---

## 👤 Paso 2: Crear Cuenta en Render

### 1️⃣ Ir a Render

Abre tu navegador y ve a:
```
https://render.com
```

### 2️⃣ Registrarse

Haz clic en **"Get Started"** o **"Sign Up"**

**Opciones de registro:**
- ✅ **GitHub** (Recomendado - más fácil)
- Google
- GitLab
- Email

### 3️⃣ Autorizar GitHub

Si usas GitHub:
1. Haz clic en **"Sign up with GitHub"**
2. Autoriza Render para acceder a tus repositorios
3. Puedes dar acceso a todos los repos o solo a `iestp-library`

---

## 🗄️ Paso 3: Crear Base de Datos MySQL

Render ofrece PostgreSQL gratis, pero Laravel funciona mejor con MySQL para tu proyecto.

### Opción 1: PostgreSQL Gratis (Recomendado para empezar)

1. En el Dashboard de Render, haz clic en **"New +"**
2. Selecciona **"PostgreSQL"**
3. Configura:
   - **Name:** `iestp-library-db`
   - **Database:** `iestp_library`
   - **User:** `iestp_user` (se genera automáticamente)
   - **Region:** Oregon (US West) - Más cercano a Perú
   - **Plan:** **Free** ✅

4. Haz clic en **"Create Database"**

⏱️ La base de datos estará lista en 1-2 minutos.

### Opción 2: MySQL Externo (Gratis)

Puedes usar servicios gratuitos de MySQL:

#### **FreeSQLDatabase.com**
```
https://www.freesqldatabase.com/
```
- 5MB gratis
- MySQL 5.5

#### **db4free.net**
```
https://www.db4free.net/
```
- MySQL 8.0
- Sin límite de tiempo

#### **PlanetScale** (Recomendado)
```
https://planetscale.com/
```
- 5GB gratis
- MySQL compatible
- Muy rápido

---

## 🌐 Paso 4: Crear Web Service

### 1️⃣ Crear Nuevo Web Service

1. En el Dashboard, haz clic en **"New +"**
2. Selecciona **"Web Service"**

### 2️⃣ Conectar Repositorio

1. Selecciona **"Build and deploy from a Git repository"**
2. Haz clic en **"Next"**
3. Busca y selecciona tu repositorio `iestp-library`
4. Haz clic en **"Connect"**

### 3️⃣ Configurar el Servicio

#### **Información Básica:**
- **Name:** `iestp-library` (será parte de tu URL)
- **Region:** Oregon (US West)
- **Branch:** `main`
- **Root Directory:** (dejar vacío)

#### **Build & Deploy:**
- **Runtime:** `Docker` o `Native Environment`

**Si eliges Native Environment:**
- **Build Command:**
  ```bash
  ./render-build.sh
  ```

- **Start Command:**
  ```bash
  php artisan serve --host=0.0.0.0 --port=$PORT
  ```

#### **Plan:**
- Selecciona **"Free"** ✅

### 4️⃣ Configuración Avanzada (Expandir)

Haz clic en **"Advanced"** y configura:

- **Auto-Deploy:** ✅ Yes (Deploy automático cuando hagas push a GitHub)

---

## ⚙️ Paso 5: Configurar Variables de Entorno

Antes de crear el servicio, necesitas configurar las variables de entorno.

### 1️⃣ Agregar Variables de Entorno

En la sección **"Environment Variables"**, haz clic en **"Add Environment Variable"** y agrega:

#### **Variables Básicas:**

```env
APP_NAME=IESTP Library
APP_ENV=production
APP_DEBUG=false
APP_URL=https://iestp-library.onrender.com
```

#### **Generar APP_KEY:**

En tu computadora local, ejecuta:
```powershell
php artisan key:generate --show
```

Copia el resultado y agrégalo:
```env
APP_KEY=base64:TU_KEY_GENERADA_AQUI
```

#### **Base de Datos (PostgreSQL de Render):**

Si usas PostgreSQL de Render, Render provee estas variables automáticamente:

```env
DB_CONNECTION=pgsql
DB_HOST=${DATABASE_HOST}
DB_PORT=${DATABASE_PORT}
DB_DATABASE=${DATABASE_NAME}
DB_USERNAME=${DATABASE_USERNAME}
DB_PASSWORD=${DATABASE_PASSWORD}
```

**Nota:** Las variables `${DATABASE_*}` se llenan automáticamente cuando conectas la base de datos.

#### **Base de Datos (MySQL Externa):**

Si usas MySQL externo:
```env
DB_CONNECTION=mysql
DB_HOST=tu-host-mysql.com
DB_PORT=3306
DB_DATABASE=tu_database
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

#### **Otras Variables:**

```env
LOG_CHANNEL=stack
LOG_LEVEL=error

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=public
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=biblioteca@iestp.edu.pe
MAIL_FROM_NAME="${APP_NAME}"
```

### 2️⃣ Conectar Base de Datos

Si usas PostgreSQL de Render:

1. Baja hasta **"Environment Variables"**
2. Haz clic en **"Add from Database"**
3. Selecciona tu base de datos `iestp-library-db`
4. Render agregará automáticamente las variables de conexión

---

## 🚀 Paso 6: Primer Deploy

### 1️⃣ Crear el Servicio

Haz clic en **"Create Web Service"**

### 2️⃣ Esperar el Deploy

Render comenzará a:
1. ✅ Clonar tu repositorio
2. ✅ Instalar dependencias (`composer install`)
3. ✅ Ejecutar el build script
4. ✅ Iniciar el servidor

⏱️ El primer deploy toma 5-10 minutos.

### 3️⃣ Ver Logs en Tiempo Real

Puedes ver el progreso en la pestaña **"Logs"**

Verás algo como:
```
==> Cloning from https://github.com/tu-usuario/iestp-library...
==> Running build command './render-build.sh'...
==> Installing dependencies...
==> Build successful!
==> Starting service...
==> Your service is live 🎉
```

---

## 🗃️ Paso 7: Ejecutar Migraciones

Una vez que el servicio esté corriendo, necesitas ejecutar las migraciones.

### 1️⃣ Abrir Shell

1. En tu servicio, ve a la pestaña **"Shell"**
2. Haz clic en **"Launch Shell"**

### 2️⃣ Ejecutar Migraciones

En la shell, ejecuta:

```bash
# Ejecutar migraciones
php artisan migrate --force

# Ejecutar seeders (crear usuarios de prueba)
php artisan db:seed --force

# Crear enlace simbólico para storage
php artisan storage:link
```

### 3️⃣ Verificar

```bash
# Ver tablas creadas
php artisan migrate:status
```

---

## 🎉 Paso 8: Verificar que Funciona

### 1️⃣ Obtener tu URL

Tu aplicación estará disponible en:
```
https://iestp-library.onrender.com
```

(Reemplaza `iestp-library` con el nombre que elegiste)

### 2️⃣ Abrir en el Navegador

Haz clic en el enlace en el Dashboard de Render o copia la URL.

### 3️⃣ Iniciar Sesión

Usa las credenciales por defecto:
- **Email:** `admin@iestp.local`
- **Password:** `password`

### 4️⃣ Verificar Funcionalidad

Prueba:
- ✅ Login
- ✅ Ver materiales
- ✅ Crear préstamo
- ✅ Subir archivos

---

## 🌍 Paso 9: Configurar Dominio Personalizado (Opcional)

Si tienes un dominio (ej: `biblioteca.miescuela.edu.pe`):

### 1️⃣ En Render

1. Ve a tu servicio
2. Pestaña **"Settings"**
3. Sección **"Custom Domains"**
4. Haz clic en **"Add Custom Domain"**
5. Ingresa: `biblioteca.miescuela.edu.pe`

### 2️⃣ Configurar DNS

Render te dará instrucciones para configurar DNS:

**Opción A: CNAME (Recomendado)**
```
Tipo: CNAME
Nombre: biblioteca
Valor: iestp-library.onrender.com
```

**Opción B: A Record**
```
Tipo: A
Nombre: biblioteca
Valor: [IP que Render te proporcione]
```

### 3️⃣ SSL Automático

Render configurará SSL automáticamente (puede tomar 5-10 minutos).

### 4️⃣ Actualizar .env

Actualiza la variable `APP_URL`:
```env
APP_URL=https://biblioteca.miescuela.edu.pe
```

---

## 🔄 Paso 10: Mantenimiento

### Actualizar tu Aplicación

Render hace deploy automático cuando haces push a GitHub:

```powershell
# Hacer cambios en tu código
# ...

# Commit y push
git add .
git commit -m "Descripción de cambios"
git push

# Render detectará el push y desplegará automáticamente
```

### Ver Logs

1. Ve a tu servicio en Render
2. Pestaña **"Logs"**
3. Ver logs en tiempo real

### Ejecutar Comandos

1. Pestaña **"Shell"**
2. **"Launch Shell"**
3. Ejecutar comandos:

```bash
# Limpiar cachés
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Ver logs
tail -f storage/logs/laravel.log

# Ejecutar migraciones
php artisan migrate --force
```

### Reiniciar Servicio

1. Pestaña **"Manual Deploy"**
2. Haz clic en **"Clear build cache & deploy"**

### Backups de Base de Datos

⚠️ **Importante:** La base de datos gratuita de Render expira después de 90 días.

**Hacer backup manual:**

1. Abre Shell en tu servicio
2. Ejecuta:

```bash
# Backup de PostgreSQL
pg_dump $DATABASE_URL > backup.sql

# Ver el backup
cat backup.sql
```

**Copiar a tu computadora:**

Render no permite descargar archivos directamente, pero puedes:

1. Usar un servicio de almacenamiento temporal
2. O configurar backups automáticos a S3/Dropbox

---

## 🔧 Troubleshooting

### Error: "Application Error"

**Ver logs:**
1. Ve a **Logs**
2. Busca errores en rojo

**Causas comunes:**
- ❌ `APP_KEY` no configurado
- ❌ Variables de base de datos incorrectas
- ❌ Migraciones no ejecutadas

**Solución:**
```bash
# En Shell
php artisan key:generate
php artisan migrate --force
php artisan config:cache
```

### Error: "502 Bad Gateway"

El servicio está iniciando. Espera 30-60 segundos.

### Error: "Database connection failed"

**Verificar:**
1. Variables de entorno de base de datos
2. Que la base de datos esté corriendo
3. Credenciales correctas

### Servicio se duerme

Es normal en el plan gratuito. El servicio se despierta automáticamente cuando alguien lo visita (tarda ~30 segundos).

**Solución:** Upgrade a plan de pago ($7/mes) para mantenerlo siempre activo.

### Archivos subidos se pierden

Render usa almacenamiento efímero. Los archivos se pierden al redesplegar.

**Solución:** Usar almacenamiento externo:
- AWS S3
- Cloudinary
- Backblaze B2

---

## 💰 Planes de Render

| Plan | Precio | Características |
|------|--------|-----------------|
| **Free** | $0 | Se duerme después de 15 min, 750 horas/mes |
| **Starter** | $7/mes | Siempre activo, más recursos |
| **Standard** | $25/mes | Más CPU/RAM, mejor rendimiento |
| **Pro** | $85/mes | Máximo rendimiento |

---

## 📊 Comparación: Render vs Otros

| Característica | Render | Railway | Heroku | Oracle Cloud |
|----------------|--------|---------|--------|--------------|
| **Precio** | Gratis | $5/mes | $7/mes | Gratis |
| **Facilidad** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐ |
| **BD Gratis** | ✅ 90 días | ✅ | ➕ Addon | ✅ |
| **SSL** | ✅ Auto | ✅ Auto | ✅ Auto | ⚙️ Manual |
| **Se duerme** | ✅ Sí | ❌ No | ✅ Sí | ❌ No |

---

## ✅ Checklist Final

- [ ] Proyecto en GitHub
- [ ] Archivos `render-build.sh` y `render.yaml` creados
- [ ] Cuenta de Render creada
- [ ] Base de datos PostgreSQL creada
- [ ] Web Service creado
- [ ] Variables de entorno configuradas
- [ ] Primer deploy exitoso
- [ ] Migraciones ejecutadas
- [ ] Aplicación funcionando
- [ ] SSL activo
- [ ] Dominio personalizado configurado (opcional)

---

## 🎯 Comandos Útiles

```bash
# En Shell de Render

# Ver logs
tail -f storage/logs/laravel.log

# Limpiar cachés
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Ejecutar migraciones
php artisan migrate --force

# Ver estado de migraciones
php artisan migrate:status

# Ejecutar seeders
php artisan db:seed --force

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📚 Recursos Adicionales

- **Documentación Render:** https://render.com/docs
- **Render + Laravel:** https://render.com/docs/deploy-laravel
- **Comunidad:** https://community.render.com/

---

## 🎉 ¡Listo!

Tu aplicación Laravel está corriendo en Render de forma **GRATUITA** con:
- ✅ SSL automático (HTTPS)
- ✅ Deploy automático desde GitHub
- ✅ Base de datos incluida
- ✅ URL pública

**URL de tu aplicación:**
```
https://iestp-library.onrender.com
```

**Credenciales por defecto:**
- Email: `admin@iestp.local`
- Password: `password`

---

**¡Felicidades! 🎊 Tu proyecto está en la nube!**

Si tienes problemas, revisa la sección de Troubleshooting o pregúntame.
