# IESTP Hybrid Library Platform

A modern Laravel 11 application for managing physical and digital library materials, loans, fines, and user roles in an educational institution.

## Features

### 1. Unified Catalog
- Search physical and digital materials
- Filter by type (physical, digital, hybrid)
- View material details and availability
- Keyword-based search

### 2. Loans Management
- Register book loans with due dates
- Track active, returned, and overdue loans
- Automatic fine calculation for overdue returns
- Prevent loans for users with unpaid fines
- Inventory tracking for physical materials

### 3. Digital Repository
- Students can submit thesis, research, and final work documents
- Multi-level approval workflow (Area heads approval required)
- Published documents are searchable and downloadable
- Download tracking and statistics
- Document versioning support

### 4. Role-Based Access Control
- **Admin**: Full system access
- **Trabajador (Worker)**: Loan/return registration, inventory management, fine processing
- **Estudiante (Student)**: Search materials, reserve items, submit documents
- **Jefe_Area (Area Head)**: Manage repositories, approve documents

### 5. Fines System
- Automatic fine generation for overdue loans ($1.50/day)
- Manual fine creation
- Fine payment tracking
- Forgiveness capability for admins

## Installation

### Prerequisites
- PHP 8.2+
- Composer
- MySQL/MariaDB
- Node.js & npm

### Setup Steps

```bash
# 1. Clone or create the project
cd path/to/project

# 2. Install dependencies
composer install
npm install

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Setup database
# Edit .env with your database credentials
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder

# 5. Build frontend assets
npm run build

# 6. Start development server
php artisan serve
```

## Database Schema

### Core Tables

#### users
- Standard authentication + `institutional_email`, `role` (via Spatie)

#### materials
- `id`, `title`, `author`, `description`, `type` (fisico/digital/hibrido), `code` (unique), `keywords`

#### material_fisicos
- `id`, `material_id`, `isbn`, `stock`, `available`, `publisher`, `publication_year`, `location`

#### material_digitals
- `id`, `material_id`, `url`, `downloadable`, `file_path`, `file_type`, `access_count`, `license`

#### prestamos (Loans)
- `id`, `user_id`, `material_id`, `fecha_prestamo`, `fecha_devolucion_esperada`, `fecha_devolucion_actual`, `status` (activo/devuelto/vencido), `registrado_por` (worker id), `notas`

#### multas (Fines)
- `id`, `prestamo_id`, `user_id`, `monto`, `razon`, `status` (pendiente/pagada/condonada), `fecha_vencimiento`, `fecha_pago`, `registrado_por`

#### reservas (Reservations)
- `id`, `user_id`, `material_id`, `fecha_reserva`, `fecha_expiracion`, `status` (activa/cancelada/recogida), `posicion_cola`

#### repositorio_documentos
- `id`, `user_id`, `titulo`, `descripcion`, `autor`, `tipo` (tesis/investigacion/trabajo_final/otro), `file_path`, `status` (pendiente/aprobado/rechazado/publicado), `revisado_por`, `fecha_revision`, `descargas`

#### aprobaciones (Approvals)
- `id`, `documento_id`, `jefe_area_id`, `estado` (pendiente/aprobado/rechazado), `comentarios`, `fecha_revision`

## API Routes

### Materials
- `GET /materials` - List all materials
- `GET /materials/{material}` - View material details
- `POST /materials` - Create material (Admin/Jefe_Area)
- `PUT /materials/{material}` - Update material (Admin/Jefe_Area)
- `DELETE /materials/{material}` - Delete material (Admin)

### Loans
- `GET /loans` - List loans
- `GET /loans/{loan}` - View loan details
- `POST /loans` - Create loan (Trabajador)
- `GET /loans/{loan}/return` - Return loan form (Trabajador)
- `POST /loans/{loan}/return` - Process return (Trabajador)

### Repository
- `GET /repository` - List published documents
- `POST /repository` - Submit document (Estudiante)
- `GET /repository/{documento}` - View document
- `GET /repository/{documento}/approve` - Approval form (Jefe_Area)
- `POST /repository/{documento}/approve` - Process approval (Jefe_Area)
- `GET /repository/{documento}/download` - Download document

## Testing

### Run Unit Tests
```bash
php artisan test tests/Unit/MaterialModelTest.php
php artisan test tests/Unit/PrestamoModelTest.php
```

### Run Feature Tests
```bash
php artisan test tests/Feature/AuthorizationTest.php
```

### All Tests
```bash
php artisan test
```

## Verification Checklist

### Manual Testing

#### Admin Verification
- [ ] Admin can create/edit users
- [ ] Admin can manage system settings
- [ ] Admin can view all loans and fines
- [ ] Admin can forgive fines

#### Worker Verification
- [ ] Worker can register loans
- [ ] Worker can process returns
- [ ] Worker can create fines for overdue loans
- [ ] Worker cannot delete materials

#### Student Verification
- [ ] Student can search materials
- [ ] Student can view digital content
- [ ] Student can submit documents to repository
- [ ] Student cannot access admin routes
- [ ] Student can view their own loan history

#### Route Protection
- [ ] Unauthenticated users redirected to login
- [ ] Students cannot access loan creation
- [ ] Students cannot access document approval
- [ ] Trabajador cannot manage users
- [ ] Proper 403 responses for unauthorized access

### Automated Testing
- Material model tests verify relationships
- Loan overdue detection works correctly
- Availability checking for physical materials
- Authorization tests for role-based access

## Technologies Used

- **Framework**: Laravel 11
- **Database**: MySQL
- **Auth**: Laravel Breeze + Spatie Permissions
- **Frontend**: Blade + Tailwind CSS
- **PDF**: DOMPDF
- **Excel**: Maatwebsite Excel
- **Testing**: PHPUnit + Pest

## Folder Structure

```
iestp-library/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── MaterialController.php
│   │   │   ├── LoanController.php
│   │   │   └── RepositoryController.php
│   │   └── Middleware/
│   │       ├── CheckRole.php
│   │       └── CheckPermission.php
│   └── Models/
│       ├── User.php
│       ├── Material.php
│       ├── Prestamo.php
│       ├── Multa.php
│       └── RepositorioDocumento.php
├── database/
│   ├── migrations/
│   │   ├── create_materials_table.php
│   │   ├── create_transactions_table.php
│   │   └── create_repository_table.php
│   ├── seeders/
│   │   └── RolePermissionSeeder.php
│   └── factories/
│       ├── MaterialFactory.php
│       └── PrestamoFactory.php
├── routes/
│   └── web.php
└── tests/
    ├── Unit/
    │   ├── MaterialModelTest.php
    │   └── PrestamoModelTest.php
    └── Feature/
        └── AuthorizationTest.php
```

## Configuration

### Environment Variables
Update `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=iestp_library
DB_USERNAME=root
DB_PASSWORD=

APP_NAME="IESTP Library"
APP_DEBUG=true
```

### Mail Configuration
For fine notifications, configure mail in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

## Development

### Create New Controller
```bash
php artisan make:controller YourController
```

### Create New Migration
```bash
php artisan make:migration create_your_table
```

### Create New Model
```bash
php artisan make:model YourModel -m
```

## Security Considerations

1. **Authentication**: All routes require authentication except login/register
2. **Authorization**: Role-based middleware prevents unauthorized access
3. **File Uploads**: Stored outside public folder, requires authentication to download
4. **CSRF Protection**: Enabled on all forms
5. **Query Injection**: Uses Laravel's query builder with parameter binding

## Future Enhancements

- Email notifications for loan reminders and approvals
- SMS notifications for fines
- QR code scanning for quick loans
- Integration with institutional student system
- Advanced analytics and reporting
- Mobile app for students
- Automated fine generation job
- Document versioning in repository

## Support & Maintenance

For bugs or feature requests, create a new issue in your project management system.

## License

This project is proprietary to IESTP.
