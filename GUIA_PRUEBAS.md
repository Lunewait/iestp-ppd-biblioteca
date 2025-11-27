# 🧪 GUÍA DE PRUEBAS - Sistema de Biblioteca IESTP

## 📋 Checklist de Pruebas

Usa esta guía para verificar que todas las correcciones funcionan correctamente.

---

## 🔐 1. Pruebas de Navegación por Roles

### Como Estudiante:
- [ ] Iniciar sesión con: `estudiante@iestp.local` / `password`
- [ ] Verificar que el menú muestra:
  - ✅ Dashboard
  - ✅ 📖 Materiales
  - ✅ 📋 **Mis Préstamos** (no "Préstamos")
  - ✅ 📝 Solicitar Préstamo
  - ✅ 💰 Multas
  - ❌ NO debe mostrar: "Aprobar Préstamos", "Usuarios", "Gestionar Préstamos"

### Como Admin:
- [ ] Iniciar sesión con: `admin@iestp.local` / `password`
- [ ] Verificar que el menú muestra:
  - ✅ Dashboard
  - ✅ 📖 Materiales
  - ✅ 📋 **Gestionar Préstamos** (no "Mis Préstamos")
  - ✅ ✅ Aprobar Préstamos
  - ✅ 💰 Multas
  - ✅ 👥 Usuarios
  - ❌ NO debe mostrar: "Solicitar Préstamo"

### Como Trabajador:
- [ ] Iniciar sesión con: `trabajador@iestp.local` / `password`
- [ ] Verificar que el menú muestra:
  - ✅ Dashboard
  - ✅ 📖 Materiales
  - ✅ 📋 **Gestionar Préstamos**
  - ✅ ✅ Aprobar Préstamos
  - ✅ 💰 Multas
  - ✅ 👥 Usuarios
  - ❌ NO debe mostrar: "Solicitar Préstamo"

---

## 💰 2. Pruebas de Multas

### Como Estudiante:
- [ ] Ir a "Multas"
- [ ] Verificar que NO hay error 403
- [ ] Verificar que solo se muestran las multas propias
- [ ] Verificar que NO puede crear multas (no hay botón)

### Como Admin/Trabajador:
- [ ] Ir a "Multas"
- [ ] Verificar que se muestran TODAS las multas
- [ ] Verificar que puede crear nuevas multas
- [ ] Verificar que puede marcar multas como pagadas
- [ ] Verificar que puede condonar multas (solo Admin)

---

## 👥 3. Pruebas de Gestión de Usuarios

### Crear Usuario Individual:
- [ ] Ir a "Usuarios" → "Nuevo Usuario"
- [ ] Completar el formulario:
  - Nombre: `Test Usuario`
  - Email: `test@example.com`
  - Email Institucional: `test@iestp.edu.pe`
  - Contraseña: `password123`
  - Confirmar Contraseña: `password123`
  - Rol: Seleccionar "Estudiante"
- [ ] Verificar que el select de roles muestra: Admin, Jefe_Area, Trabajador, Estudiante
- [ ] Guardar y verificar que se creó correctamente
- [ ] Verificar que aparece en la lista de usuarios

### Editar Usuario:
- [ ] Seleccionar un usuario existente
- [ ] Clic en editar
- [ ] Cambiar el rol
- [ ] Verificar que el select de roles funciona correctamente
- [ ] Guardar cambios

---

## 📥 4. Pruebas de Importación de Usuarios

### Preparación:
- [ ] Ir a "Usuarios" → "Importar Excel"
- [ ] Verificar que la página carga correctamente
- [ ] Descargar la plantilla CSV

### Crear archivo de prueba:
Crea un archivo CSV con el siguiente contenido:

```csv
Nombre,Email,Email Institucional,Contraseña,Rol
Ana López,ana.lopez@example.com,ana.lopez@iestp.edu.pe,password123,Estudiante
Carlos Ruiz,carlos.ruiz@example.com,carlos.ruiz@iestp.edu.pe,password456,Estudiante
María Torres,maria.torres@example.com,maria.torres@iestp.edu.pe,password789,Trabajador
```

### Importar:
- [ ] Subir el archivo CSV creado
- [ ] Verificar que muestra mensaje de éxito
- [ ] Verificar que se importaron 3 usuarios
- [ ] Ir a la lista de usuarios y verificar que aparecen
- [ ] Verificar que cada usuario tiene el rol correcto

### Probar errores:
Crea un archivo con errores:

```csv
Nombre,Email,Email Institucional,Contraseña,Rol
,invalido@example.com,invalido@iestp.edu.pe,123,RolInvalido
```

- [ ] Subir el archivo con errores
- [ ] Verificar que muestra los errores encontrados
- [ ] Verificar que NO se creó el usuario inválido

---

## 📋 5. Pruebas de Préstamos

### Como Estudiante:
- [ ] Ir a "Mis Préstamos"
- [ ] Verificar que solo muestra préstamos propios
- [ ] Ir a "Solicitar Préstamo"
- [ ] Crear una solicitud de préstamo
- [ ] Verificar que aparece en "Mis Préstamos" con estado "Pendiente"

### Como Admin/Trabajador:
- [ ] Ir a "Gestionar Préstamos"
- [ ] Verificar que muestra TODOS los préstamos del sistema
- [ ] Ir a "Aprobar Préstamos"
- [ ] Verificar que NO hay error 403
- [ ] Aprobar la solicitud del estudiante
- [ ] Verificar que el préstamo cambia a estado "Activo"

### Verificar Préstamos Vencidos:
- [ ] Crear un préstamo con fecha de devolución pasada (usar base de datos)
- [ ] Verificar que se marca como "Vencido" en la interfaz
- [ ] Verificar que se calcula la multa automáticamente

---

## 🎯 6. Pruebas de Permisos

### Estudiante NO debe poder:
- [ ] Ver "Aprobar Préstamos" (debe dar 403 si accede directamente)
- [ ] Crear materiales
- [ ] Editar materiales
- [ ] Eliminar materiales
- [ ] Crear usuarios
- [ ] Ver todos los préstamos (solo los suyos)
- [ ] Crear multas

### Estudiante SÍ debe poder:
- [ ] Ver materiales
- [ ] Ver sus propios préstamos
- [ ] Solicitar préstamos
- [ ] Ver sus propias multas
- [ ] Crear reservaciones

### Admin debe poder:
- [ ] TODO lo anterior
- [ ] Crear/editar/eliminar usuarios
- [ ] Aprobar préstamos
- [ ] Gestionar multas
- [ ] Condonar multas
- [ ] Ver todos los préstamos

---

## 📊 7. Verificación de Estados de Préstamos

### Crear préstamos de prueba:

1. **Préstamo Activo (no vencido):**
   - [ ] Crear préstamo con fecha de devolución futura
   - [ ] Verificar que muestra estado "Activo"
   - [ ] Verificar que NO muestra como "Vencido"

2. **Préstamo Vencido:**
   - [ ] Crear préstamo con fecha de devolución pasada
   - [ ] Verificar que muestra estado "Activo" pero marcado como "Vencido"
   - [ ] Verificar que calcula días de retraso
   - [ ] Verificar que calcula monto de multa

3. **Préstamo Devuelto:**
   - [ ] Devolver un préstamo
   - [ ] Verificar que cambia a estado "Devuelto"
   - [ ] Verificar que ya NO se marca como vencido

---

## 🔧 Comandos de Verificación

Si algo no funciona, ejecuta estos comandos:

```bash
# Actualizar permisos
php artisan db:seed --class=RolePermissionSeeder

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Verificar cambios
php verificar_cambios.php
```

---

## ✅ Checklist Final

Antes de dar por terminadas las pruebas, verifica:

- [ ] Todos los roles tienen la navegación correcta
- [ ] Los estudiantes pueden ver sus multas
- [ ] Los formularios de usuarios funcionan correctamente
- [ ] La importación de usuarios funciona
- [ ] Los préstamos muestran correctamente "Activo" y "Vencido"
- [ ] Los permisos están correctos para cada rol
- [ ] No hay errores 403 inesperados
- [ ] La documentación está completa

---

## 📝 Reportar Problemas

Si encuentras algún problema durante las pruebas:

1. Anota el error exacto
2. Anota el rol con el que estabas probando
3. Anota los pasos para reproducir el error
4. Revisa la documentación en `SOLUCION_PROBLEMAS.md`
5. Ejecuta los comandos de verificación

---

## 🎉 ¡Pruebas Completadas!

Si todas las pruebas pasaron exitosamente, el sistema está listo para usar.

**Fecha de pruebas:** _______________
**Probado por:** _______________
**Resultado:** ⭕ Aprobado / ⭕ Con observaciones

---

**Documentación relacionada:**
- `SOLUCION_PROBLEMAS.md` - Explicación detallada de cada solución
- `RESUMEN_CAMBIOS.md` - Resumen de todos los cambios
- `verificar_cambios.php` - Script de verificación automática
