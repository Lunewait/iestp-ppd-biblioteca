# 🆘 TROUBLESHOOTING - IESTP Library Platform

## 📍 Índice Rápido
1. [Errores Comunes](#errores-comunes)
2. [Problemas de Base de Datos](#problemas-de-base-de-datos)
3. [Problemas de Autenticación](#problemas-de-autenticación)
4. [Problemas de Servidor](#problemas-de-servidor)
5. [Problemas de Componentes](#problemas-de-componentes)
6. [Solución de Errores 500](#solución-de-errores-500)
7. [Solución de Errores 403](#solución-de-errores-403)

---

## 🔴 Errores Comunes

### Error: "SQLSTATE[42S22]: Unknown column"

**Síntomas:**
- Error SQL al acceder a ciertas páginas
- Mensaje: "Unknown column 'xyz' in 'where clause'"

**Causas Comunes:**
1. Nombre de columna incorrecto en consulta
2. Nombre de tabla incorrecto
3. Base de datos no sincronizada

**Solución:**

```bash
# 1. Verificar que todas las migraciones están aplicadas
php artisan migrate:status

# 2. Si faltan migraciones, correr:
php artisan migrate

# 3. Si tienes problemas, resetear todo:
php artisan migrate:fresh --seed
```

**Columnas Correctas Documentadas:**

| Tabla | Columnas Correctas |
|-------|-------------------|
| materials | title, author, type, code, description, keywords |
| material_fisicos | stock, available, isbn, publisher, publication_year, location |
| material_digitales | url, file_path, format |
| prestamos | status, fecha_prestamo, fecha_devolucion_esperada, approval_status |
| multas | monto, status (pendiente\|pagada), razon |

---

### Error: "Call to undefined method authorize()"

**Síntomas:**
- Error cuando accedes a ciertas funcionalidades
- Stack trace menciona "authorize()"

**Causa:**
- Controller o Livewire component falta el trait `AuthorizesRequests`

**Solución:**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class YourController extends Controller
{
    use AuthorizesRequests;
    
    // ... resto del código
}
```

**Archivos ya Corregidos:**
- FineController.php ✅
- UserController.php ✅
- MaterialController.php ✅
- LoanController.php ✅
- RepositoryController.php ✅
- ReservationController.php ✅
- LoanApprovalList.php ✅

---

### Error: "403 Forbidden"

**Síntomas:**
- Acceso denegado a ciertas páginas
- Incluso admin no puede acceder

**Causas Comunes:**
1. Usuario no tiene rol asignado
2. Rol no tiene permiso requerido
3. Middleware de autenticación falla
4. Component Livewire falta AuthorizesRequests

**Solución:**

```bash
# 1. Verificar que usuario tiene rol
# En el dashboard, ir a Usuarios y asignar rol

# 2. Verificar permisos del rol
# En la BD, verificar tabla role_has_permissions

# 3. Limpiar caché
php artisan cache:clear

# 4. Resetear BD y seeders
php artisan migrate:fresh --seed
```

**SQL para Verificar:**
```sql
-- Ver roles del usuario
SELECT r.name FROM model_has_roles mr
JOIN roles r ON r.id = mr.role_id
WHERE mr.model_id = 1; -- 1 = user id

-- Ver permisos del rol
SELECT p.name FROM role_has_permissions rp
JOIN permissions p ON p.id = rp.permission_id
WHERE rp.role_id = 1; -- 1 = role id
```

---

### Error: "Trying to access array offset on value of type null"

**Síntomas:**
- Error al acceder a propiedades de relaciones
- Blade template no renderiza datos

**Causa:**
- Relación no cargada o retorna null
- Acceso a propiedad inexistente

**Solución:**

```php
// ❌ Incorrecto
{{ $material->materialFisico->stock }}

// ✅ Correcto (con verificación)
{{ $material->materialFisico?->stock ?? 'N/A' }}

// ✅ O cargar relación en controlador
$material = Material::with('materialFisico')->find($id);
```

---

## 📦 Problemas de Base de Datos

### No hay datos de prueba

**Síntoma:**
- Base de datos vacía
- No hay usuarios para login

**Solución:**

```bash
# 1. Resetear y sembrar BD
php artisan migrate:fresh --seed

# 2. Verificar que se crearon datos
php artisan tinker
>>> User::count() // Debe mostrar 4
>>> Material::count() // Debe mostrar datos
>>> exit()
```

### Tablas faltantes

**Síntoma:**
- Error "Table 'tabla' doesn't exist"

**Solución:**

```bash
# 1. Listar migraciones pendientes
php artisan migrate:status

# 2. Correr migraciones
php artisan migrate

# 3. Si hay conflicto, resetear
php artisan migrate:fresh --seed
```

### Columna no existe en tabla

**Síntoma:**
- "Unknown column 'xyz'"

**Solución:**

Verificar que la columna existe en la migración:

```bash
# 1. Revisar migraciones
ls database/migrations/

# 2. Abrir el archivo relevant y verificar el nombre de columna

# 3. Si falta, crear nueva migración:
php artisan make:migration add_column_to_table

# 4. Correr migración
php artisan migrate
```

---

## 🔐 Problemas de Autenticación

### No puedo hacer login

**Síntoma:**
- Botón de login no funciona
- Vuelve a la página de login sin error

**Solución:**

```bash
# 1. Verificar que BD está seeded
php artisan migrate:fresh --seed

# 2. Verificar credenciales en database/seeders/DatabaseSeeder.php

# 3. Intentar con credenciales correctas:
Email: admin@iestp.local
Password: password

# 4. Limpiar caché de sesiones
php artisan cache:clear
php artisan session:clear
```

### Token expirado

**Síntoma:**
- Error "CSRF token mismatch"
- Error "Expired token"

**Solución:**

```bash
# 1. Limpiar cache
php artisan cache:clear

# 2. Limpiar cookies del navegador:
# Abre DevTools (F12)
# Application → Cookies → Elimina todo

# 3. Recarga la página
```

### No puede crear usuario

**Síntoma:**
- Error al crear usuario en panel de admin
- Validación falla

**Solución:**

1. Verificar que campo de email no esté duplicado:
   ```bash
   php artisan tinker
   >>> User::where('email', 'nuevo@example.com')->exists()
   >>> exit()
   ```

2. Usar email único:
   - usuario1@iestp.local
   - usuario2@iestp.local
   - etc.

3. Asignar rol válido:
   - student
   - worker
   - admin
   - jefe_area

---

## 🖥️ Problemas de Servidor

### Servidor no inicia

**Síntoma:**
- Error al ejecutar `php artisan serve`
- Puerto en uso o error de PHP

**Solución:**

```bash
# 1. Verificar que PHP esté instalado
php --version

# 2. Matar procesos PHP existentes
Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force

# 3. Iniciar servidor en puerto diferente
php artisan serve --host=127.0.0.1 --port=8001

# 4. Si sigue sin funcionar, verificar logs
tail -f storage/logs/laravel.log
```

### Puerto 8000 ya está en uso

**Solución:**

```bash
# 1. Encontrar qué proceso está usando el puerto
Get-Process | Where-Object {$_.ProcessName -eq 'php'}

# 2. Matar el proceso
Stop-Process -Name php -Force

# 3. Iniciar en puerto diferente
php artisan serve --host=127.0.0.1 --port=8001
```

### Acceso desde otra máquina

**Síntoma:**
- No puedo acceder desde otra computadora

**Solución:**

```bash
# En lugar de usar 127.0.0.1 (localhost), usar IP de la máquina
php artisan serve --host=0.0.0.0 --port=8000

# Luego acceder desde otra máquina usando:
http://IP_DE_LA_MAQUINA:8000
```

---

## 🧩 Problemas de Componentes

### Componente Livewire no carga

**Síntoma:**
- Componente no aparece en la página
- Error en la consola

**Solución:**

```bash
# 1. Verificar que componente existe
ls app/Livewire/

# 2. Verificar nombre en view
# Debe ser en formato kebab-case:
@livewire('materials-list')

# 3. Reconstruir componentes
php artisan livewire:discover

# 4. Limpiar cache
php artisan cache:clear
php artisan view:clear
```

### Modal no abre/cierra

**Síntoma:**
- Click en botón no abre modal
- Modal se queda abierto

**Solución:**

1. Verificar JavaScript en consola (F12 → Console)
2. Verificar que Tailwind CSS esté compilado:
   ```bash
   npm run build
   ```

3. Verificar atributos `wire:click`:
   ```blade
   <!-- Correcto -->
   <button wire:click="openModal">Abrir</button>
   
   <!-- Incorrecto -->
   <button onclick="openModal()">Abrir</button>
   ```

### Búsqueda no funciona

**Síntoma:**
- Escribir en búsqueda no filtra resultados
- Componente no responde

**Solución:**

```php
// Verificar que Livewire reactive property existe
public string $search = '';

// Verificar que input tiene wire:model
<input type="text" wire:model.debounce.300ms="search" />

// Verificar que query usa $search
$this->authorize('view', Material::class);
return Material::where('title', 'like', "%{$this->search}%")
    ->paginate(12);
```

### Notificaciones no aparecen

**Síntoma:**
- NotificationToast componente no muestra mensajes
- Eventos no se disparan

**Solución:**

```php
// Verificar que dispatch se usa correctamente
$this->dispatch('notify', 
    message: 'Préstamo aprobado',
    type: 'success'
);

// Verificar que listener está en componente
#[On('notify')]
public function handleNotify($message, $type) {
    // ...
}

// Verificar que componente está renderizado en layout
@livewire('notification-toast')
```

---

## 🔴 Solución de Errores 500

### Error 500 genérico

**Síntoma:**
- Página muestra "500 Server Error"
- No hay detalles del error

**Solución:**

```bash
# 1. Ver logs detallados
tail -f storage/logs/laravel.log

# 2. O en Windows PowerShell
Get-Content storage/logs/laravel.log -Tail 50 -Wait

# 3. Buscar línea con "Exception" o "Error"

# 4. Aplicar solución basada en el error específico
```

### Error: Class not found

**Síntoma:**
```
Class 'App\Models\Material' not found
```

**Solución:**

```bash
# 1. Verificar que archivo existe
ls app/Models/Material.php

# 2. Verificar namespace en archivo
# Debe ser: namespace App\Models;

# 3. Verificar import en controlador
use App\Models\Material;

# 4. Ejecutar autoload
composer dump-autoload
```

---

## 🔐 Solución de Errores 403

### Error 403 después del login

**Síntoma:**
- Login exitoso pero acceso denegado a funcionalidades
- Error 403 en middleware

**Solución:**

```bash
# 1. Verificar que usuario tiene rol
php artisan tinker
>>> $user = User::find(1);
>>> $user->roles;
>>> exit()

# 2. Si no hay rol, asignar:
>>> $user->assignRole('admin');
>>> exit()

# 3. Asegurar que rol tiene permisos:
>>> $user->givePermissionTo('create materials');
>>> exit()
```

### Admin bloqueado de funcionalidades

**Síntoma:**
- Admin no puede crear materiales
- Admin recibe 403

**Solución:**

```bash
# 1. Otorgar todos los permisos a admin
php artisan tinker
>>> $admin = User::find(1);
>>> $admin->givePermissionTo('*'); // Todos los permisos
>>> exit()

# 2. O resetear BD y seeders
php artisan migrate:fresh --seed
```

---

## 🔧 Comandos Útiles

### Limpiar todo el caché
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
```

### Resetear base de datos completamente
```bash
php artisan migrate:fresh --seed
```

### Ver migraciones aplicadas
```bash
php artisan migrate:status
```

### Compilar assets
```bash
npm run build
```

### Ver logs en tiempo real
```bash
tail -f storage/logs/laravel.log
```

### Conectar a la BD
```bash
mysql -u root -p iestp_library
```

---

## 📞 Checklist de Diagnóstico

Cuando algo no funcione, seguir este checklist:

- [ ] ¿El servidor está corriendo? `php artisan serve`
- [ ] ¿Puedo acceder a http://127.0.0.1:8000?
- [ ] ¿Los tests pasan? `php artisan test`
- [ ] ¿La BD tiene datos? `php artisan tinker` → `User::count()`
- [ ] ¿El usuario tiene rol? Ver en Usuarios o tinker
- [ ] ¿El rol tiene permisos? Ver en BD tabla role_has_permissions
- [ ] ¿Los logs muestran errores? `tail -f storage/logs/laravel.log`
- [ ] ¿Cambié código? Hacer `composer dump-autoload`
- [ ] ¿Está todo commiteado? Ver `git status`

---

## 📧 Cuando Contactar Soporte

Si después de intentar todo esto aún tienes problemas, contacta soporte con:

1. **Descripción del problema:**
   - ¿Qué intentaste hacer?
   - ¿Qué pasó?
   - ¿Qué esperabas que pasara?

2. **Información técnica:**
   - Screenshot o error exacto
   - URL donde ocurre el problema
   - Credenciales usadas para login
   - Pasos para reproducir

3. **Salida de comandos:**
   ```bash
   php --version
   php artisan tinker
   >>> User::count()
   >>> Material::count()
   >>> exit()
   ```

4. **Últimas líneas del log:**
   ```bash
   tail -f storage/logs/laravel.log | head -50
   ```

---

**Última Actualización:** 26 Noviembre 2025  
**Versión:** 1.0  
**Status:** ✅ Pronto para Troubleshooting
