# 📚 Documentación del Sistema de Biblioteca IESTP

## 🔧 Problemas Solucionados

### 1. ✅ Navegación Mejorada por Roles

**Problema:** El administrador veía opciones de estudiante como "Solicitar Préstamo" y "Mis Préstamos"

**Solución:**
- **Estudiantes** ahora ven:
  - 📋 Mis Préstamos (sus propios préstamos)
  - 📝 Solicitar Préstamo
  
- **Admin/Trabajadores/Jefe de Área** ahora ven:
  - 📋 Gestionar Préstamos (todos los préstamos del sistema)
  - ✅ Aprobar Préstamos

### 2. ✅ Permisos de Multas Corregidos

**Problema:** Los estudiantes no podían ver sus multas (error 403)

**Solución:**
- Se agregó el permiso `view_fines` al rol Estudiante
- Los estudiantes solo pueden ver sus propias multas
- Admin/Trabajadores pueden ver todas las multas

### 3. ✅ Formulario de Usuarios Corregido

**Problema:** No se podían crear usuarios - error en el select de roles

**Solución:**
- Corregido el formulario de creación de usuarios (`users/create.blade.php`)
- Corregido el formulario de edición de usuarios (`users/edit.blade.php`)
- Ahora usa correctamente `$role->name` en lugar de `$role`

### 4. ✅ Importación Masiva de Usuarios desde Excel/CSV

**Nueva Funcionalidad Implementada:**

#### Características:
- Importar múltiples usuarios desde archivos Excel (.xlsx, .xls) o CSV
- Plantilla descargable con formato correcto
- Validación automática de datos
- Reporte de errores detallado
- Asignación automática de roles

#### Cómo usar:
1. Ir a **Usuarios** → **Importar Excel**
2. Descargar la plantilla CSV
3. Completar con los datos:
   - Nombre
   - Email
   - Email Institucional
   - Contraseña
   - Rol (Admin, Jefe_Area, Trabajador, Estudiante)
4. Subir el archivo completado
5. El sistema validará y creará los usuarios automáticamente

#### Formato del archivo:
```csv
Nombre,Email,Email Institucional,Contraseña,Rol
Juan Pérez,juan.perez@example.com,juan.perez@iestp.edu.pe,password123,Estudiante
María García,maria.garcia@example.com,maria.garcia@iestp.edu.pe,securepass456,Trabajador
```

---

## 📖 Lógica de Préstamos: Estados "Activo" y "Vencido"

### Estados de Préstamos

Un préstamo puede tener los siguientes **estados**:

1. **Pendiente** (`pending`)
   - El préstamo fue solicitado pero aún no ha sido aprobado
   - Requiere aprobación de Admin/Trabajador/Jefe de Área

2. **Activo** (`activo`)
   - El préstamo fue aprobado y el material está en posesión del usuario
   - El usuario tiene el libro/material prestado
   - Aún no ha sido devuelto

3. **Devuelto** (`devuelto`)
   - El material fue devuelto a la biblioteca
   - El préstamo está completado

4. **Rechazado** (`rejected`)
   - La solicitud de préstamo fue rechazada por el personal

### ¿Qué significa "Vencido"?

**"Vencido"** NO es un estado, es una **condición** que se calcula dinámicamente:

- Un préstamo está **VENCIDO** cuando:
  - Su estado es `activo` (aún no devuelto)
  - Y la `fecha_devolucion_esperada` ya pasó (es menor que la fecha actual)

**Ejemplo:**
```
Préstamo #123
- Estado: activo
- Fecha de préstamo: 2025-01-01
- Fecha de devolución esperada: 2025-01-15
- Fecha actual: 2025-01-20

Este préstamo está ACTIVO pero VENCIDO (5 días de retraso)
```

### Cómo se detecta un préstamo vencido

En el modelo `Prestamo.php`, existe el método `isOverdue()`:

```php
public function isOverdue()
{
    if ($this->status !== 'activo') {
        return false; // Solo préstamos activos pueden estar vencidos
    }
    
    return now()->greaterThan($this->fecha_devolucion_esperada);
}
```

### Cálculo de Multas por Vencimiento

Cuando un préstamo está vencido, se puede calcular la multa:

```php
public function calculateFineAmount($dailyRate = 1.50)
{
    if (!$this->isOverdue()) {
        return 0; // No hay multa si no está vencido
    }
    
    $daysOverdue = now()->diffInDays($this->fecha_devolucion_esperada);
    return $daysOverdue * $dailyRate; // Ej: 5 días * S/. 1.50 = S/. 7.50
}
```

### Flujo Completo de un Préstamo

```
1. SOLICITUD (Estudiante)
   ↓
   Estado: pending
   
2. APROBACIÓN (Admin/Trabajador)
   ↓
   Estado: activo
   fecha_devolucion_esperada: hoy + 7 días
   
3a. DEVOLUCIÓN A TIEMPO
    ↓
    Estado: devuelto
    Sin multa
    
3b. DEVOLUCIÓN TARDÍA
    ↓
    isOverdue() = true
    Se genera multa automática
    Estado: devuelto
    Multa: días_retraso * tarifa_diaria
```

### Consultas Útiles

**Obtener préstamos vencidos:**
```php
$overdueLoans = Prestamo::where('status', 'activo')
    ->where('fecha_devolucion_esperada', '<', now())
    ->get();
```

**Obtener préstamos activos (no vencidos):**
```php
$activeLoans = Prestamo::where('status', 'activo')
    ->where('fecha_devolucion_esperada', '>=', now())
    ->get();
```

---

## 🎯 Roles y Permisos

### Estudiante
- Ver materiales
- Ver sus propios préstamos
- Solicitar préstamos
- Ver sus propias multas
- Crear reservaciones
- Ver repositorio
- Subir documentos

### Trabajador
- Todo lo del estudiante +
- Aprobar préstamos
- Crear préstamos directamente
- Devolver préstamos
- Gestionar inventario
- Ver todos los préstamos
- Crear multas
- Ver todas las multas
- Gestionar reservaciones
- Ver usuarios

### Jefe de Área
- Todo lo del trabajador +
- Crear materiales
- Editar materiales
- Aprobar documentos del repositorio
- Gestionar repositorio

### Admin
- **TODOS** los permisos del sistema
- Crear/editar/eliminar usuarios
- Gestionar roles
- Condonar multas
- Eliminar materiales
- Acceso completo al sistema

---

## 🔐 Usuarios de Prueba

```
Admin:
- Email: admin@iestp.local
- Password: password

Trabajador:
- Email: trabajador@iestp.local
- Password: password

Estudiante:
- Email: estudiante@iestp.local
- Password: password

Jefe de Área:
- Email: jefe@iestp.local
- Password: password
```

---

## 📝 Notas Importantes

1. **Multas Automáticas:** El sistema puede generar multas automáticamente cuando un préstamo se devuelve tarde.

2. **Tarifa de Multa:** Por defecto es S/. 1.50 por día de retraso (configurable).

3. **Permisos:** Los permisos se asignan a través de roles usando Spatie Laravel Permission.

4. **Importación de Usuarios:** Útil para inicio de semestre cuando se necesita registrar muchos estudiantes.

5. **Validación de Email:** Tanto el email personal como el institucional deben ser únicos.

---

## 🚀 Próximos Pasos Recomendados

1. Ejecutar las migraciones y seeders para actualizar permisos:
   ```bash
   php artisan migrate:fresh --seed
   ```

2. Probar la importación de usuarios con la plantilla

3. Verificar que cada rol tenga acceso correcto a sus funciones

4. Configurar notificaciones por email para préstamos vencidos (opcional)

---

**Fecha de actualización:** 2025-11-26
**Versión:** 2.0
