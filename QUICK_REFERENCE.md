# Quick Reference Guide

**Last Updated**: November 25, 2025

---

## 🚀 Start Development Session

### 1. Open Project
```bash
cd c:\Users\Maria\Documents\iestp-library
```

### 2. Start Server
```bash
php artisan serve
# Server runs at: http://localhost:8000
```

### 3. Access Application
```
URL: http://localhost:8000
```

### 4. Run Tests (Verify Everything)
```bash
php artisan test
# Expected: 13 passed, 100% success
```

---

## 👤 Demo User Credentials

### Admin User
```
Email: admin@iestp.local
Role: Admin
Access: Full system access
```

### Worker
```
Email: trabajador@iestp.local
Role: Trabajador
Access: Loan management, material creation
```

### Student
```
Email: estudiante@iestp.local
Role: Estudiante
Access: Material viewing, document submission
```

### Area Head
```
Email: jefe@iestp.local
Role: Jefe_Area
Access: Document approval, worker management
```

Password for all: `password`

---

## 📁 Important Directories

### Frontend (Web)
```
resources/views/
├── auth/
├── layouts/
├── dashboard.blade.php
├── materials/
├── loans/
├── fines/
├── reservations/
├── repository/
└── users/
```

### Backend (API & Logic)
```
app/Http/
├── Controllers/
├── Requests/         (Form validations)
├── Resources/        (API responses)
└── Middleware/

app/Models/           (Database models)
```

### Routes
```
routes/
├── web.php          (Web routes)
├── api.php          (API routes - NEW)
└── auth.php         (Auth routes)
```

### Database
```
database/
├── migrations/      (Table structures)
├── seeders/         (Initial data)
└── factories/       (Test data)
```

### Tests
```
tests/
├── Unit/            (Model tests)
└── Feature/         (Controller tests)
```

---

## 🔧 Common Commands

### Database
```bash
# Run migrations
php artisan migrate

# Reset database (careful!)
php artisan migrate:refresh --seed

# Seed data
php artisan db:seed
```

### Cache & Config
```bash
# Clear all caches
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Optimize
php artisan optimize
```

### Code Quality
```bash
# Run tests
php artisan test

# Run specific test file
php artisan test tests/Feature/AuthorizationTest.php

# Run tests with coverage (if configured)
php artisan test --coverage
```

### Artisan Helpers
```bash
# Generate API token
$token = User::find(1)->createToken('api-token')->plainTextToken;

# List all routes
php artisan route:list

# List all permissions
php artisan permission:list
```

---

## 📝 File Locations

### Configuration
- `.env` - Environment variables (database, mail, etc.)
- `phpunit.xml` - Test configuration
- `composer.json` - PHP dependencies
- `package.json` - Node dependencies

### Key Controllers
- `app/Http/Controllers/MaterialController.php`
- `app/Http/Controllers/LoanController.php`
- `app/Http/Controllers/FineController.php`
- `app/Http/Controllers/RepositoryController.php`
- `app/Http/Controllers/ReservationController.php`

### Key Models
- `app/Models/User.php`
- `app/Models/Material.php`
- `app/Models/Prestamo.php` (Loan)
- `app/Models/Multa.php` (Fine)
- `app/Models/Reserva.php`

---

## 🐛 Troubleshooting

### Database Connection Error
```bash
# Check .env file
cat .env

# Verify MySQL is running
mysql -u root -p

# Reset database
php artisan migrate:refresh --seed
```

### Server Not Starting
```bash
# Clear cache
php artisan cache:clear

# Regenerate autoloader
composer dump-autoload

# Try again
php artisan serve
```

### Tests Failing
```bash
# Refresh test database
php artisan migrate:refresh --env=testing

# Run tests with verbose output
php artisan test --verbose

# Run single test
php artisan test tests/Feature/AuthorizationTest.php
```

### Permission Denied
```bash
# Fix file permissions (on Linux/Mac)
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# On Windows, set proper directory permissions
```

---

## 📊 Database Schema Quick Reference

### Users Table
```
- id
- name
- email (unique)
- institutional_email (unique)
- password (hashed)
- roles (via spatie/laravel-permission)
```

### Materials Table
```
- id
- title
- author
- description
- type (fisico, digital, hibrido)
- code (unique)
- keywords
```

### Prestamos Table (Loans)
```
- id
- user_id → users
- material_id → materials
- fecha_prestamo
- fecha_devolucion_esperada
- fecha_devolucion_actual
- status (activo, devuelto)
- registrado_por → users
```

### Multas Table (Fines)
```
- id
- user_id → users
- prestamo_id → prestamos
- monto (amount)
- razon (reason)
- status (pendiente, pagada, condonada)
- fecha_pago
- registrado_por → users
```

### Reservas Table
```
- id
- user_id → users
- material_id → materials
- fecha_reserva_esperada
- estado (activa, completada, cancelada)
- posicion (queue position)
```

---

## 🔐 Security Checklist

Before deploying to production:

- [ ] Change all demo user passwords
- [ ] Set `APP_DEBUG=false` in .env
- [ ] Generate new `APP_KEY`
- [ ] Setup proper file permissions
- [ ] Configure HTTPS/SSL
- [ ] Setup email notifications
- [ ] Configure backup strategy
- [ ] Review security headers
- [ ] Setup firewall rules
- [ ] Enable CSRF protection (already done)
- [ ] Validate all input (already configured)
- [ ] Use strong database password

---

## 📚 Documentation Files

| File | Purpose |
|------|---------|
| `START_HERE.md` | Start here first! |
| `QUICKSTART.md` | 5-minute setup |
| `DOCUMENTATION.md` | Complete features |
| `IMPLEMENTATION_GUIDE.md` | Technical details |
| `BACKEND_ENHANCEMENTS.md` | API documentation |
| `FINAL_STATUS_REPORT.md` | Project status |
| `PROJECT_OVERVIEW.md` | Project overview |
| `README.md` | GitHub README |

---

## 🌐 API Quick Reference

### Base URL
```
http://localhost:8000/api
```

### Authentication Header
```
Authorization: Bearer {TOKEN}
Accept: application/json
```

### Example Requests

**Get Materials**
```bash
curl -H "Authorization: Bearer {TOKEN}" \
     http://localhost:8000/api/materials
```

**Create Loan**
```bash
curl -X POST http://localhost:8000/api/loans \
     -H "Authorization: Bearer {TOKEN}" \
     -H "Content-Type: application/json" \
     -d '{
       "user_id": 3,
       "material_id": 1,
       "fecha_devolucion_esperada": "2025-12-25"
     }'
```

**Get User Info**
```bash
curl -H "Authorization: Bearer {TOKEN}" \
     http://localhost:8000/api/user
```

---

## ⚡ Performance Tips

### Database Optimization
```php
// Good - uses eager loading
$materials = Material::with('materialFisico', 'materialDigital')->paginate();

// Bad - causes N+1 queries
$materials = Material::paginate();
foreach ($materials as $material) {
    echo $material->materialFisico->stock; // Extra query for each!
}
```

### Caching
```php
// Cache user summary
$summary = Cache::remember("user.{$user->id}.summary", 3600, function() use ($user) {
    return $user->getSummary();
});
```

### Pagination
```php
// Always paginate large datasets
$materials = Material::paginate(15);
// Don't use get() on large tables
// $materials = Material::get(); // ❌ Bad for performance
```

---

## 🎯 Development Workflow

### 1. Start Session
```bash
cd c:\Users\Maria\Documents\iestp-library
php artisan serve
```

### 2. Make Changes
```bash
# Edit controllers, models, views, etc.
# Changes reflect instantly in browser
```

### 3. Test Changes
```bash
php artisan test
# Or run specific test
php artisan test tests/Unit/MaterialModelTest.php
```

### 4. Database Changes
```bash
# If you modify migrations or seeders
php artisan migrate:refresh --seed
```

### 5. Deploy/Commit
```bash
# When ready for production
git commit -m "Feature: description"
php artisan migrate --force  # on production
```

---

## 📞 Quick Help

### How to create new material?
1. Navigate to /materials/create
2. Fill form (title, author, code required)
3. Select type (fisico, digital, hibrido)
4. Click Create

### How to register loan?
1. Navigate to /loans/create
2. Select user (student)
3. Select material
4. Choose return date (future date)
5. Click Register

### How to view pending fines?
1. Navigate to /fines
2. Filter by status "Pending"
3. See total pending amount

### How to access API?
1. Get user token: `$user->createToken('api')->plainTextToken`
2. Add header: `Authorization: Bearer {TOKEN}`
3. Call endpoints: `/api/materials`, `/api/loans`, etc.

---

## 🚀 Quick Deploy Checklist

Before going to production:

```bash
# 1. Update dependencies
composer update
npm update

# 2. Compile assets
npm run build

# 3. Run tests one more time
php artisan test

# 4. Clear caches
php artisan cache:clear
php artisan config:clear

# 5. Create .env.production
cp .env .env.production

# 6. Generate key if needed
php artisan key:generate --env=production

# 7. Run migrations
php artisan migrate --force

# 8. Seed production data
php artisan db:seed --force

# 9. Optimize for production
php artisan optimize
php artisan view:cache
php artisan route:cache

# 10. Deploy!
# Use your hosting provider's deployment tool
```

---

## 📈 Monitoring

### Check Server Status
```bash
# Is server running?
curl http://localhost:8000/health

# Check database
mysql -u root -p -e "SELECT COUNT(*) FROM users;"

# Check logs
tail -f storage/logs/laravel.log
```

### Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| 404 errors | Run `php artisan route:cache --clear` |
| Permission errors | Check `.env` file paths |
| Database errors | Verify MySQL connection in `.env` |
| Slow requests | Check database queries, add indexes |
| API not working | Verify Sanctum is installed |

---

## ✨ Quick Reference Commands

```bash
# Most used commands
php artisan serve                    # Start server
php artisan test                     # Run all tests
php artisan migrate:refresh --seed   # Reset DB with demo data
php artisan cache:clear              # Clear cache
php artisan key:generate             # Generate APP_KEY
php artisan route:list               # List all routes
php artisan tinker                   # Interactive shell

# Database commands
php artisan migrate                  # Run migrations
php artisan migrate:rollback         # Rollback migrations
php artisan db:seed                  # Run seeders

# Cache commands
php artisan config:clear
php artisan view:cache
php artisan route:cache

# Optimization
php artisan optimize
composer dump-autoload
npm run build
```

---

**Happy Coding! 🚀**

*Remember: Always run tests before deploying!*

---

**Project**: IESTP Library Platform  
**Status**: ✅ Complete & Ready  
**Last Updated**: November 25, 2025
