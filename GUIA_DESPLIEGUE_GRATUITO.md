# Guía de Despliegue Gratuito para Laravel (Render.com)

Subir un proyecto Laravel (Backend + Base de Datos) de forma 100% gratuita es posible, pero tiene limitaciones. La mejor opción moderna y gratuita actualmente es **Render.com**.

## Limitaciones del Plan Gratuito de Render
*   **Web Service**: Se "duerme" después de 15 minutos de inactividad. El primer acceso tardará unos 30 segundos en cargar.
*   **Base de Datos**: La base de datos PostgreSQL gratuita expira después de 90 días (tienes que hacer backup y crear una nueva).

---

## Paso 1: Subir tu código a GitHub
Para usar Render, tu código debe estar en GitHub.
1.  Crea un repositorio en [GitHub.com](https://github.com).
2.  Sube tu proyecto:
    ```bash
    git init
    git add .
    git commit -m "Primer commit"
    git branch -M main
    git remote add origin https://github.com/TU_USUARIO/TU_REPO.git
    git push -u origin main
    ```

## Paso 2: Crear la Base de Datos en Render
1.  Ve a [dashboard.render.com](https://dashboard.render.com) y crea una cuenta.
2.  Haz clic en **New +** y selecciona **PostgreSQL**.
3.  **Name**: `biblioteca-db` (o lo que quieras).
4.  **Region**: Elige la más cercana (ej. Ohio o Frankfurt).
5.  **Instance Type**: Selecciona **Free**.
6.  Haz clic en **Create Database**.
7.  **IMPORTANTE**: Cuando se cree, copia la **"Internal Database URL"** (la usaremos luego).

## Paso 3: Crear el Web Service (Tu App Laravel)
1.  En el dashboard, haz clic en **New +** y selecciona **Web Service**.
2.  Conecta tu cuenta de GitHub y selecciona tu repositorio `iestp-ppd-biblioteca`.
3.  **Name**: `biblioteca-app`.
4.  **Region**: La misma que tu base de datos.
5.  **Branch**: `main`.
6.  **Runtime**: `Docker` (Render detectará un Dockerfile si existe, si no, usaremos el entorno nativo).
    *   *Recomendación*: Selecciona **Environment: PHP**.
7.  **Build Command**:
    ```bash
    composer install --no-dev --optimize-autoloader && npm install && npm run build
    ```
8.  **Start Command**:
    ```bash
    php artisan migrate --force && heroku-php-apache2 public/
    ```
9.  **Instance Type**: Selecciona **Free**.

## Paso 4: Configurar Variables de Entorno
En la pantalla de creación (o en la pestaña "Environment" después), añade estas variables:

| Key | Value |
| --- | --- |
| `APP_NAME` | Biblioteca PPD |
| `APP_ENV` | production |
| `APP_KEY` | (Copia esto de tu archivo `.env` local) |
| `APP_DEBUG` | false |
| `APP_URL` | (La URL que Render te dará, ej: `https://tu-app.onrender.com`) |
| `DATABASE_URL` | (Pega la **Internal Database URL** que copiaste en el Paso 2) |

## Paso 5: Archivo de Configuración Docker (Opcional pero recomendado)
Si Render no detecta bien PHP, puedes crear un archivo `Dockerfile` en la raíz de tu proyecto. Pero generalmente el entorno "PHP" de Render funciona bien si tienes `composer.json`.

## Paso 6: Finalizar
Haz clic en **Create Web Service**. Render empezará a descargar las dependencias y construir tu app. Esto puede tardar unos minutos.

---

## Alternativa: InfinityFree (Hosting Compartido Clásico)
Si prefieres un hosting tipo "cPanel" clásico (subir archivos por FTP):
1.  Regístrate en InfinityFree.
2.  Crea una base de datos MySQL en su panel.
3.  Sube tus archivos usando FileZilla a la carpeta `htdocs`.
    *   **OJO**: Laravel requiere que la carpeta pública sea la raíz. En hostings compartidos esto es difícil de configurar. Tendrás que subir todo el contenido de `public` a `htdocs` y el resto de carpetas a un nivel superior, y modificar `index.php` para apuntar correctamente.
    *   *No recomendado para principiantes*.

## Resumen
Te recomiendo **Render.com** porque es más profesional, soporta HTTPS automático y se integra con Git, lo que facilita las actualizaciones.
