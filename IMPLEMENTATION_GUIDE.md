# IESTP Library Platform - Implementation Guide

## Project Structure Overview

This document outlines the complete structure and implementation of the IESTP Hybrid Library Platform.

## 1. Authentication & Authorization System

### Roles Configured
- **Admin**: Full system access
- **Trabajador**: Library staff operations
- **Estudiante**: Student/user operations  
- **Jefe_Area**: Repository management

### Permissions Matrix

| Permission | Admin | Trabajador | Estudiante | Jefe_Area |
|-----------|-------|-----------|-----------|-----------|
| view_materials | ✓ | ✓ | ✓ | ✓ |
| create_material | ✓ | | | ✓ |
| edit_material | ✓ | | | ✓ |
| manage_inventory | ✓ | ✓ | | ✓ |
| create_loan | ✓ | ✓ | | |
| return_loan | ✓ | ✓ | | |
| view_fines | ✓ | ✓ | ✓ | ✓ |
| submit_document | ✓ | | ✓ | ✓ |
| approve_document | ✓ | | | ✓ |
| manage_users | ✓ | | | |

## 2. Database Schema Details

### Material Management System

```
materials (Base table)
├── material_fisicos (Physical attributes)
│   └── ISBN, Stock, Location, Publisher
└── material_digitals (Digital attributes)
    └── URL, Download permission, File type
```

**Material Types**:
- `fisico`: Physical books/materials only
- `digital`: Digital documents/resources only
- `hibrido`: Both physical and digital available

### Transaction Management

```
prestamos (Loans)
├── Links to users (borrower)
├── Links to materials
├── Tracks status: activo, devuelto, vencido
└── Triggers multas on overdue

multas (Fines)
├── Links to prestamos
├── Links to users
├── Automatic calculation: $1.50/day late
└── Status tracking: pendiente, pagada, condonada
```

### Repository System

```
repositorio_documentos
├── Submitted by Estudiante/Trabajador
├── Status: pendiente → aprobado/rechazado → publicado
└── Multi-approval workflow

aprobaciones
├── Each Jefe_Area must approve
├── Unanimous approval required
└── Comments and timestamp tracking
```

## 3. Controllers Overview

### MaterialController
**Endpoints**:
- `index()` - Catalog with search/filter
- `create()/store()` - Admin/Jefe_Area only
- `show()` - Public view
- `edit()/update()` - Admin/Jefe_Area
- `destroy()` - Admin only

**Features**:
- Full-text search on title, author, code
- Type filtering (fisico/digital/hibrido)
- Pagination with 15 items per page
- Creates both fisico and digital records as needed

### LoanController
**Endpoints**:
- `index()` - Loan history listing
- `create()/store()` - Register new loan
- `show()` - Loan details
- `returnForm()/return()` - Process return

**Business Logic**:
- Validates material availability
- Checks for unpaid fines
- Decrements physical stock
- Auto-generates fines for overdue (calculated days late × $1.50)

### RepositoryController
**Endpoints**:
- `index()` - List published documents
- `create()/store()` - Student submission
- `show()` - Document view/download
- `approve()` / `processApproval()` - Area head approval

**Workflow**:
1. Student submits document (status: pendiente)
2. System creates approval records for all Jefe_Area
3. Each Jefe_Area reviews and approves/rejects
4. Document becomes publicado when all approve
5. Rejected documents return to student with comments

## 4. Models & Relationships

### User Model
```php
User → Prestamos (hasMany)
User → Reservas (hasMany)
User → Multas (hasMany)
User → Documentos (hasMany)
// Spatie Roles included
```

### Material Model
```php
Material → MaterialFisico (hasOne)
Material → MaterialDigital (hasOne)
Material → Prestamos (hasMany)
Material → Reservas (hasMany)

// Methods
isAvailable() // Checks stock or is digital
```

### Prestamo Model
```php
Prestamo → Usuario (belongsTo User)
Prestamo → Material (belongsTo)
Prestamo → RegistradoPor (belongsTo User)
Prestamo → Multas (hasMany)

// Methods
isOverdue() // Checks if status=activo AND now > fecha_devolucion
```

### RepositorioDocumento Model
```php
RepositorioDocumento → Usuario (belongsTo)
RepositorioDocumento → RevisadoPor (belongsTo User)
RepositorioDocumento → Aprobaciones (hasMany)
```

## 5. Middleware Implementation

### CheckRole Middleware
```php
// Usage: middleware('role:Admin,Trabajador')
// Allows multiple role check (OR logic)
// Returns 401 if unauthenticated
// Returns 403 if role mismatch
```

### CheckPermission Middleware
```php
// Usage: middleware('permission:create_loan,manage_inventory')
// Checks Spatie permissions
// Uses auth()->user()->hasPermissionTo()
// Returns 403 if permission denied
```

**Registered in bootstrap/app.php**:
```php
$middleware->alias([
    'role' => CheckRole::class,
    'permission' => CheckPermission::class,
]);
```

## 6. Routes Configuration

### Protected Routes Structure
```php
Route::middleware(['auth'])->group(function () {
    // Materials (index, show - public to auth users)
    Route::resource('materials', MaterialController)
        ->only(['index', 'show']);
    
    // Materials (create, edit, delete - permission based)
    Route::resource('materials', MaterialController)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware('permission:create_material');
    
    // Loans
    Route::resource('loans', LoanController)
        ->only(['create', 'store'])
        ->middleware('permission:create_loan');
    
    // Repository
    Route::post('/repository', [RepositoryController::class, 'store'])
        ->middleware('permission:submit_document');
});
```

## 7. Testing Strategy

### Unit Tests
**Location**: `tests/Unit/`

- `MaterialModelTest.php`
  - Test relationships (hasOne, hasMany)
  - Test availability checking
  - Test physical/digital type differentiation

- `PrestamoModelTest.php`
  - Test user/material relationships
  - Test overdue calculation
  - Test status tracking

### Feature Tests
**Location**: `tests/Feature/`

- `AuthorizationTest.php`
  - Test role-based access (Student can't create loan)
  - Test permission-based access
  - Test authentication requirement
  - Test 403 responses for unauthorized

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test tests/Unit/MaterialModelTest.php

# Run with coverage
php artisan test --coverage
```

## 8. Seeding Initial Data

### RolePermissionSeeder
Automatically runs with `php artisan db:seed --class=RolePermissionSeeder`

Creates:
- All roles: Admin, Trabajador, Estudiante, Jefe_Area
- All permissions (24 total)
- Demo users:
  - admin@iestp.local / password
  - trabajador@iestp.local / password
  - estudiante@iestp.local / password
  - jefe@iestp.local / password

## 9. Verification Procedures

### Admin Role Verification
```
1. Login as admin@iestp.local
2. Navigate to users management (if implemented)
3. Verify can create/edit users
4. Check can access all materials operations
5. Confirm can view and manage all fines
```

### Worker Role Verification
```
1. Login as trabajador@iestp.local
2. Navigate to /loans/create
3. Verify can register new loan
4. Complete loan and test return process
5. Verify automatic fine creation for overdue
6. Check cannot access material create
```

### Student Role Verification
```
1. Login as estudiante@iestp.local
2. Visit /materials (view catalog)
3. Verify search functionality
4. Check cannot access /loans/create
5. Navigate to /repository/create
6. Upload sample document
7. Verify appears in pending status
```

### Route Protection Verification
```
1. Test unauthenticated access → redirect to login
2. Test Student accessing /loans/create → 403
3. Test Student accessing /materials/create → 403
4. Test proper role error messages
5. Test permission cascading (Admin can do all)
```

## 10. Key Implementation Details

### Fine Calculation
```php
// In LoanController::return()
if ($loan->isOverdue()) {
    $daysLate = now()->diffInDays($loan->fecha_devolucion_esperada);
    $fineAmount = $daysLate * 1.50; // $1.50 per day
    
    Multa::create([
        'prestamo_id' => $loan->id,
        'user_id' => $loan->user_id,
        'monto' => $fineAmount,
        'razon' => "Devolución tardía ({$daysLate} días)",
        'status' => 'pendiente',
        'registrado_por' => auth()->id(),
    ]);
}
```

### Repository Approval Workflow
```php
// Approval logic in RepositoryController::processApproval()
1. Find/create approval for current user
2. Update approval with estado and timestamp
3. Check if ALL Jefe_Area approvals are positive
4. If unanimous: status = 'publicado'
5. If any rejected: status = 'rechazado'
```

### Inventory Management
```php
// On loan creation: material->materialFisico->decrement('available')
// On loan return: material->materialFisico->increment('available')
// Check availability: material->isAvailable()
```

## 11. File Structure Reference

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── MaterialController.php      (CRUD + Search)
│   │   ├── LoanController.php          (Loan management)
│   │   └── RepositoryController.php    (Repository + Approvals)
│   └── Middleware/
│       ├── CheckRole.php               (Role validation)
│       └── CheckPermission.php         (Permission validation)
└── Models/
    ├── User.php                        (+ Spatie roles)
    ├── Material.php                    (Base material)
    ├── MaterialFisico.php              (Physical details)
    ├── MaterialDigital.php             (Digital details)
    ├── Prestamo.php                    (Loan tracking)
    ├── Multa.php                       (Fines)
    ├── Reserva.php                     (Reservations)
    ├── RepositorioDocumento.php        (Submitted documents)
    └── Aprobacion.php                  (Document approvals)

database/
├── migrations/
│   ├── *_create_materials_table.php
│   ├── *_create_transactions_table.php
│   └── *_create_repository_table.php
├── seeders/
│   └── RolePermissionSeeder.php        (Roles + Permissions + Demo Users)
└── factories/
    ├── UserFactory.php                 (+ institutional_email)
    ├── MaterialFactory.php
    └── PrestamoFactory.php

tests/
├── Unit/
│   ├── MaterialModelTest.php
│   └── PrestamoModelTest.php
└── Feature/
    └── AuthorizationTest.php
```

## 12. API Response Format

All endpoints return appropriate HTTP status codes:
- **200**: Success (GET, PUT)
- **201**: Created (POST)
- **302**: Redirect (after action)
- **401**: Unauthorized
- **403**: Forbidden (role/permission denied)
- **404**: Not Found
- **422**: Validation error

## 13. Next Steps for Frontend Development

1. Create Blade templates in `resources/views/`
2. Install Tailwind CSS for styling
3. Implement form validation messages
4. Add pagination views
5. Create admin dashboard
6. Build worker loan interface
7. Create student document upload form
8. Implement approval workflow UI

## 14. Deployment Checklist

- [ ] Run `composer install --optimize-autoloader`
- [ ] Run `npm run build` (production)
- [ ] Set `APP_DEBUG=false` in .env
- [ ] Run migrations on production database
- [ ] Run `php artisan db:seed --class=RolePermissionSeeder`
- [ ] Create admin user via tinker or migration
- [ ] Setup queue worker for notifications
- [ ] Configure email service
- [ ] Enable HTTPS/SSL
- [ ] Setup backup strategy
- [ ] Monitor error logs
