# 🗺️ MAPA DE RELACIONES - Base de Datos del Sistema de Biblioteca

## 📊 Diagrama de Relaciones Principal

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         SISTEMA DE BIBLIOTECA                           │
└─────────────────────────────────────────────────────────────────────────┘

┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃                      MÓDULO: USUARIOS Y PERMISOS                       ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

        ┌──────────┐
        │  users   │ ← Tabla central de usuarios
        └────┬─────┘
             │
     ┌───────┼────────┬──────────┐
     │       │        │          │
     ▼       ▼        ▼          ▼
┌─────────┐ ┌────┐ ┌──────┐ ┌─────────┐
│  roles  │ │permissions│ │sessions │
└─────────┘ └──────────┘ └─────────┘
     │            │
     └─────┬──────┘
           ▼
  ┌─────────────────────┐
  │ role_has_permissions│
  └─────────────────────┘
  ┌──────────────────┐
  │model_has_roles   │
  └──────────────────┘
  ┌─────────────────────────┐
  │model_has_permissions    │
  └─────────────────────────┘

┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃                    MÓDULO: CATÁLOGO DE MATERIALES                      ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

         ┌─────────────────┐
         │   materials     │ ← Tabla principal del catálogo
         │  (polymorphic)  │
         └────────┬────────┘
                  │
         ┌────────┴────────┐
         │                 │
         ▼                 ▼
┌──────────────────┐  ┌──────────────────┐
│material_fisicos  │  │material_digitals │
│                  │  │                  │
│• stock           │  │• url_descarga    │
│• available       │  │• file_size       │
│• isbn            │  │• formato         │
│• ubicacion       │  └──────────────────┘
└──────────────────┘

┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃                    MÓDULO: GESTIÓN DE PRÉSTAMOS                        ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

  ┌────────┐              ┌────────────┐
  │ users  │──────────────│ prestamos  │
  └────────┘              └─────┬──────┘
       │                        │
       │                   ┌────┴───────┬──────────┐
       │                   │            │          │
       │                   ▼            ▼          ▼
       │             ┌──────────┐ ┌─────────┐ ┌────────────────┐
       │             │materials │ │ multas  │ │ approval_logs  │
       │             └──────────┘ └─────────┘ │ (historial de  │
       │                              │       │ aprobaciones   │
       │                              ▼       │ de PRÉSTAMOS)  │
       └──────────────────────────────────────└────────────────┘
                                      │
                                      ▼
                              ┌───────────┐
                              │  users    │
                              │(reviewer) │
                              └───────────┘

  ┌────────┐              ┌────────────┐
  │ users  │──────────────│  reservas  │
  └────────┘              └─────┬──────┘
                                │
                                ▼
                          ┌──────────┐
                          │materials │
                          └──────────┘

┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃                  MÓDULO: REPOSITORIO INSTITUCIONAL                     ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

  ┌────────┐              ┌───────────────────────┐
  │ users  │──────────────│repositorio_documentos │
  └────────┘              └───────────┬───────────┘
       │                              │
       │                              ▼
       │                      ┌───────────────┐
       │                      │ aprobaciones  │
       │                      │ (aprobaciones │
       └──────────────────────│ de DOCUMENTOS │
                (jefe_area)   │ del repositorio)│
                              └───────────────┘
```

---

## 🔍 DIFERENCIAS CLAVE ENTRE TABLAS SIMILARES

### `aprobaciones` vs `approval_logs`

| Aspecto | `aprobaciones` | `approval_logs` |
|---------|----------------|-----------------|
| **Módulo** | Repositorio Institucional | Sistema de Préstamos |
| **Aprueba** | Documentos académicos (tesis) | Solicitudes de préstamos |
| **Quién aprueba** | Jefe de Área | Admin/Bibliotecario |
| **Relación** | `repositorio_documentos` | `prestamos` |
| **Estados** | pendiente, aprobado, rechazado | requested, approved, rejected, cancelled |
| **Propósito** | Workflow académico | Workflow de biblioteca |

### Ejemplo de Uso:

**Tabla `aprobaciones` (Repositorio)**:
```
┌─────────────────────────────────────────────────┐
│ Estudiante sube tesis                           │
│    ↓                                            │
│ Se crea registro en repositorio_documentos      │
│    ↓                                            │
│ Se crea registro en aprobaciones                │
│    ↓                                            │
│ Jefe de Área revisa y aprueba/rechaza          │
│    ↓                                            │
│ Si aprobado → documento se publica             │
└─────────────────────────────────────────────────┘
```

**Tabla `approval_logs` (Préstamos)**:
```
┌─────────────────────────────────────────────────┐
│ Estudiante solicita libro                      │
│    ↓                                            │
│ Se crea préstamo con status='activo'           │
│ y approval_status='pending'                    │
│    ↓                                            │
│ Se crea registro en approval_logs (requested)  │
│    ↓                                            │
│ Admin revisa y aprueba/rechaza                 │
│    ↓                                            │
│ Se actualiza approval_logs (approved/rejected) │
│    ↓                                            │
│ Si aprobado → se entrega el libro              │
└─────────────────────────────────────────────────┘
```

---

## 📋 CARDINALIDADES

### Relaciones Uno a Muchos (1:N)

```
users (1) ──→ (N) prestamos
users (1) ──→ (N) multas
users (1) ──→ (N) reservas
users (1) ──→ (N) repositorio_documentos
materials (1) ── (N) prestamos
materials (1) ──→ (N) reservas
prestamos (1) ──→ (N) multas
prestamos (1) ──→ (N) approval_logs
repositorio_documentos (1) ──→ (N) aprobaciones
```

### Relaciones Uno a Uno (1:1)

```
materials (1) ──→ (1) material_fisicos
materials (1) ──→ (1) material_digitals
```

### Relaciones Muchos a Muchos (N:M)

```
users (N) ←──→ (M) roles
    (a través de: model_has_roles)

users (N) ←──→ (M) permissions
    (a través de: model_has_permissions)

roles (N) ←──→ (M) permissions
    (a través de: role_has_permissions)
```

---

## 🎯 FLUJO DE DATOS POR CASO DE USO

### Caso 1: Préstamo de Libro

```
1. USER crea solicitud
   ├─→ INSERT en prestamos
   │   ├─ user_id (FK → users)
   │   ├─ material_id (FK → materials)
   │   ├─ status = 'activo'
   │   └─ approval_status = 'pending'
   │
   └─→ INSERT en approval_logs
       ├─ prestamo_id (FK → prestamos)
       ├─ reviewer_id (FK → users)
       └─ action = 'requested'

2. ADMIN aprueba
   ├─→ UPDATE prestamos.approval_status = 'approved'
   └─→ INSERT en approval_logs (action='approved')

3. Al devolver (tardío)
   └─→ INSERT en multas
       ├─ prestamo_id (FK → prestamos)
       ├─ user_id (FK → users)
       └─ monto = días_retraso * tarifa
```

### Caso 2: Subir Tesis al Repositorio

```
1. USER sube tesis
   └─→ INSERT en repositorio_documentos
       ├─ user_id (FK → users)
       ├─ estado = 'pendiente'
       └─ file_path = '/storage/...'

2. Sistema crea aprobación
   └─→ INSERT en aprobaciones
       ├─ documento_id (FK → repositorio_documentos)
       ├─ jefe_area_id (FK → users)
       └─ estado = 'pendiente'

3. JEFE_AREA aprueba
   ├─→ UPDATE aprobaciones.estado = 'aprobado'
   └─→ UPDATE repositorio_documentos.estado = 'publicado'
```

---

## 🗃️ ÍNDICES RECOMENDADOS (para mejor rendimiento)

```sql
-- PRÉSTAMOS
CREATE INDEX idx_prestamos_user_status 
    ON prestamos(user_id, status);

CREATE INDEX idx_prestamos_material_status 
    ON prestamos(material_id, status, approval_status);

CREATE INDEX idx_prestamos_fecha_devolucion 
    ON prestamos(fecha_devolucion_esperada) 
    WHERE status = 'activo';

-- MATERIALES
CREATE INDEX idx_materials_type 
    ON materials(type);

CREATE INDEX idx_materials_title 
    ON materials(title);

-- MULTAS
CREATE INDEX idx_multas_user_status 
    ON multas(user_id, status);

CREATE INDEX idx_multas_prestamo 
    ON multas(prestamo_id);

-- REPOSITORIO
CREATE INDEX idx_repositorio_estado 
    ON repositorio_documentos(estado);

CREATE INDEX idx_repositorio_tipo 
    ON repositorio_documentos(tipo);

-- APROBACIONES
CREATE INDEX idx_aprobaciones_documento 
    ON aprobaciones(documento_id, estado);

-- APPROVAL LOGS
CREATE INDEX idx_approval_logs_prestamo 
    ON approval_logs(prestamo_id, action);
```

---

## 📊 RESUMEN DE TABLAS POR MÓDULO

| Módulo | Tablas | Total |
|--------|--------|-------|
| **Sistema Laravel** | migrations, sessions, cache, cache_locks, jobs, failed_jobs, job_batches, password_reset_tokens | 8 |
| **Autenticación/Permisos** | users, roles, permissions, model_has_roles, model_has_permissions, role_has_permissions | 6 |
| **Catálogo** | materials, material_fisicos, material_digitals | 3 |
| **Préstamos** | prestamos, approval_logs, multas, reservas | 4 |
| **Repositorio** | repositorio_documentos, aprobaciones | 2 |
| **TOTAL** | | **23 tablas** |

---

## ✅ CONCLUSIÓN

La base de datos está **bien estructurada y normalizada**. No hay duplicación real de datos.

**La aparente "duplicación" es en realidad dos sistemas separados:**
- `aprobaciones` → Para documentos del repositorio académico
- `approval_logs` → Para préstamos de biblioteca

**Ambas tablas son necesarias** porque manejan flujos de trabajo diferentes.
