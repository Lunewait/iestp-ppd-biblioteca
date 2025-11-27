# 🔗 Livewire Components Integration Guide

## Quick Start - Add Components to Routes

### Option 1: Create New Routes for Components

Add to `routes/web.php`:

```php
Route::middleware(['auth', 'verified'])->group(function () {
    // Materials with Livewire component
    Route::get('/materials-dynamic', function () {
        return view('materials.dynamic'); // Create this view
    })->name('materials.dynamic');
    
    // Loans with Livewire component
    Route::get('/loans-dynamic', function () {
        return view('loans.dynamic'); // Create this view
    })->name('loans.dynamic');
    
    // Create material with Livewire component
    Route::get('/materials/new', function () {
        return view('materials.new'); // Create this view
    })->name('materials.new');
});
```

### Option 2: Embed Components in Existing Controller Routes

Update existing controller methods:

**MaterialController:**
```php
public function index()
{
    // Replace with Livewire component
    return view('materials.index'); // Will contain <livewire:materials-list />
}

public function create()
{
    // Replace with Livewire component
    return view('materials.create'); // Will contain <livewire:create-material />
}
```

**LoanController:**
```php
public function index()
{
    // Replace with Livewire component
    return view('loans.index'); // Will contain <livewire:loans-list />
}
```

---

## 📄 Create Required Views

### 1. `resources/views/materials/dynamic.blade.php`
```blade
@extends('layouts.app')

@section('title', 'Gestión de Materiales')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Materiales Disponibles</h1>
        <a href="{{ route('materials.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            + Nuevo Material
        </a>
    </div>

    <!-- Livewire Component -->
    <livewire:materials-list />
</div>
@endsection
```

### 2. `resources/views/loans/dynamic.blade.php`
```blade
@extends('layouts.app')

@section('title', 'Gestión de Préstamos')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Préstamos y Devoluciones</h1>
        <p class="text-gray-600 mt-2">Seguimiento en tiempo real de todos los préstamos</p>
    </div>

    <!-- Livewire Component -->
    <livewire:loans-list />
</div>
@endsection
```

### 3. `resources/views/materials/new.blade.php`
```blade
@extends('layouts.app')

@section('title', 'Registrar Nuevo Material')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4">
    <!-- Livewire Component -->
    <livewire:create-material />
</div>
@endsection
```

---

## 🎯 Integration Examples

### Example 1: Add Livewire Link to Dashboard

In your dashboard view, add navigation links:

```blade
<!-- In layouts/navigation.blade.php or dashboard.blade.php -->
<nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
            <div class="flex space-x-8">
                <a href="{{ route('materials.index') }}" class="...">Materiales</a>
                <a href="{{ route('loans.index') }}" class="...">Préstamos</a>
                <a href="{{ route('materials.create') }}" class="...">+ Nuevo</a>
            </div>
        </div>
    </div>
</nav>
```

### Example 2: Embed Component in Existing Page

Instead of replacing entire pages, embed component in existing view:

```blade
<!-- In materials/dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <h1 class="text-3xl font-bold mb-6">Dashboard de Materiales</h1>
    
    <!-- Stats Section -->
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="text-2xl font-bold">{{ $totalMaterials }}</div>
            <div class="text-gray-600">Total de Materiales</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="text-2xl font-bold">{{ $availableMaterials }}</div>
            <div class="text-gray-600">Disponibles</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="text-2xl font-bold">{{ $borrowedMaterials }}</div>
            <div class="text-gray-600">En Préstamo</div>
        </div>
    </div>

    <!-- Embedded Livewire Component -->
    <livewire:materials-list />
</div>
@endsection
```

### Example 3: Authorization-Based Conditional Display

```blade
@auth
    @can('view_material')
        <livewire:materials-list />
    @else
        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded">
            <p class="text-yellow-800">No tienes permiso para ver esta información.</p>
        </div>
    @endcan
@endauth
```

---

## 🔄 Updating Existing Views

### Replace MaterialController Index View

**Before:**
```blade
@section('content')
    <table class="min-w-full">
        <!-- Static HTML table -->
    </table>
@endsection
```

**After:**
```blade
@section('content')
    <livewire:materials-list />
@endsection
```

### Keep Existing Navigation, Add Component

```blade
@extends('layouts.app')

@section('header')
    <h1>Gestión de Materiales</h1>
    <nav>
        <a href="{{ route('materials.index') }}">Todos</a>
        <a href="{{ route('materials.create') }}">Crear</a>
    </nav>
@endsection

@section('content')
    <!-- Your existing header/filters -->
    
    <!-- NEW: Livewire Component -->
    <livewire:materials-list />
@endsection
```

---

## 🔗 Navigation Links

Update your navigation menu to link to Livewire components:

```blade
<!-- In layouts/app.blade.php or navbar -->
<div class="nav-menu">
    <a href="{{ route('dashboard') }}" class="nav-link">
        Dashboard
    </a>
    
    <a href="{{ route('materials.index') }}" class="nav-link">
        📚 Materiales
    </a>
    
    <a href="{{ route('loans.index') }}" class="nav-link">
        📋 Préstamos
    </a>
    
    <a href="{{ route('fines.index') }}" class="nav-link">
        💰 Multas
    </a>
    
    <a href="{{ route('users.index') }}" class="nav-link">
        👥 Usuarios
    </a>
</div>
```

---

## 🎨 Custom Styling

### Apply to Component Container

```blade
<div class="my-custom-container">
    <livewire:materials-list />
</div>

<style>
    .my-custom-container {
        background: linear-gradient(to right, #f0f9ff, #f0fdf4);
        border-radius: 12px;
        padding: 24px;
    }
</style>
```

### Override Component Styles

Modify `resources/views/livewire/materials-list.blade.php` to add custom classes:

```blade
<div class="space-y-6 custom-bg custom-shadow">
    <!-- Your custom classes here -->
</div>
```

---

## 🔐 Permission-Based Component Display

### Show Component Only if User Has Permission

```blade
@if(auth()->user()->hasPermissionTo('view_material'))
    <livewire:materials-list />
@else
    <div class="text-red-600 p-4">
        Acceso denegado. No tienes permiso para ver esta página.
    </div>
@endif
```

### Role-Based Display

```blade
@if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('jefe_area'))
    <livewire:create-material />
@endif
```

---

## 📱 Responsive Integration

### Mobile-Friendly Layout

```blade
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Mobile header -->
    <div class="md:flex md:justify-between md:items-center mb-6">
        <h1 class="text-2xl md:text-3xl font-bold">Materiales</h1>
        <a href="{{ route('materials.create') }}" class="mt-4 md:mt-0 btn btn-primary">
            + Nuevo
        </a>
    </div>

    <!-- Component scales responsively -->
    <livewire:materials-list />
</div>
@endsection
```

---

## 🚀 Testing the Integration

### 1. Verify Route Works

```bash
# Start server
php artisan serve

# Visit in browser
# http://localhost:8000/materials-dynamic
```

### 2. Test Component Interactivity

- Type in search box
- Select filters
- Click delete button
- Verify no page reload

### 3. Test Authorization

- Log in as different roles
- Verify permissions work
- Check errors display correctly

### 4. Run Tests

```bash
php artisan test
# ✅ All 13 tests should pass
```

---

## 🐛 Common Integration Issues

### Issue: Component shows but interactions don't work

**Solution:**
1. Clear view cache: `php artisan view:clear`
2. Check Livewire scripts in layout: `@livewireScripts`
3. Check browser console for JavaScript errors

### Issue: Forms not submitting

**Solution:**
1. Ensure form has `wire:submit` on form element
2. Check buttons have `type="submit"`
3. Verify `$rules` array in component

### Issue: Data not updating after save

**Solution:**
1. Component automatically redirects on success
2. If not redirecting, check `return redirect()`
3. Verify model has `$fillable` array set

### Issue: Pagination shows wrong number of pages

**Solution:**
1. Adjust `$perPage` in component
2. Check `paginate()` is called in `#[Computed]`
3. Verify query returns correct results

---

## 📋 Implementation Checklist

- [ ] Add routes to `routes/web.php`
- [ ] Create view files in `resources/views/`
- [ ] Add navigation links
- [ ] Test in browser
- [ ] Test authorization
- [ ] Run tests: `php artisan test`
- [ ] Clear caches: `php artisan cache:clear`
- [ ] Test on mobile
- [ ] Check permissions work
- [ ] Verify no page reloads on interactions

---

## ✅ Success Criteria

When integration is complete:
- ✅ Components render without errors
- ✅ Search/filter updates without page reload
- ✅ Forms submit successfully
- ✅ Delete confirmations work
- ✅ Authorization prevents unauthorized access
- ✅ No console JavaScript errors
- ✅ All tests still pass
- ✅ Responsive design works on mobile

---

**Next Step:** Implement routes and views using the templates above, then test the components in your browser!
