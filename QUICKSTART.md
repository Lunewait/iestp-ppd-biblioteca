# IESTP Library Platform - Quick Start Guide

## 🚀 Start Here

Welcome! This guide will get you up and running with the IESTP Hybrid Library Platform in minutes.

---

## Step 1: Initial Setup (One Time)

### 1.1 Navigate to Project
```powershell
cd C:\Users\Diurno\Documents\Efsrt\iestp-library
```

### 1.2 Install Dependencies
```powershell
npm install
```

### 1.3 Configure Database
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=iestp_library      # Change if needed
DB_USERNAME=root               # Your MySQL user
DB_PASSWORD=                   # Your MySQL password
```

### 1.4 Create Database
```powershell
# Using MySQL client or phpMyAdmin, create database:
CREATE DATABASE iestp_library;
```

### 1.5 Run Migrations & Seeding
```powershell
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
```

This creates:
- All database tables
- 4 roles (Admin, Trabajador, Estudiante, Jefe_Area)
- 24 permissions
- 4 demo users

---

## Step 2: Run the Application

### 2.1 Start Development Server
```powershell
php artisan serve
```

Server will be available at: `http://localhost:8000`

### 2.2 In Another Terminal, Start Frontend Build
```powershell
npm run dev
```

---

## Step 3: Login with Demo Accounts

### Admin Account
- Email: `admin@iestp.local`
- Password: `password`
- Access: Full system control

### Worker Account
- Email: `trabajador@iestp.local`
- Password: `password`
- Access: Loan/return operations, inventory management

### Student Account
- Email: `estudiante@iestp.local`
- Password: `password`
- Access: Search materials, submit documents

### Area Head Account
- Email: `jefe@iestp.local`
- Password: `password`
- Access: Approve documents, manage repository

---

## Common Tasks

### Run Tests
```powershell
php artisan test
```

### Create New Material
```powershell
php artisan tinker
>>> $material = App\Models\Material::create([
    'title' => 'Sample Book',
    'author' => 'John Doe',
    'type' => 'fisico',
    'code' => 'BOOK-001'
]);
>>> $material->materialFisico()->create([
    'isbn' => '978-0-123456-78-9',
    'stock' => 5,
    'available' => 5
]);
```

### Create New User
```powershell
php artisan tinker
>>> $user = App\Models\User::create([
    'name' => 'New User',
    'email' => 'newuser@iestp.local',
    'institutional_email' => 'newuser@iestp.edu.pe',
    'password' => bcrypt('password')
]);
>>> $user->assignRole('Estudiante');
```

### Reset Database
```powershell
php artisan migrate:fresh --seed --seeder=RolePermissionSeeder
```

---

## 📁 Where to Find Things

### Models
Location: `app/Models/`
- User.php
- Material.php
- MaterialFisico.php
- MaterialDigital.php
- Prestamo.php (Loans)
- Multa.php (Fines)
- Reserva.php
- RepositorioDocumento.php
- Aprobacion.php

### Controllers
Location: `app/Http/Controllers/`
- MaterialController.php
- LoanController.php
- RepositoryController.php

### Routes
Location: `routes/web.php`
All routes defined here

### Tests
Location: `tests/`
- tests/Unit/ - Model tests
- tests/Feature/ - Authorization tests

### Documentation
- `DOCUMENTATION.md` - User & developer guide
- `IMPLEMENTATION_GUIDE.md` - Technical architecture
- `IMPLEMENTATION_SUMMARY.md` - What's been built

---

## 🎯 Quick Feature Demo

### Test Material Creation (As Admin)
1. Login as admin@iestp.local
2. (Next: Create Blade template for /materials/create)
3. Fill in material details
4. Select type (fisico, digital, or hibrido)
5. Add physical details (ISBN, stock) or digital (URL)

### Test Loan System (As Worker)
1. Login as trabajador@iestp.local
2. (Next: Create Blade template for /loans/create)
3. Select student and material
4. Set due date
5. System checks availability and fines
6. Loan registered, inventory updated

### Test Repository (As Student)
1. Login as estudiante@iestp.local
2. (Next: Create Blade template for /repository/create)
3. Upload thesis/research document
4. Fill in title, author, description
5. Submit for approval
6. Area heads review and approve
7. Document published to repository

---

## 🔍 Testing the API (Using Postman or cURL)

### Get All Materials
```powershell
curl -H "Authorization: Bearer YOUR_TOKEN" http://localhost:8000/materials
```

### Create Loan
```powershell
curl -X POST http://localhost:8000/loans \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "user_id": 3,
    "material_id": 1,
    "fecha_devolucion_esperada": "2025-12-25"
  }'
```

---

## 🛠️ Development Workflow

### Create New Feature
1. Create Migration: `php artisan make:migration`
2. Create Model: `php artisan make:model YourModel`
3. Create Controller: `php artisan make:controller YourController`
4. Write Tests: Create in `tests/Feature/` or `tests/Unit/`
5. Add Routes: Edit `routes/web.php`

### Build Frontend
Create Blade templates in `resources/views/`:
```
resources/views/
├── materials/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── show.blade.php
│   └── edit.blade.php
├── loans/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── show.blade.php
│   └── return.blade.php
└── repository/
    ├── index.blade.php
    ├── create.blade.php
    ├── show.blade.php
    └── approve.blade.php
```

---

## 📚 Useful Commands

```powershell
# Start development server
php artisan serve

# Run frontend dev server
npm run dev

# Run production build
npm run build

# Run all tests
php artisan test

# Run specific test
php artisan test tests/Unit/MaterialModelTest.php

# Interactive shell
php artisan tinker

# View database contents
php artisan tinker
>>> App\Models\Material::all();
>>> App\Models\User::all();

# Clear cache
php artisan cache:clear
php artisan config:cache

# Generate app key
php artisan key:generate

# Fresh migrations
php artisan migrate:fresh --seed --seeder=RolePermissionSeeder
```

---

## 🐛 Troubleshooting

### "SQLSTATE[HY000]: General error"
- Check `.env` database credentials
- Ensure MySQL is running
- Check database exists: `CREATE DATABASE iestp_library;`

### "TokenMismatchException" on forms
- Clear cache: `php artisan cache:clear`
- Ensure CSRF token in forms: `@csrf`

### Models not found
- Run: `composer dump-autoload`
- Check namespace in `config/app.php`

### Migrations already exist error
- Check `database/migrations/` directory
- Don't create duplicate migrations

### Permission denied errors
- Check user role: `auth()->user()->getRoleNames()`
- Verify permission: `auth()->user()->hasPermissionTo('permission_name')`

---

## 📖 Learn More

- **DOCUMENTATION.md** - Comprehensive user & developer guide
- **IMPLEMENTATION_GUIDE.md** - Technical architecture details
- **IMPLEMENTATION_SUMMARY.md** - What's been implemented
- Laravel Docs: https://laravel.com/docs/11.x
- Spatie Permissions: https://spatie.be/docs/laravel-permission/v6/introduction

---

## ✅ Verification Checklist

After setup, verify everything works:

- [ ] Created database `iestp_library`
- [ ] Ran migrations successfully
- [ ] Seeded demo data
- [ ] Server runs on http://localhost:8000
- [ ] Can login as admin@iestp.local
- [ ] Can login as trabajador@iestp.local
- [ ] Can login as estudiante@iestp.local
- [ ] Can view materials (authenticated)
- [ ] Tests run without errors
- [ ] No console errors in browser

---

## 🎓 Next Steps

1. **Create Blade Templates** for Material, Loan, and Repository interfaces
2. **Implement Frontend** with Tailwind CSS
3. **Add Form Validation** messages
4. **Create Admin Dashboard** for statistics
5. **Test All Workflows** with different users
6. **Deploy to Production** using deployment checklist

---

## 📞 Support

For detailed information on any feature, refer to:
- `IMPLEMENTATION_GUIDE.md` - Technical details
- `DOCUMENTATION.md` - Features and usage
- Code comments in controllers and models

---

**Happy Coding! 🚀**
