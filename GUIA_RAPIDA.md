# ⚡ GUÍA RÁPIDA - IESTP LIBRARY

## 🚀 Iniciar Inmediatamente

```bash
# El servidor ya está corriendo en:
http://127.0.0.1:8000

# Si necesitas reiniciarlo:
cd C:\Users\Maria\Documents\iestp-library
php artisan serve
```

---

## 🔓 Credenciales de Acceso

| Rol | Email | Password |
|---|---|---|
| Admin | `admin@iestp.local` | `password` |
| Estudiante | `carlos@iestp.local` | `password` |
| Trabajador | `diego@iestp.local` | `password` |

---

## 📱 Funcionalidades por Rol

### 👨‍💼 Admin
- Dashboard con estadísticas
- Ver catálogo de materiales
- Crear/editar/eliminar materiales
- Aprobar o rechazar préstamos
- Gestionar usuarios
- Ver historial de multas

### 🎓 Estudiante
- Dashboard con mis estadísticas
- Ver catálogo de materiales
- Solicitar préstamos
- Ver mis préstamos activos
- Ver mis multas pendientes

### 👨‍💻 Trabajador
- Dashboard
- Ver catálogo
- Crear/editar materiales
- Aprobar/rechazar préstamos
- Ver registro de multas

---

## 🗂️ Navegación Principal

| Página | URL | Acceso |
|---|---|---|
| Dashboard | `/dashboard` | Todos |
| Materiales | `/materials` | Todos |
| Solicitar Préstamo | `/loan-requests` | Solo Estudiantes |
| Mis Préstamos | `/loans` | Todos |
| Aprobaciones | `/loan-approvals` | Admin/Trabajador |
| Mis Multas | `/fines` | Todos |

---

## ⚙️ Comandos Útiles

### Ver Logs
```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log
```

### Ejecutar Tests
```bash
php artisan test
```

### Resetear BD
```bash
php artisan migrate:fresh --seed --force
```

### Ejecutar Seeders
```bash
php artisan db:seed
```

---

## 📊 Datos Disponibles

- **11 Materiales** (libros, recursos digitales, etc)
- **19 Usuarios** con 4 roles diferentes
- **7 Préstamos** en diferentes estados
- **2 Multas** por retrasos

---

## 🔍 Flujo de Préstamo

1. **Estudiante** solicita préstamo desde `/loan-requests`
2. **Sistema** marca como `approval_status: pending`
3. **Admin/Trabajador** ve solicitud en `/loan-approvals`
4. **Admin** aprueba o rechaza
5. **Estudiante** ve estado actualizado en `/loans`

---

## 📝 Multas Automáticas

- Se generan automáticamente cuando vence un préstamo
- **Monto**: 1.50 por día de retraso
- **Estado**: Pendiente (pagada/condonada por admin)

---

## 💡 Tips Importantes

✅ **No editars**: Los archivos de controladores ya tienen los traits necesarios
✅ **Base de datos**: Está en MySQL, verifica que esté corriendo
✅ **Livewire**: Los componentes se actualizan sin recargar la página
✅ **Roles**: El sistema valida automáticamente qué puede hacer cada usuario

---

## 🆘 Si Algo Falla

### Error: "Connection refused"
```bash
# Asegúrate que MySQL esté corriendo
# Y que el servidor esté activo
php artisan serve
```

### Error: "Table not found"
```bash
# Recrea la BD
php artisan migrate:fresh --seed --force
```

### Error: 403 Unauthorized
- Verificar que el usuario tenga el rol correcto
- Revisar que esté logueado

### Tests fallan
```bash
php artisan migrate:fresh --seed --force
php artisan test
```

---

## 📚 Stack Tecnológico

- **Laravel 12** - Framework PHP moderno
- **Livewire 3** - Componentes reactivos sin JS
- **MySQL 8** - Base de datos
- **Tailwind CSS** - Estilos
- **Spatie Permission** - Control de acceso

---

## 🎯 Checklist de Verificación

- [ ] Servidor corriendo en http://127.0.0.1:8000
- [ ] Puedo acceder a `/login`
- [ ] Puedo logearme como admin
- [ ] Veo el dashboard con estadísticas
- [ ] Puedo ver 11 materiales en `/materials`
- [ ] Puedo solicitar préstamo como estudiante
- [ ] Puedo aprobar préstamos como admin
- [ ] Tests pasan (13/13)

---

## 📞 Resumen Rápido

**Sistema**: ✅ Operativo
**Servidor**: ✅ Activo  
**BD**: ✅ Poblada
**Tests**: ✅ Pasando

**¡Listo para usar! 🚀**

