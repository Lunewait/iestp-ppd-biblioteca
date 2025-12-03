# Guía Rápida: Cómo Cambiar Colores de Botones y Formularios

## 🎨 Dónde están los archivos de las vistas

Todas las vistas (páginas) están en la carpeta:
```
resources/views/
```

### Estructura de carpetas:
```
resources/views/
├── auth/               → Páginas de login y registro
│   └── login.blade.php
├── materials/          → Páginas de materiales
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── loans/              → Páginas de préstamos
│   ├── index.blade.php
│   └── create.blade.php
├── fines/              → Páginas de multas
├── users/              → Páginas de usuarios
├── reservations/       → Páginas de reservas
└── layouts/
    └── app.blade.php   → Plantilla principal (menú, header, etc.)
```

## 🔍 Cómo encontrar un botón específico

### Método 1: Buscar por texto
1. Presiona `Ctrl + Shift + F` en VS Code
2. Escribe el texto del botón (ej: "Exportar a Excel")
3. Te mostrará en qué archivo está

### Método 2: Buscar por color
Los colores en Tailwind CSS se escriben así:
- `bg-blue-600` = Fondo azul
- `bg-green-600` = Fondo verde
- `bg-red-600` = Fondo rojo
- `bg-cyan-600` = Fondo cyan (azul claro)
- `bg-purple-600` = Fondo morado

Busca con `Ctrl + Shift + F` el color que quieres cambiar.

## 🎨 Cómo cambiar colores de botones

### Ejemplo 1: Cambiar el botón "Exportar a Excel" de verde a azul

**Archivo**: `resources/views/materials/index.blade.php`

**Antes:**
```blade
<a href="{{ route('materials.export') }}" 
   class="... bg-green-600 hover:bg-green-700 ...">
```

**Después:**
```blade
<a href="{{ route('materials.export') }}" 
   class="... bg-blue-600 hover:bg-blue-700 ...">
```

### Ejemplo 2: Cambiar el botón de login

**Archivo**: `resources/views/auth/login.blade.php` (línea ~115)

**Antes:**
```blade
<button type="submit"
    class="... bg-cyan-600 hover:bg-cyan-700 ...">
    Iniciar Sesión
</button>
```

**Después (a rojo):**
```blade
<button type="submit"
    class="... bg-red-600 hover:bg-red-700 ...">
    Iniciar Sesión
</button>
```

## 📋 Tabla de Colores Disponibles

| Color | Código Tailwind | Ejemplo Visual |
|-------|----------------|----------------|
| Rojo | `bg-red-600` | 🔴 |
| Naranja | `bg-orange-600` | 🟠 |
| Amarillo | `bg-yellow-600` | 🟡 |
| Verde | `bg-green-600` | 🟢 |
| Azul | `bg-blue-600` | 🔵 |
| Cyan | `bg-cyan-600` | 🔷 |
| Morado | `bg-purple-600` | 🟣 |
| Rosa | `bg-pink-600` | 🌸 |
| Gris | `bg-gray-600` | ⚫ |

**Nota**: El número (600, 700, 800) indica la intensidad:
- `500` = Más claro
- `600` = Normal
- `700` = Más oscuro
- `800` = Muy oscuro

## 🖌️ Cambiar colores de formularios

### Cambiar el color del borde de los inputs

**Busca en cualquier formulario:**
```blade
<input ... class="... border-gray-300 focus:border-cyan-500 ...">
```

**Para cambiar a azul:**
```blade
<input ... class="... border-gray-300 focus:border-blue-500 ...">
```

### Cambiar el color de fondo de un formulario

**Busca:**
```blade
<div class="bg-white ...">
```

**Para cambiar a gris claro:**
```blade
<div class="bg-gray-50 ...">
```

## 🎯 Ejemplos Prácticos Comunes

### 1. Cambiar todos los botones verdes a azules
Busca: `bg-green-600`
Reemplaza por: `bg-blue-600`

Y también:
Busca: `hover:bg-green-700`
Reemplaza por: `hover:bg-blue-700`

### 2. Cambiar el color del menú lateral
**Archivo**: `resources/views/layouts/app.blade.php`

Busca la sección del sidebar (línea ~50-100) y cambia:
```blade
<div class="bg-gray-900 ...">  <!-- Menú oscuro -->
```

A:
```blade
<div class="bg-blue-900 ...">  <!-- Menú azul oscuro -->
```

### 3. Cambiar el color de los enlaces del menú
En el mismo archivo `app.blade.php`, busca:
```blade
<a ... class="... text-cyan-400 hover:bg-gray-700 ...">
```

Cambia a:
```blade
<a ... class="... text-blue-400 hover:bg-gray-700 ...">
```

## ⚡ Consejo Pro: Buscar y Reemplazar en Múltiples Archivos

1. Presiona `Ctrl + Shift + H` en VS Code
2. En "Search" escribe: `bg-green-600`
3. En "Replace" escribe: `bg-blue-600`
4. Haz clic en "Replace All" (el icono de dos flechas)
5. ¡Todos los botones verdes cambiarán a azul!

## 🚨 Importante

Después de hacer cambios:
1. Guarda el archivo (`Ctrl + S`)
2. Recarga la página en tu navegador (`F5`)
3. Si no ves los cambios, presiona `Ctrl + Shift + R` (recarga forzada)

## 📝 Archivos Clave para Cambios Visuales

| Qué quieres cambiar | Archivo |
|---------------------|---------|
| Página de login | `resources/views/auth/login.blade.php` |
| Menú lateral y header | `resources/views/layouts/app.blade.php` |
| Botones de materiales | `resources/views/materials/index.blade.php` |
| Formulario de crear material | `resources/views/materials/create.blade.php` |
| Botones de préstamos | `resources/views/loans/index.blade.php` |
| Página de inicio | `resources/views/welcome.blade.php` |

---

**Recuerda**: No tengas miedo de experimentar. Si algo sale mal, siempre puedes usar `Ctrl + Z` para deshacer o pedirme ayuda. 😊
