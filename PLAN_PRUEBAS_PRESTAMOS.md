# 🧪 Plan de Pruebas - Sistema de Límite de Préstamos

## ✅ Checklist de Pruebas

### 1. Prueba: Límite de 3 Libros

#### Test 1.1: Solicitar hasta el límite
- [ ] Iniciar sesión como estudiante
- [ ] Ir a "Solicitar Préstamo" (`/loan-requests`)
- [ ] Seleccionar libro 1 y solicitar → ✅ Debe crear solicitud
- [ ] Seleccionar libro 2 y solicitar → ✅ Debe crear solicitud
- [ ] Seleccionar libro 3 y solicitar → ✅ Debe crear solicitud
- [ ] Ver "Mis Préstamos" → Debe mostrar contador **3/3**

#### Test 1.2: Intentar exceder el límite
- [ ] Con 3 préstamos activos/pendientes
- [ ] Ir a "Solicitar Préstamo"
- [ ] Intentar seleccionar un 4to libro
- [ ] **Resultado esperado**: Mensaje de error
  ```
  ⚠️ Alcanzó el límite permitido de libros que se puede prestar (máximo 3)
  ```

#### Test 1.3: Límite se libera al devolver
- [ ] Como administrador, devolver uno de los 3 libros
- [ ] Como estudiante, ver "Mis Préstamos" → Debe mostrar **2/3**
- [ ] Intentar solicitar un nuevo libro → ✅ Debe permitir

---

### 2. Prueba: Stock de Libros Físicos

#### Test 2.1: Libro desaparece del catálogo al ser prestado
- [ ] **Estudiante A**: Iniciar sesión
- [ ] Ir al catálogo (`/materials`)
- [ ] Verificar que libro "Clean Code" aparece como **Disponible**
- [ ] Solicitar préstamo de "Clean Code"
- [ ] Cerrar sesión
- [ ] **Estudiante B**: Iniciar sesión
- [ ] Ir al catálogo
- [ ] **Resultado esperado**: "Clean Code" **NO debe aparecer** en el catálogo

#### Test 2.2: Administrador ve todos los libros
- [ ] Iniciar sesión como **Admin**
- [ ] Ir al catálogo
- [ ] **Resultado esperado**: "Clean Code" **SÍ debe aparecer** (marcado como "Agotado")

#### Test 2.3: Libro reaparece al ser devuelto
- [ ] Como administrador, devolver el libro "Clean Code"
- [ ] Cerrar sesión
- [ ] Como estudiante diferente, ir al catálogo
- [ ] **Resultado esperado**: "Clean Code" debe aparecer como **Disponible**

---

### 3. Prueba: Validaciones de Disponibilidad

#### Test 3.1: No permitir préstamos duplicados
- [ ] Estudiante A solicita libro "Python Crash Course"
- [ ] Estudiante B intenta solicitar el mismo libro
- [ ] **Resultado esperado**: Mensaje de error
  ```
  ❌ Este libro ya está reservado o prestado
  ```

#### Test 3.2: Verificar stock físico
- [ ] Crear un libro físico con stock = 0
- [ ] Como estudiante, ir al catálogo
- [ ] **Resultado esperado**: El libro NO debe aparecer

---

### 4. Prueba: Contador Visual en "Mis Préstamos"

#### Test 4.1: Sin préstamos (0/3)
- [ ] Estudiante sin préstamos activos
- [ ] Ir a "Mis Préstamos" (`/loans`)
- [ ] **Resultado esperado**:
  - Contador muestra **0/3**
  - Mensaje: "Puedes solicitar 3 más"
  - Barra de progreso: **0%** (vacía)
  - Botón "Solicitar Nuevo Libro" **visible**

#### Test 4.2: Con 2 préstamos (2/3)
- [ ] Estudiante con 2 préstamos activos
- [ ] Ir a "Mis Préstamos"
- [ ] **Resultado esperado**:
  - Contador muestra **2/3**
  - Mensaje: "Puedes solicitar 1 más"
  - Barra de progreso: **~67%**
  - Botón "Solicitar Nuevo Libro" **visible**

#### Test 4.3: Límite alcanzado (3/3)
- [ ] Estudiante con 3 préstamos activos
- [ ] Ir a "Mis Préstamos"
- [ ] **Resultado esperado**:
  - Contador muestra **3/3**
  - Mensaje: "¡Límite alcanzado!"
  - Barra de progreso: **100%** (color amarillo)
  - Botón "Solicitar Nuevo Libro" **NO visible**

---

### 5. Prueba: Estados de Préstamos

#### Test 5.1: Préstamos pendientes cuentan para el límite
- [ ] Estudiante solicita 3 libros (estado: pending)
- [ ] Intentar solicitar un 4to
- [ ] **Resultado esperado**: Error de límite alcanzado

#### Test 5.2: Préstamos aprobados cuentan para el límite
- [ ] Admin aprueba las 3 solicitudes (estado: approved)
- [ ] Estudiante intenta solicitar otro
- [ ] **Resultado esperado**: Error de límite alcanzado

#### Test 5.3: Préstamos devueltos NO cuentan
- [ ] Devolver los 3 libros (estado: devuelto)
- [ ] Ver "Mis Préstamos" → Contador debe mostrar **0/3**
- [ ] Intentar solicitar nuevo libro → ✅ Debe permitir

#### Test 5.4: Préstamos rechazados NO cuentan
- [ ] Admin rechaza una solicitud (estado: rejected)
- [ ] Contador debe disminuir
- [ ] Debe permitir solicitar nuevamente

---

### 6. Prueba: Creación de Préstamos por Administrador

#### Test 6.1: Admin puede crear préstamo manualmente
- [ ] Iniciar sesión como **Admin**
- [ ] Ir a "Crear Préstamo" (`/loans/create`)
- [ ] Seleccionar estudiante y libro
- [ ] Crear préstamo
- [ ] **Resultado esperado**: Préstamo creado exitosamente

#### Test 6.2: Admin no puede exceder límite del estudiante
- [ ] Estudiante ya tiene 3 préstamos activos
- [ ] Admin intenta crear un 4to préstamo para ese estudiante
- [ ] **Resultado esperado**: Mensaje de error
  ```
  ⚠️ El usuario alcanzó el límite permitido de libros que se puede prestar (máximo 3)
  ```

#### Test 6.3: Admin no puede prestar libro ya prestado
- [ ] Libro "Design Patterns" ya está prestado
- [ ] Admin intenta crear préstamo del mismo libro
- [ ] **Resultado esperado**: Mensaje de error
  ```
  ❌ Este libro ya está reservado o prestado
  ```

---

### 7. Prueba: Configuración Personalizada

#### Test 7.1: Cambiar límite desde .env
- [ ] Editar archivo `.env`:
  ```env
  LIBRARY_MAX_LOANS=5
  ```
- [ ] Ejecutar:
  ```bash
  php artisan config:cache
  ```
- [ ] Como estudiante, ver "Mis Préstamos"
- [ ] **Resultado esperado**: Contador debe mostrar **/5** en lugar de **/3**

#### Test 7.2: Verificar valor por defecto
- [ ] Eliminar variable `LIBRARY_MAX_LOANS` del `.env`
- [ ] Limpiar caché: `php artisan config:clear`
- [ ] **Resultado esperado**: Sistema usa valor por defecto (3)

---

## 🚀 Comandos Útiles para Pruebas

```bash
# Limpiar cachés
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Ver configuración actual
php artisan tinker
>>> config('library.max_active_loans_per_user')

# Ver préstamos de un usuario
php artisan tinker
>>> $user = User::find(1);
>>> $user->prestamos()->where('status', 'activo')->whereIn('approval_status', ['pending', 'approved'])->count();

# Crear datos de prueba
php artisan db:seed --class=PrestamoSeeder
```

---

## 📝 Checklist de Validación Final

- [ ] ✅ Límite de 3 libros funciona correctamente
- [ ] ✅ Libros prestados NO aparecen en catálogo para estudiantes
- [ ] ✅ Libros prestados SÍ aparecen para administradores
- [ ] ✅ Contador visual muestra información correcta
- [ ] ✅ Validaciones previenen préstamos duplicados
- [ ] ✅ Mensajes de error son claros y específicos
- [ ] ✅ Configuración desde .env funciona
- [ ] ✅ Devolución de libros libera el límite
- [ ] ✅ Estados de préstamos se contabilizan correctamente
- [ ] ✅ Sistema es responsive en móvil y escritorio

---

## 🐛 Errores Conocidos / Solución de Problemas

### Problema: "Límite no se actualiza"
**Solución**: Limpiar caché de configuración
```bash
php artisan config:cache
php artisan view:clear
```

### Problema: "Libros no desaparecen del catálogo"
**Verificar**:
1. El préstamo tiene `status = 'activo'`
2. El `approval_status` es 'pending' o 'approved'
3. El material es de tipo 'fisico'

**Solución**: Revisar en base de datos
```sql
SELECT * FROM prestamos WHERE status = 'activo';
SELECT * FROM materials WHERE id = [material_id];
```

### Problema: "Contador muestra valor incorrecto"
**Solución**: Verificar que la consulta cuenta correctamente
```bash
php artisan tinker
>>> use App\Models\Prestamo;
>>> Prestamo::where('user_id', 1)
...   ->where('status', 'activo')
...   ->whereIn('approval_status', ['pending', 'approved'])
...   ->get();
```

---

**Fecha de pruebas**: _____________  
**Probado por**: _____________  
**Estado final**: [ ] Aprobado  [ ] Requiere correcciones
