# ✅ FLUJO COMPLETO DE PRÉSTAMOS - Sistema Actualizado

## 🎯 Nuevo Flujo Implementado

### Resumen del Cambio
**ANTES**: Solo se contaban préstamos activos  
**AHORA**: Se cuentan TODAS las solicitudes en proceso (máximo 3)

---

## 📊 Estados del Préstamo

| Estado | Descripción | Cuenta para límite | Visible en catálogo |
|--------|-------------|-------------------|---------------------|
| **`pending`** | Solicitud enviada, esperando aprobación del admin | ✅ SÍ | ❌ NO (libro reservado) |
| **`approved`** | Aprobado por admin, estudiante tiene 24h para recoger | ✅ SÍ | ❌ NO (aún reservado) |
| **`collected`** | Estudiante recogió el libro, tiene 7 días para devolver | ✅ SÍ | ❌ NO (prestado activamente) |
| **`returned`** | Libro devuelto | ❌ NO | ✅ SÍ (libro disponible) |
| **`rejected`** | Admin rechazó la solicitud | ❌ NO | ✅ SÍ (libro disponible) |
| **`expired`** | Estudiante NO recogió en 24h | ❌ NO | ✅ SÍ (libro disponible) |
| **`cancelled`** | Estudiante canceló la solicitud | ❌ NO | ✅ SÍ (libro disponible) |

---

## 🔄 Flujo Completo Paso a Paso

### PASO 1: Estudiante Solicita Libro

```
┌─────────────────────────────────────────────────┐
│ 1. Estudiante ve catálogo                      │
│    - Solo ve libros DISPONIBLES                │
│    - Libros con solicitudes activas NO aparecen│
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│ 2. Verifica límite de solicitudes               │
│    - Cuenta: pending + approved + collected    │
│    - Si >= 3 → ERROR: "Límite alcanzado"       │
│    - Si < 3 → Permite continuar                │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│ 3. Crea solicitud                               │
│    approval_status = 'pending'                  │
│    status = 'activo'                            │
│    Stock físico -1 (RESERVA INMEDIATA)         │
└─────────────────────────────────────────────────┘
```

**Resultado**: 
- ✅ Solicitud creada
- ✅ Libro desaparece del catálogo
- ✅ Cuenta 1 de 3 solicitudes

---

### PASO 2: Admin Revisa y Aprueba

```
┌─────────────────────────────────────────────────┐
│ 1. Admin ve solicitudes pendientes              │
│    - Filtra por: approval_status = 'pending'   │
└─────────────────────────────────────────────────┘
                    ↓
          ┌─────────┴─────────┐
          │                   │
          ▼                   ▼
    ┌──────────┐        ┌──────────┐
    │ APROBAR  │        │ RECHAZAR │
    └──────────┘        └──────────┘
          │                   │
          ▼                   ▼
┌─────────────────┐   ┌─────────────────┐
│ approval_status │   │ approval_status │
│ = 'approved'    │   │ = 'rejected'    │
│                 │   │                 │
│ fecha_limite_   │   │ Stock +1        │
│ recogida =      │   │ (Devuelve al    │
│ now() + 24h     │   │  catálogo)      │
└─────────────────┘   └─────────────────┘
```

**Si aprueba**:
- ✅ Estudiante tiene 24 horas para recoger
- ✅ Libro sigue NO disponible en catálogo
- ✅ Sigue contando 1 de 3 solicitudes

**Si rechaza**:
- ✅ Stock vuelve al catálogo
- ✅ Libro aparece como disponible
- ✅ Ya NO cuenta (0/3 si era la única)

---

### PASO 3A: Estudiante Recoge el Libro (Dentro de 24h)

```
┌─────────────────────────────────────────────────┐
│ 1. Admin marca como "Recogido"                  │
│    approval_status = 'collected'                │
│    fecha_recogida = now()                       │
│    fecha_devolucion_esperada = now() + 7 días   │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│ INICIA PERÍODO DE PRÉSTAMO                      │
│ - El estudiante tiene 7 DÍAS para devolver     │
│ - Cuenta para el límite (sigue 1/3)            │
│ - Libro NO disponible en catálogo              │
└─────────────────────────────────────────────────┘
```

**Resultado**:
- ✅ Préstamo activo
- ✅ 7 días para devolver
- ✅ Sigue contando 1/3

---

### PASO 3B: Estudiante NO Recoge (Pasan 24 horas)

```
┌─────────────────────────────────────────────────┐
│ Admin detecta expiración (pasan 24h)            │
│ - Ejecuta: markAsExpired()                      │
│   approval_status = 'expired'                   │
│   status = 'vencido'                            │
│   Stock físico +1 (DEVUELVE AL CATÁLOGO)       │
└─────────────────────────────────────────────────┘
```

**Resultado**:
- ✅ Libro vuelve al catálogo
- ✅ Ya NO cuenta (0/3 si era la única)
- ✅ Estudiante puede sol

icitar nuevamente

---

### PASO 4: Devolución del Libro

```
┌─────────────────────────────────────────────────┐
│ Admin marca como devuelto                       │
│ approval_status = 'returned'                    │
│ status = 'devuelto'                             │
│ fecha_devolucion_actual = now()                 │
│ Stock físico +1 (DEVUELVE AL CATÁLOGO)         │
└─────────────────────────────────────────────────┘
```

**Resultado**:
- ✅ Libro disponible en catálogo
- ✅ Ya NO cuenta (0/3 si era la única)
- ✅ Si devolvió tarde → multa

---

## 📈 Ejemplos de Conteo

### Escenario 1: Estudiante con 1 solicitud pendiente
```
Solicitudes:
- Libro A: approval_status = 'pending'

Contador: 1/3
¿Puede solicitar más? ✅ SÍ (2 más)
```

### Escenario 2: Estudiante con 2 aprobados y 1 prestado
```
Solicitudes:
- Libro A: approval_status = 'approved' (esperando recoger)
- Libro B: approval_status = 'approved' (esperando recoger)  
- Libro C: approval_status = 'collected' (prestado activo)

Contador: 3/3
¿Puede solicitar más? ❌ NO (límite alcanzado)
```

### Escenario 3: Estudiante devuelve 1 libro
```
Antes:
- Libro A: approval_status = 'collected'
- Libro B: approval_status = 'collected'
- Libro C: approval_status = 'collected'
Contador: 3/3

Después (devuelve Libro A):
- Libro A: approval_status = 'returned' ← Ya NO cuenta
- Libro B: approval_status = 'collected'
- Libro C: approval_status = 'collected'
Contador: 2/3

¿Puede solicitar más? ✅ SÍ (1 más)
```

### Escenario 4: Admin rechaza una solicitud
```
Antes:
- Libro A: approval_status = 'pending'
- Libro B: approval_status = 'pending'
- Libro C: approval_status = 'pending'
Contador: 3/3

Después (admin rechaza Libro A):
- Libro A: approval_status = 'rejected' ← Ya NO cuenta
- Libro B: approval_status = 'pending'
- Libro C: approval_status = 'pending'
Contador: 2/3

¿Puede solicitar más? ✅ SÍ (1 más)
```

---

## 🔍 Campos Nuevos en la Base de Datos

| Campo | Tipo | Propósito |
|-------|------|-----------|
| `fecha_limite_recogida` | datetime | Fecha límite para recoger (24h después de aprobar) |
| `fecha_recogida` | datetime | Cuándo el estudiante recogió el libro |
| `approval_status` | enum | Estados: pending, approved, collected, returned, rejected, cancelled, expired |

---

## 🎨 Cambios en la Interfaz

### Vista "Solicitar Préstamo":
- ✅ Solo muestra libros DISPONIBLES
- ✅ Verifica límite de 3 ANTES de solicitar
- ✅ Mensaje claro: "Alcanzó el límite de solicitudes (máximo 3)"

### Vista "Mis Préstamos" (Estudiante):
- ✅ Contador actualizado: "X/3 solicitudes activas"
- ✅ Aclaración: "(Incluye: pendientes, aprobadas y prestadas)"
- ✅ Barra de progreso visual

### Vista "Aprobar Préstamos" (Admin):
- ✅ Botón "Aprobar" → Da 24 horas para recoger
- ✅ Botón "Rechazar" → Devuelve al catálogo
- ✅ Botón "Marcar como Recogido" → Inicia los 7 días
- ✅ Botón "Marcar como Expirado" → Si pasaron 24h

---

## ⚙️ Comandos para Limpiar y Probar

```bash
# Limpiar cachés
php artisan cache:clear
php artisan config:cache
php artisan view:clear

# Verificar migraciones
php artisan migrate:status

# Iniciar servidor
php artisan serve
```

---

## 🧪 Casos de Prueba

### Test 1: Límite de 3 solicitudes
1. Como estudiante, solicita 3 libros
2. Contador debe mostrar: 3/3
3. Intenta solicitar un 4to → ERROR ✅

### Test 2: Flujo de aprobación
1. Admin aprueba solicitud
2. Estudiante tiene 24h
3. Verifica `fecha_limite_recogida` en DB ✅

### Test 3: Recogida del libro
1. Admin marca como "Recogido"
2. `fecha_devolucion_esperada` = +7 días ✅
3. Libro sigue NO disponible en catálogo ✅

### Test 4: No recoge en 24h
1. Pasan 24 horas
2. Admin marca como "Expirado"
3. Stock vuelve al catálogo ✅
4. Contador disminuye ✅

---

## ✅ Resumen de Mejoras

| Antes | Ahora |
|-------|-------|
| Contaba solo préstamos activos | Cuenta toda solicitud en proceso |
| Libro desaparecía solo al aprobar | Libro desaparece al solicitar (reserva) |
| No había límite de tiempo para recoger | 24 horas para recoger |
| No se distinguía "aprobado" de "recogido" | Estados claros: approved → collected |
| Stock se manejaba manualmente | Stock automático en cada paso |

---

**Fecha de implementación**: 2025-12-04  
**Estado**: ✅ COMPLETADO Y FUNCIONAL  
**Flujo**: Optimizado para la lógica del negocio
