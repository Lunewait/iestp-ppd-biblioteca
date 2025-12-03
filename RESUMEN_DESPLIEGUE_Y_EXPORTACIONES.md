# Resumen: Configuración de Despliegue y Exportaciones

## ✅ Lo que hemos completado

### 1. Configuración para Render.com (Despliegue Gratuito)
- ✅ Creado `Dockerfile` con configuración para PHP 8.2 + Apache
- ✅ Migraciones automáticas al desplegar (`migrate:fresh --seed`)
- ✅ Compatibilidad dual MySQL/PostgreSQL en migraciones
- ✅ Correos institucionales cambiados a `@iestp.edu.pe`

### 2. Sistema de Exportación a Excel
- ✅ Creadas clases de exportación:
  - `app/Exports/MaterialsExport.php`
  - `app/Exports/LoansExport.php`
- ✅ Agregados métodos `export()` en controladores
- ✅ Rutas configuradas:
  - `GET /materials/export` → Descarga Excel de materiales
  - `GET /loans/export` → Descarga Excel de préstamos
- ✅ Permisos creados:
  - `export_materials` (solo Admin)
  - `export_loans` (solo Admin)

## 📋 Pasos Pendientes

### Paso 1: Actualizar Base de Datos Local
Ejecuta en tu terminal:
```powershell
php artisan migrate:fresh --seed
```

### Paso 2: Agregar Botones de Exportación en las Vistas

#### En `resources/views/materials/index.blade.php`
Busca la sección donde están los botones (probablemente cerca del título "Materiales") y agrega:

```blade
@can('export_materials')
    <a href="{{ route('materials.export') }}" 
       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Exportar a Excel
    </a>
@endcan
```

#### En `resources/views/loans/index.blade.php`
Agrega el mismo botón pero cambiando la ruta:

```blade
@can('export_loans')
    <a href="{{ route('loans.export') }}" 
       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Exportar a Excel
    </a>
@endcan
```

### Paso 3: Desplegar en Render
1. Ve a [dashboard.render.com](https://dashboard.render.com)
2. Entra a tu servicio `iestp-ppd-biblioteca`
3. Haz clic en **Manual Deploy** → **Deploy latest commit**
4. Espera 3-5 minutos

### Paso 4: Probar
**Localmente:**
- Inicia sesión como Admin: `admin@iestp.edu.pe` / `password`
- Ve a Materiales o Préstamos
- Haz clic en "Exportar a Excel"

**En Render:**
- Igual que localmente

## 📊 Contenido de los Excel

### Materiales (materiales_YYYY-MM-DD.xlsx)
- ID, Título, Autor, Editorial, Año, ISBN, Tipo, Categoría
- Copias Totales, Copias Disponibles, URL (Digital), Fecha Creación

### Préstamos (prestamos_YYYY-MM-DD.xlsx)
- ID, Usuario, Email, Material, Fecha Préstamo
- Fecha Devolución Esperada, Fecha Devolución Real
- Estado, Días de Retraso, Multa Calculada

## 🔐 Credenciales de Acceso

| Rol | Email | Contraseña |
|-----|-------|------------|
| Admin | `admin@iestp.edu.pe` | `password` |
| Trabajador | `trabajador@iestp.edu.pe` | `password` |
| Estudiante | `estudiante@iestp.edu.pe` | `password` |
| Jefe de Área | `jefe@iestp.edu.pe` | `password` |

## 🚀 URL de Render
Tu aplicación estará en: `https://iestp-ppd-biblioteca.onrender.com`

---
**Nota**: El plan gratuito de Render "duerme" la aplicación después de 15 minutos de inactividad. El primer acceso tardará ~30 segundos en cargar.
