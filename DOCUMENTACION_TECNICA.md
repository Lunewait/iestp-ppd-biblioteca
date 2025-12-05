# 📚 Sistema de Biblioteca IESTP Pedro P. Díaz
## Documentación Técnica Completa

---

## 1. TECNOLOGÍAS UTILIZADAS

### Backend
| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| **Laravel** | 12.x | Framework PHP principal |
| **PHP** | 8.2 | Lenguaje de programación |
| **MySQL/PostgreSQL** | 8.0/15 | Base de datos |
| **Livewire** | 3.x | Componentes reactivos sin JavaScript |
| **Spatie Permission** | 6.x | Sistema de roles y permisos |

### Frontend
| Tecnología | Propósito |
|------------|-----------|
| **Blade** | Motor de plantillas de Laravel |
| **Tailwind CSS** | Framework de estilos |
| **Vite** | Compilador de assets |
| **Font Awesome** | Iconos |

### Despliegue
| Tecnología | Propósito |
|------------|-----------|
| **Docker** | Contenedores |
| **Render** | Hosting en la nube |
| **GitHub** | Control de versiones |

---

## 2. ARQUITECTURA DEL SISTEMA

### Patrón MVC (Model-View-Controller)
```
┌─────────────────────────────────────────────────────────────┐
│                        USUARIO                               │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    ROUTES (web.php)                          │
│  Define qué controlador maneja cada URL                     │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                 MIDDLEWARE (Seguridad)                       │
│  - Autenticación (auth)                                     │
│  - Roles (role:Admin)                                       │
│  - Permisos (permission:view_loans)                         │
│  - Restricciones estudiante (student.restrictions)         │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    CONTROLLERS                               │
│  - LoanController      → Préstamos                          │
│  - MaterialController  → Materiales                         │
│  - FineController      → Multas                             │
│  - UserController      → Usuarios                           │
│  - RepositoryController → Repositorio                       │
│  - ReportController    → Reportes                           │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      MODELS (Eloquent)                       │
│  - User, Prestamo, Material, Multa, etc.                    │
│  - Relaciones: hasMany, belongsTo                           │
│  - Reglas de negocio                                        │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    BASE DE DATOS                             │
│  MySQL (local) / PostgreSQL (producción)                    │
└─────────────────────────────────────────────────────────────┘
```

---

## 3. ESTRUCTURA DE CARPETAS

```
iestp-ppd-biblioteca/
├── app/
│   ├── Http/
│   │   ├── Controllers/           # Controladores
│   │   │   ├── LoanController.php
│   │   │   ├── MaterialController.php
│   │   │   ├── FineController.php
│   │   │   ├── UserController.php
│   │   │   ├── RepositoryController.php
│   │   │   └── ReportController.php
│   │   └── Middleware/            # Filtros de seguridad
│   │       ├── CheckRole.php
│   │       ├── CheckPermission.php
│   │       └── CheckStudentRestrictions.php
│   ├── Livewire/                  # Componentes reactivos
│   │   ├── AdminLoanManagement.php
│   │   ├── LoanRequests.php
│   │   ├── MaterialsList.php
│   │   └── ...
│   └── Models/                    # Modelos de datos
│       ├── User.php
│       ├── Prestamo.php
│       ├── Material.php
│       ├── Multa.php
│       └── ...
├── database/
│   ├── migrations/                # Estructura de tablas
│   └── seeders/                   # Datos iniciales
├── resources/views/               # Vistas (HTML)
│   ├── layouts/app.blade.php      # Layout principal
│   ├── materials/                 # Vistas de materiales
│   ├── loans/                     # Vistas de préstamos
│   ├── fines/                     # Vistas de multas
│   └── livewire/                  # Componentes Livewire
└── routes/web.php                 # Rutas de la aplicación
```

---

## 4. MODELOS Y BASE DE DATOS

### Diagrama de Relaciones
```
┌──────────────┐       ┌──────────────┐       ┌──────────────┐
│    USERS     │       │   PRESTAMOS  │       │  MATERIALS   │
├──────────────┤       ├──────────────┤       ├──────────────┤
│ id           │◄──────│ user_id      │       │ id           │
│ name         │       │ material_id  │──────►│ title        │
│ email        │       │ status       │       │ author       │
│ role         │       │ fecha_prestamo│      │ code         │
│ blocked_for_ │       │ fecha_devol  │       │ type         │
│   loans      │       │ approval_    │       │ (fisico/     │
└──────────────┘       │   status     │       │  digital)    │
       │               └──────────────┘       └──────────────┘
       │                      │                      │
       │                      ▼                      │
       │               ┌──────────────┐              │
       │               │    MULTAS    │              │
       │               ├──────────────┤              │
       └──────────────►│ user_id      │              │
                       │ prestamo_id  │◄─────────────┘
                       │ monto        │
                       │ status       │
                       │ (pendiente/  │
                       │  pagada/     │
                       │  condonada)  │
                       └──────────────┘
```

### Tablas Principales

**users**
- id, name, email, password
- institutional_email
- blocked_for_loans (boolean)
- roles (relación con spatie/permission)

**materials**
- id, title, author, code, type, description
- Relación con material_fisicos o material_digitales

**prestamos**
- id, user_id, material_id
- status: 'activo', 'devuelto', 'pendiente_recogida', 'cancelado'
- approval_status: 'pending', 'approved', 'collected', 'rejected', 'expired'
- fecha_prestamo, fecha_devolucion_esperada, fecha_devolucion_actual

**multas**
- id, user_id, prestamo_id
- monto, razon
- status: 'pendiente', 'pagada', 'condonada'

---

## 5. SISTEMA DE ROLES Y PERMISOS

### Roles
| Rol | Descripción |
|-----|-------------|
| **Admin** | Acceso total al sistema |
| **Trabajador** | Gestiona préstamos, multas, usuarios |
| **Jefe_Area** | Solo puede subir documentos al repositorio |
| **Estudiante** | Solicita préstamos, ve catálogo |

### Permisos por Módulo
```php
// Materiales
'view_materials', 'create_material', 'edit_material', 'delete_material'

// Préstamos
'view_loans', 'create_loan', 'approve_loan', 'return_loan', 'manage_loans'

// Multas
'view_fines', 'create_fine', 'manage_fines', 'forgive_fine'

// Usuarios
'view_users', 'create_user', 'edit_user', 'delete_user', 'manage_roles'

// Repositorio
'view_repository', 'submit_document', 'approve_document'
```

---

## 6. FLUJOS DE TRABAJO PRINCIPALES

### Flujo de Préstamo (Estudiante)
```
1. Estudiante solicita préstamo
   └─► estado: 'pending' / approval_status: 'pending'

2. Trabajador/Admin aprueba
   └─► estado: 'pendiente_recogida' / approval_status: 'approved'
   └─► Tiene 24 horas para recoger

3. Estudiante recoge el libro (botón "Entregar")
   └─► estado: 'activo' / approval_status: 'collected'
   └─► Inician 7 días para devolver

4. Estudiante devuelve (botón "Recibir")
   └─► estado: 'devuelto' / approval_status: 'returned'
   └─► Si hay retraso, se genera MULTA automáticamente
```

### Flujo de Préstamo (Admin directo)
```
1. Admin crea préstamo desde panel
   └─► estado: 'activo' / approval_status: 'collected'
   └─► El libro ya está entregado
```

### Flujo de Multas
```
1. Préstamo vencido → Multa automática (S/. 1 por día)
2. Admin marca como "Pagada" o "Condonada"
3. Si no hay más multas pendientes → Usuario desbloqueado automáticamente
```

### Flujo de Repositorio
```
Admin/Trabajador sube documento:
└─► Se publica automáticamente (estado: 'publicado')

Jefe de Área sube documento:
└─► Queda pendiente (estado: 'pendiente')
└─► Requiere aprobación de Admin
```

---

## 7. COMPONENTES LIVEWIRE

Livewire permite crear componentes interactivos sin escribir JavaScript.

### AdminLoanManagement.php
```php
class AdminLoanManagement extends Component
{
    public $search = '';      // Búsqueda en tiempo real
    public $filterStatus = ''; // Filtro por estado
    
    public function deliver($loanId)  // Entregar libro
    public function receive($loanId)  // Recibir libro
    public function cancel($loanId)   // Cancelar préstamo
    
    public function render()
    {
        // Consulta con filtros
        $loans = Prestamo::query()
            ->when($this->search, fn($q) => $q->where(...))
            ->paginate(10);
            
        return view('livewire.admin-loan-management', compact('loans'));
    }
}
```

### ¿Cómo funciona Livewire?
1. Usuario escribe en input → `wire:model="search"`
2. Laravel recibe el cambio
3. Ejecuta `render()` con nueva data
4. Actualiza solo la parte del HTML que cambió

---

## 8. MIDDLEWARE DE SEGURIDAD

### CheckRole.php
```php
public function handle($request, Closure $next, ...$roles)
{
    if (!auth()->user()->hasRole($roles)) {
        abort(403); // Acceso denegado
    }
    return $next($request);
}
```

### CheckStudentRestrictions.php
```php
// Si estudiante tiene multas pendientes o préstamos vencidos:
// - Solo puede acceder a: catálogo, repositorio, multas
// - No puede solicitar nuevos préstamos
```

---

## 9. CONTROLADORES PRINCIPALES

### LoanController.php
```php
public function store(Request $request)
{
    // Validaciones
    $user = User::find($request->user_id);
    
    // 1. ¿Usuario bloqueado?
    if ($user->blocked_for_loans) {
        return back()->withErrors('Usuario bloqueado');
    }
    
    // 2. ¿Tiene multas pendientes?
    if ($user->multas()->where('status', 'pendiente')->exists()) {
        return back()->withErrors('Tiene multas pendientes');
    }
    
    // 3. ¿Límite de préstamos? (máx 3)
    if ($user->prestamos()->where('status', 'activo')->count() >= 3) {
        return back()->withErrors('Máximo de préstamos alcanzado');
    }
    
    // 4. Crear préstamo
    if (auth()->user()->hasRole('Admin')) {
        // Admin: préstamo directo (ya entregado)
        $prestamo = Prestamo::create([
            'status' => 'activo',
            'approval_status' => 'collected',
            'fecha_recogida' => now(),
        ]);
    } else {
        // Estudiante: solicitud pendiente
        $prestamo = Prestamo::create([
            'status' => 'pendiente',
            'approval_status' => 'pending',
        ]);
    }
}
```

---

## 10. VISTAS Y BLADE

### Layout Principal (app.blade.php)
```html
<!-- Sidebar para Admin/Trabajador -->
<nav>
    <a href="/materials">📚 Materiales</a>
    <a href="/loans">📖 Préstamos</a>
    <a href="/fines">💰 Multas</a>
    @if(auth()->user()->hasRole('Admin'))
        <a href="/reports">📊 Reportes</a>
    @endif
</nav>

<!-- Contenido -->
<main>
    @yield('content')
</main>

<!-- Alertas para estudiantes -->
@if($hasOverdueLoans)
    <div class="alert">⚠️ Tienes préstamos vencidos</div>
@endif
```

### Componente Livewire en vista
```html
<input wire:model.live="search" placeholder="Buscar...">

@foreach($loans as $loan)
    <tr>
        <td>{{ $loan->user->name }}</td>
        <td>{{ $loan->material->title }}</td>
        <td>
            @if($loan->status === 'pendiente_recogida')
                <button wire:click="deliver({{ $loan->id }})">
                    Entregar
                </button>
            @endif
        </td>
    </tr>
@endforeach
```

---

## 11. DESPLIEGUE

### Docker (docker-compose.yml)
```yaml
services:
  app:
    build: .
    ports:
      - "8080:80"
    environment:
      - DB_CONNECTION=pgsql
      - DATABASE_URL=...
    depends_on:
      - db
  
  db:
    image: postgres:15
    environment:
      - POSTGRES_DB=biblioteca
```

### Variables de Entorno (.env)
```
APP_NAME="Biblioteca Pedro P. Díaz"
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql  # o pgsql
DB_HOST=localhost
DB_DATABASE=biblioteca
DB_USERNAME=root
DB_PASSWORD=
```

---

## 12. COMANDOS ÚTILES

```bash
# Desarrollo local
php artisan serve              # Iniciar servidor
php artisan migrate            # Ejecutar migraciones
php artisan db:seed            # Ejecutar seeders

# Cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Permisos
php artisan permission:cache-reset

# Docker
docker-compose up --build      # Levantar contenedores
docker-compose down            # Detener contenedores
```

---

## 13. CREDENCIALES DE PRUEBA

| Usuario | Email | Contraseña | Rol |
|---------|-------|------------|-----|
| Admin | admin@iestp.edu.pe | password | Admin |
| Trabajador | trabajador@iestp.edu.pe | password | Trabajador |
| Estudiante | estudiante@iestp.edu.pe | password | Estudiante |
| Jefe | jefe@iestp.edu.pe | password | Jefe_Area |

---

## 14. PREGUNTAS FRECUENTES

### ¿Cómo funciona la autenticación?
Laravel usa **sessions** y **cookies**. El usuario inicia sesión con email/password, se crea una sesión, y se usa el middleware `auth` para proteger rutas.

### ¿Cómo se generan las multas automáticamente?
En el modelo `Prestamo`, hay un método `calcularMultaPorRetraso()` que calcula S/. 1.00 por cada día de retraso. Se llama cuando se devuelve un libro tardío.

### ¿Por qué usamos Livewire en lugar de Vue/React?
Livewire permite crear interfaces interactivas usando solo PHP y Blade, sin necesidad de API REST ni JavaScript complejo. Es más rápido para desarrollar y más fácil de mantener.

### ¿Cómo funciona el sistema de permisos?
Usamos **Spatie Laravel Permission**. Los usuarios tienen roles, y los roles tienen permisos. En las rutas usamos `->middleware('permission:nombre_permiso')` para proteger acceso.

---

**Autor:** Sistema generado con Laravel 12  
**Fecha:** Diciembre 2024
