# Sistema de Límite de Préstamos y Gestión de Stock

## 📋 Resumen de Funcionalidades Implementadas

### 1. Límite de 3 Libros por Usuario ✅

Se ha implementado un sistema que **limita a cada estudiante a solicitar máximo 3 libros** simultáneamente (incluyendo préstamos pendientes de aprobación y aprobados).

#### Características:

- **Validación en múltiples puntos**:
  - Al intentar seleccionar un libro para solicitar préstamo
  - Al enviar la solicitud de préstamo
  - Al crear un préstamo desde el panel administrativo

- **Mensajes claros**: Cuando un usuario alcanza el límite, recibe el mensaje:
  > "Alcanzó el límite permitido de libros que se puede prestar (máximo 3)"

- **Contador visual**: Los estudiantes ven una tarjeta informativa en "Mis Préstamos" que muestra:
  - Número actual de libros prestados (ej: "2/3")
  - Cuántos libros más pueden solicitar
  - Barra de progreso visual
  - Botón para solicitar nuevo libro (solo si no alcanzó el límite)

### 2. Gestión de Stock - Libros Físicos ✅

Se implementó un sistema que **oculta automáticamente los libros del catálogo** cuando ya están prestados o reservados.

#### Características:

- **Filtrado inteligente del catálogo**:
  - Los estudiantes solo ven libros físicos que están **verdaderamente disponibles**
  - Se excluyen libros que tienen préstamos activos o pendientes de aprobación
  - Los administradores ven todos los libros (para gestión)

- **Validaciones de disponibilidad**:
  - Al solicitar un libro se verifica que no esté ya prestado
  - No se pueden crear préstamos duplicados del mismo libro
  - Se actualiza método `isAvailable()` para verificar préstamos activos

- **Prevención de reservas duplicadas**:
  - Un libro físico solo puede tener un préstamo activo a la vez
  - Si otro usuario intenta solicitarlo, recibe el mensaje:
    > "Este libro ya está reservado o prestado"

### 3. Configuración Centralizada 🔧

Se creó un archivo de configuración (`config/library.php`) para gestionar parámetros del sistema:

```php
// Límite de préstamos (por defecto 3, configurable desde .env)
'max_active_loans_per_user' => env('LIBRARY_MAX_LOANS', 3),

// Días de préstamo por defecto
'default_loan_days' => env('LIBRARY_DEFAULT_LOAN_DAYS', 14),

// Multa diaria por retraso
'daily_fine_rate' => env('LIBRARY_DAILY_FINE_RATE', 1.50),
```

Puedes cambiar estos valores agregando en tu `.env`:
```env
LIBRARY_MAX_LOANS=3
LIBRARY_DEFAULT_LOAN_DAYS=14
LIBRARY_DAILY_FINE_RATE=1.50
```

## 📁 Archivos Modificados

### Componentes Livewire:
1. **`app/Livewire/RequestLoan.php`**
   - Validación de límite de préstamos
   - Filtrado de materiales disponibles (excluye prestados)
   - Verificación de disponibilidad antes de crear solicitud

2. **`app/Livewire/MaterialsList.php`**
   - Filtro de catálogo para estudiantes (solo materiales disponibles)
   - Los administradores ven todos los materiales

### Controladores:
3. **`app/Http/Controllers/LoanController.php`**
   - Validación de límite de préstamos en método `store()`
   - Verificación de que el libro no esté ya prestado

### Modelos:
4. **`app/Models/Material.php`**
   - Actualización del método `isAvailable()`
   - Verifica stock físico Y préstamos activos

### Vistas:
5. **`resources/views/livewire/loans-list.blade.php`**
   - Tarjeta informativa con contador de libros prestados
   - Barra de progreso visual
   - Botón condicional para solicitar nuevo libro

### Configuración:
6. **`config/library.php`** (NUEVO)
   - Configuración centralizada del límite de préstamos
   - Parámetros personalizables desde `.env`

## 🔍 Flujo de Funcionamiento

### Para Estudiantes:

1. **Ver Catálogo**:
   - Solo ven libros físicos que NO están prestados actualmente
   - Los libros digitales siempre están disponibles

2. **Solicitar Préstamo**:
   - Sistema verifica que el estudiante no tenga 3 libros activos
   - Verifica que el libro esté disponible
   - Crea la solicitud con estado "pending"

3. **Mis Préstamos**:
   - Visualiza contador de libros prestados (X/3)
   - Barra de progreso mostrando cuántos puede solicitar
   - Botón para solicitar deshabilitado si llega al límite

### Para Administradores:

1. **Ver Catálogo**:
   - Ven TODOS los materiales (incluso los prestados)
   - Pueden gestionar el inventario completo

2. **Crear Préstamo**:
   - Sistema valida límite de 3 por estudiante
   - Verifica disponibilidad del material
   - Previene préstamos duplicados

## 🎯 Validaciones Implementadas

### Límite de Préstamos:
```php
// Se cuenta:
- Préstamos con status = 'activo'
- Approval_status IN ('pending', 'approved')

// NO se cuenta:
- Préstamos devueltos
- Préstamos rechazados
- Préstamos cancelados
```

### Disponibilidad de Material:
```php
// Un libro está disponible si:
1. Es tipo 'digital' (siempre disponible) O
2. Es físico Y tiene stock > 0 Y
3. NO tiene préstamos activos/pendientes
```

## 🚀 Cómo Probar

1. **Límite de 3 libros**:
   - Inicia sesión como estudiante
   - Ve a "Solicitar Préstamo"
   - Solicita 3 libros
   - Intenta solicitar un 4to libro → Debe mostrar error
   - Ve a "Mis Préstamos" → Deberías ver el contador en 3/3

2. **Stock de libros**:
   - Inicia sesión como estudiante A
   - Solicita un libro físico
   - Cierra sesión e inicia como estudiante B
   - Ve al catálogo → El libro que solicitó A NO debe aparecer
   - Inicia sesión como admin
   - Ve al catálogo → El libro SÍ aparece (para gestión)

## 💡 Mensajes del Sistema

- **Límite alcanzado**: "Alcanzó el límite permitido de libros que se puede prestar (máximo 3)"
- **Libro no disponible**: "Este libro ya está reservado o prestado"
- **Material agotado**: "Este material ya no está disponible"

## 🔐 Roles y Permisos

- **Estudiante**: 
  - Ve solo libros disponibles
  - Máximo 3 préstamos activos
  - Ve contador de préstamos

- **Admin/Trabajador**:
  - Ve todos los libros
  - Puede crear préstamos (validando límite del estudiante)
  - Gestiona devoluciones

## ✨ Mejoras Futuras Sugeridas

1. Notificación por email cuando un libro se devuelve y hay lista de espera
2. Sistema de reservas (cola de espera para libros agotados)
3. Historial de préstamos por usuario
4. Dashboard con estadísticas de libros más solicitados
5. Ajustar límite de préstamos por rol o nivel académico

---

**Fecha de Implementación**: 2025-12-04
**Estado**: ✅ Completado y Funcional
