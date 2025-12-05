# 📚 Manual de Funcionalidades
## Sistema de Biblioteca Pedro P. Díaz

---

## 🎯 ¿Qué hace este sistema?

Es un sistema web para gestionar una biblioteca que permite:
- Administrar el catálogo de libros (físicos y digitales)
- Gestionar préstamos de libros a estudiantes
- Controlar multas por retrasos
- Subir documentos académicos al repositorio
- Generar reportes y estadísticas

---

## 👥 TIPOS DE USUARIOS

### 1. 👨‍💼 Administrador
**Acceso completo al sistema**
- Ve el Dashboard con estadísticas
- Gestiona todos los módulos
- Crea/edita/elimina usuarios
- Ve reportes completos
- Puede condonar multas

### 2. 👷 Trabajador de Biblioteca
- Gestiona préstamos (aprobar, entregar, recibir)
- Crea multas
- Administra reservaciones
- Sube documentos al repositorio

### 3. 🎓 Estudiante
- Ve el catálogo de materiales
- Solicita préstamos de libros
- Lee libros virtuales
- Ve sus multas pendientes
- Accede al repositorio

### 4. 👔 Jefe de Área
- Solo puede subir documentos al repositorio
- Sus documentos requieren aprobación del Admin

---

## 📖 MÓDULOS DEL SISTEMA

---

### 1. 📚 CATÁLOGO DE MATERIALES

**¿Qué es?**
Lista de todos los libros disponibles en la biblioteca.

**Tipos de materiales:**
| Tipo | Descripción | Acciones |
|------|-------------|----------|
| **Físico** | Libro impreso | Se puede solicitar préstamo |
| **Digital** | Libro en línea | Se accede directamente por URL |

**Funcionalidades:**
- ✅ Ver lista de materiales con búsqueda
- ✅ Filtrar por tipo (físico/digital)
- ✅ Ver detalles de cada material
- ✅ Crear nuevos materiales (Admin/Trabajador)
- ✅ Editar/Eliminar materiales

**Datos de un material físico:**
- Título, Autor, Código único
- ISBN, Editorial, Año de publicación
- Stock disponible
- Ubicación en biblioteca (Ej: "Estante A, Fila 3")

---

### 2. 📖 PRÉSTAMOS

**¿Cómo funciona el préstamo para ESTUDIANTES?**

```
PASO 1: Solicitar
─────────────────
El estudiante busca un libro y hace clic en "Solicitar Préstamo"
→ Estado: PENDIENTE

PASO 2: Aprobación
──────────────────
El Trabajador/Admin revisa y aprueba la solicitud
→ Estado: APROBADO
→ El estudiante tiene 24 HORAS para recoger el libro

PASO 3: Recogida
────────────────
El estudiante va a la biblioteca
El trabajador hace clic en "Entregar"
→ Estado: ACTIVO
→ Inician 7 DÍAS para devolver

PASO 4: Devolución
──────────────────
El estudiante devuelve el libro
El trabajador hace clic en "Recibir"
→ Estado: DEVUELTO

⚠️ Si hay retraso → Se genera MULTA automática
```

**¿Cómo funciona el préstamo para ADMIN?**
```
El Admin puede crear préstamos directamente
→ El libro se marca como "ya entregado"
→ No necesita aprobación
```

**Restricciones:**
- Máximo 3 préstamos activos por estudiante
- No puede solicitar si tiene multas pendientes
- No puede solicitar si tiene préstamos vencidos
- No puede solicitar si está bloqueado

---

### 3. 💰 MULTAS

**¿Cuándo se genera una multa?**
Cuando un estudiante devuelve un libro DESPUÉS de la fecha límite.

**Cálculo:**
```
S/. 1.00 por cada día de retraso
```

**Estados de multa:**
| Estado | Descripción |
|--------|-------------|
| **Pendiente** | El estudiante debe pagar |
| **Pagada** | El estudiante ya pagó |
| **Condonada** | El Admin perdonó la multa |

**Acciones del Admin:**
- ✅ Marcar como Pagada
- ✅ Condonar multa (perdonar)
- ✅ Editar monto
- ✅ Eliminar multa

**Efecto en el estudiante:**
- Con multa pendiente → NO puede solicitar préstamos
- Ve alertas al iniciar sesión
- Solo puede acceder al catálogo y sus multas

---

### 4. 📁 REPOSITORIO

**¿Qué es?**
Espacio para subir y compartir documentos académicos (tesis, trabajos, guías).

**¿Quién puede subir?**
| Usuario | Puede subir | Necesita aprobación |
|---------|-------------|---------------------|
| Admin | ✅ Sí | ❌ No (auto-publicado) |
| Trabajador | ✅ Sí | ❌ No (auto-publicado) |
| Jefe de Área | ✅ Sí | ✅ Sí (espera aprobación) |
| Estudiante | ❌ No | - |

**Flujo de aprobación:**
```
Jefe de Área sube documento
→ Estado: PENDIENTE
→ Admin lo revisa
→ Admin aprueba o rechaza
→ Estado: PUBLICADO o RECHAZADO
```

---

### 5. 👥 USUARIOS

**Gestión de usuarios (Solo Admin):**
- ✅ Ver lista de todos los usuarios
- ✅ Crear nuevo usuario
- ✅ Editar datos de usuario
- ✅ Cambiar rol de usuario
- ✅ Bloquear/Desbloquear usuario
- ✅ Importar usuarios desde Excel

**Bloqueo de usuario:**
```
Usuario bloqueado:
→ No puede solicitar préstamos
→ Sigue pudiendo ver catálogo
→ El Admin puede desbloquearlo
```

---

### 6. 📊 REPORTES (Solo Admin)

**Estadísticas que muestra:**
- Total de materiales (físicos vs digitales)
- Total de usuarios
- Préstamos activos y vencidos
- Multas pendientes vs cobradas
- Documentos en repositorio

**Gráficos:**
- Préstamos por mes (últimos 6 meses)
- Multas generadas por mes

**Rankings:**
- Top 10 libros más prestados
- Top 10 estudiantes más activos

**Filtros:**
- Por rango de fechas

---

## 🔔 ALERTAS Y NOTIFICACIONES

**Para estudiantes al iniciar sesión:**

| Situación | Alerta |
|-----------|--------|
| Préstamo vencido | ⚠️ "Tienes préstamos vencidos. Devuelve los libros inmediatamente" |
| Multa pendiente | 💰 "Tienes S/. XX en multas. Acércate a la biblioteca para pagar" |

---

## 🔒 SEGURIDAD

**Autenticación:**
- Login con email y contraseña
- Sesiones seguras

**Autorización:**
- Cada página verifica permisos
- Si no tienes permiso → Error 403

**Restricciones automáticas:**
- Estudiantes con problemas tienen acceso limitado
- Solo pueden ver catálogo, multas y repositorio

---

## 📱 NAVEGACIÓN

**Admin/Trabajador (Menú lateral):**
```
📊 Dashboard
📚 Materiales
📖 Préstamos
💰 Multas
👥 Usuarios
📁 Repositorio
📈 Reportes (solo Admin)
```

**Estudiante (Menú superior):**
```
📚 Catálogo
📖 Mis Préstamos
💰 Mis Multas
📁 Repositorio
```

---

## ⚙️ CONFIGURACIONES DEL SISTEMA

| Parámetro | Valor |
|-----------|-------|
| Máximo préstamos por usuario | 3 |
| Días para devolver | 7 |
| Horas para recoger (después de aprobar) | 24 |
| Multa por día de retraso | S/. 1.00 |

---

## 🎯 CASOS DE USO PRINCIPALES

### Caso 1: Estudiante solicita un libro
1. Estudiante inicia sesión
2. Va al Catálogo
3. Busca el libro
4. Hace clic en "Solicitar Préstamo"
5. Espera aprobación
6. Recoge el libro en 24 horas
7. Devuelve en 7 días

### Caso 2: Trabajador gestiona préstamo
1. Trabajador ve solicitudes pendientes
2. Aprueba la solicitud
3. Cuando el estudiante llega, hace clic en "Entregar"
4. Cuando el estudiante devuelve, hace clic en "Recibir"

### Caso 3: Admin genera reporte
1. Admin va a Reportes
2. Selecciona rango de fechas
3. Ve estadísticas y gráficos
4. Identifica libros más populares

### Caso 4: Estudiante con multa
1. Estudiante intenta solicitar préstamo
2. Sistema bloquea: "Tienes multas pendientes"
3. Estudiante ve alerta al iniciar sesión
4. Va a la biblioteca a pagar
5. Admin marca multa como "Pagada"
6. Estudiante queda desbloqueado automáticamente

---

## 🔐 CREDENCIALES DE PRUEBA

| Rol | Email | Contraseña |
|-----|-------|------------|
| Admin | admin@iestp.edu.pe | password |
| Trabajador | trabajador@iestp.edu.pe | password |
| Estudiante | estudiante@iestp.edu.pe | password |
| Jefe Área | jefe@iestp.edu.pe | password |

---

## 📞 RESUMEN RÁPIDO

| Función | Admin | Trabajador | Jefe Área | Estudiante |
|---------|-------|------------|-----------|------------|
| Ver catálogo | ✅ | ✅ | ✅ | ✅ |
| Crear materiales | ✅ | ✅ | ❌ | ❌ |
| Solicitar préstamo | ✅ | ✅ | ❌ | ✅ |
| Aprobar préstamos | ✅ | ✅ | ❌ | ❌ |
| Gestionar multas | ✅ | ✅ | ❌ | ❌ |
| Condonar multas | ✅ | ✅ | ❌ | ❌ |
| Gestionar usuarios | ✅ | ❌ | ❌ | ❌ |
| Ver reportes | ✅ | ❌ | ❌ | ❌ |
| Subir al repositorio | ✅ | ✅ | ✅ | ❌ |
| Aprobar documentos | ✅ | ❌ | ❌ | ❌ |

---

**Sistema desarrollado para:** IESTP Pedro P. Díaz  
**Fecha:** Diciembre 2024
