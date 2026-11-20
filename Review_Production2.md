# 🏛️ Dictamen de Pre-Producción — FARUSAC Sistema de Informes

**Fecha:** 20 de noviembre de 2026  
**Sistema:** Sistema de Informes Mensuales — Facultad de Arquitectura, USAC  
**Stack:** Laravel 11 + Blade + MySQL + Google OAuth + Chromium PDF  
**Evaluador:** Revisión técnica automatizada  

---

## 📊 Resumen Ejecutivo

| Área | Estado | Veredicto |
|------|--------|-----------|
| Arquitectura Backend | ✅ Bueno | Apto con observaciones |
| Seguridad | ⚠️ Con riesgos | **Requiere correcciones antes de producción** |
| Base de datos y migraciones | ✅ Bueno | Apto |
| Validación de datos | ✅ Bueno | Apto |
| Manejo de errores | ⚠️ Mejorable | Apto con observaciones |
| Frontend (Blade + JS + CSS) | ✅ Bueno | Apto con observaciones |
| Testing | 🔴 Insuficiente | **Requiere correcciones antes de producción** |
| Configuración de entorno | 🔴 Crítico | **Requiere correcciones antes de producción** |
| Rendimiento | ⚠️ Mejorable | Apto con observaciones |
| Código muerto / Deuda técnica | ⚠️ Mejorable | Apto con observaciones |

### Veredicto General

> [!WARNING]
> **NO APTO para producción en su estado actual.** Se identificaron **4 bloqueos críticos** y **8 observaciones importantes** que deben resolverse antes del despliegue. Ninguno requiere una reescritura; todos son correcciones puntuales.

---

## 🔴 BLOQUEOS CRÍTICOS (Deben resolverse antes de producción)

### 1. Credenciales expuestas en `.env` versionado

**Archivo:** `.env`  
**Severidad:** 🔴 CRÍTICA

El archivo `.env` contiene credenciales reales de producción:

```
GOOGLE_CLIENT_ID=504974444658-...apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=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
APP_KEY=base64:IT99cyfkoI8no8LYPahOIIoP3TWTFoX603NjtgWWK2U=
```

**Riesgo:** Si el repositorio es público o se comparte, las credenciales OAuth y la clave de cifrado quedan comprometidas. Un atacante podría:
- Autenticarse como cualquier usuario con el Client Secret de Google
- Descifrar cookies de sesión con el `APP_KEY`

**Acción requerida:**
1. Verificar que `.env` está en `.gitignore`
2. **Rotar inmediatamente** el `GOOGLE_CLIENT_SECRET` y el `APP_KEY` antes del despliegue
3. En producción, cambiar `APP_DEBUG=false` y `APP_ENV=production`
4. Generar un nuevo `APP_KEY` con `php artisan key:generate`

---

### 2. Configuración de desarrollo activa

**Archivo:** `.env`  
**Severidad:** 🔴 CRÍTICA

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
DB_DATABASE=Farusac_TEST
DB_PASSWORD=                    # ← Sin contraseña
GOOGLE_REDIRECT_URI=http://FarusacTest.com:8000/auth/google/callback
GOOGLE_SSL_CERT_PATH=C:\php\cacert.pem   # ← Ruta local de Windows
```

**Riesgo:** Con `APP_DEBUG=true` en producción, cualquier error mostraría al usuario el stack trace completo, incluyendo rutas del servidor, queries SQL y variables de entorno.

**Acción requerida para producción:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://informes.farusac.edu.gt   # (dominio real)
DB_DATABASE=farusac_produccion
DB_PASSWORD=<contraseña_segura>
GOOGLE_REDIRECT_URI=https://informes.farusac.edu.gt/auth/google/callback
GOOGLE_SSL_CERT_PATH=                      # o ruta válida del servidor Linux
SESSION_DRIVER=database                    # (actualmente 'file')
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

---

### 3. Ausencia total de tests

**Archivos:** `tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php`  
**Severidad:** 🔴 CRÍTICA

Los únicos tests son los de ejemplo auto-generados por Laravel:

```php
public function test_the_application_returns_a_successful_response(): void
{
    $response = $this->get('/');
    $response->assertStatus(200);  // ← Este test va a FALLAR porque '/' redirecciona (302)
}
```

**Riesgo:** No hay garantía automatizada de que las rutas funcionen, que la validación sea correcta, ni que la autenticación bloquee accesos no autorizados. Un cambio accidental podría romper funcionalidad sin que nadie lo detecte.

**Acción requerida (mínima para producción):**
- Tests de Feature para las rutas de autenticación (login, callback, logout)
- Tests para las rutas protegidas por `CheckRole` (que un docente no pueda acceder a rutas de admin)
- Tests para la validación de Form Requests (informes duplicados, campos requeridos)
- Tests para la importación CSV (formato correcto e incorrecto)

---

### 4. Sin rate limiting en rutas de autenticación — ✅ RESUELTO

**Archivo:** `routes/web.php`  
**Severidad:** 🔴 ALTA (Resuelto)

> **Solución aplicada:** Se envolvió el grupo de rutas `/auth/google` y `/auth/google/callback` bajo el middleware `throttle:10,1` (máximo 10 peticiones por minuto por IP), previniendo ataques de denegación de servicio o saturación de cuota de Google OAuth.

---

## ⚠️ OBSERVACIONES IMPORTANTES (Recomendadas antes de producción)

### 5. `Schema::hasColumn()` y `DB::table()` en runtime — ✅ RESUELTO

**Archivo:** [CoordinadorController.php](file:///c:/Users/jpsam/OneDrive/Escritorio/Uni/Farusac_SistemaInformes/Backend/app/Http/Controllers/CoordinadorController.php#L85-L100)  
**Severidad:** ⚠️ MEDIA (Resuelto)

> **Solución aplicada:** Se eliminó la consulta en runtime a `information_schema` (`Schema::hasColumn`) y se reemplazó el bypass de `DB::table()` por la llamada limpia de Eloquent `$user->update(['area' => $area])`, manteniendo `carrera` exclusivamente en la sesión activa del coordinador. Se limpiaron los imports no utilizados de `DB` y `Schema`.

---

### 6. Fallback `@exec()` en ChromiumPdfService — ✅ RESUELTO

**Archivo:** [ChromiumPdfService.php](file:///c:/Users/jpsam/OneDrive/Escritorio/Uni/Farusac_SistemaInformes/Backend/app/Services/ChromiumPdfService.php#L190-L210)  
**Severidad:** ⚠️ MEDIA (Resuelto)

> **Solución aplicada:** Se eliminó el bloque de fallback con `@exec()`. El comando de Chromium ahora se ejecuta estrictamente a través de `Process::run()` con array de argumentos y timeout de 60 segundos. Si el proceso falla o lanza una excepción, se registra detalladamente mediante `Log::error()` y se lanza una excepción descriptiva sin ocultar errores.

---

### 7. Trait `HandlesInformeData` es código muerto - ✅ RESUELTO

**Archivo:** [HandlesInformeData.php](file:///c:/Users/jpsam/OneDrive/Escritorio/Uni/Farusac_SistemaInformes/Backend/app/Traits/HandlesInformeData.php) (112 líneas)  
**Severidad:** ⚠️ BAJA

Este trait contiene 3 métodos (`buildInformeDocenteData`, `buildInformeCoordinacionData`, `eliminarInformeDocenteModel`) que **ya fueron reemplazados** por los API Resources (`InformeDocenteResource`, `InformeCoordinacionResource`) y los Services (`InformeDocenteService`, `InformeCoordinacionService`).

Ningún controlador usa `use HandlesInformeData`.

**Acción requerida:**
- Eliminar el archivo `app/Traits/HandlesInformeData.php`

---

### 8. `UserFactory::unverified()` referencia columna inexistente — ✅ RESUELTO

**Archivo:** [UserFactory.php](file:///c:/Users/jpsam/OneDrive/Escritorio/Uni/Farusac_SistemaInformes/Backend/database/factories/UserFactory.php)  
**Severidad:** ⚠️ BAJA (Resuelto)

> **Solución aplicada:** Se eliminó el método obsoleto `unverified()` y la columna inexistente `email_verified_at`, así como los imports no utilizados `Hash` y `Str` y la propiedad `$password`. Se agregaron métodos de estado oficiales del dominio: `administrador()`, `coordinador()` e `inactivo()`.

---

### 9. Modelo `User` sin `$hidden` — ✅ RESUELTO

**Archivo:** [User.php](file:///c:/Users/jpsam/OneDrive/Escritorio/Uni/Farusac_SistemaInformes/Backend/app/Models/User.php)  
**Severidad:** ⚠️ MEDIA (Resuelto)

> **Solución aplicada:** Se agregó `protected $hidden = ['google_id'];` al modelo `User`, garantizando que el identificador sensible de Google no quede expuesto al serializar el usuario a JSON o arrays.

---

### 10. Días de bloqueo hardcodeados como `3` — ✅ RESUELTO

**Archivos:** `config/informes.php`, `Informe.php`, `InformeCoordinacion.php`, servicios y controladores  
**Severidad:** ⚠️ BAJA (Resuelto)

> **Solución aplicada:** Se creó el archivo de configuración dedicado `config/informes.php` con la clave `'dias_bloqueo' => (int) env('INFORMES_DIAS_BLOQUEO', 3)`. Todos los modelos (`Informe`, `InformeCoordinacion`), servicios (`InformeDocenteService`, `InformeCoordinacionService`) y controladores (`DocenteController`, `CoordinadorController`) ahora consumen `config('informes.dias_bloqueo', 3)` dinámicamente tanto en cálculos de fechas como en mensajes de usuario.

---

### 11. `CatalogoService::getCatalogoCursos()` sin paginación / caching — ✅ RESUELTO

**Archivo:** [CatalogoService.php](file:///c:/Users/jpsam/OneDrive/Escritorio/Uni/Farusac_SistemaInformes/Backend/app/Services/CatalogoService.php#L45-L75)  
**Severidad:** ⚠️ MEDIA (Resuelto)

> **Solución aplicada:** Se implementó almacenamiento en caché con `Cache::remember('catalogo_cursos_completo', ...)` para `getCatalogoCursos()` (con TTL configurable de 30 minutos e invalidación automática en `CursoService` y `CsvImportService`). Además se añadió el método `getCatalogoCursosPaginado(int $perPage = 25)` para particionar resultados cuando se requiera.

---

### 12. Sanitización y validación de IDs en descargas por selección — ✅ RESUELTO

**Archivo:** [AdminController.php](file:///c:/Users/jpsam/OneDrive/Escritorio/Uni/Farusac_SistemaInformes/Backend/app/Http/Controllers/AdminController.php#L349-L375), [InformeExportService.php](file:///c:/Users/jpsam/OneDrive/Escritorio/Uni/Farusac_SistemaInformes/Backend/app/Services/InformeExportService.php#L210-L230)  
**Severidad:** ⚠️ BAJA (Resuelto)

> **Solución aplicada:** Se implementó un método centralizado `sanitizarIds()` en `AdminController` que valida, divide comas y convierte estrictamente cada identificador con `array_map('intval', ...)` filtrando valores mayores a 0 (`$id > 0`). Se aplicó la misma sanitización en `InformeExportService`. La protección CSRF `@csrf` ya se encuentra validada y activa en el formulario receptor `formSeleccionadosPost` y protegida por el middleware `web`.

---

## ✅ ASPECTOS POSITIVOS (Listos para producción)

### Arquitectura y Patrones

| Aspecto | Estado | Detalles |
|---------|--------|----------|
| Separación por capas | ✅ | Controllers → Services → Models bien definidos |
| Inyección de dependencias | ✅ | Todos los services inyectados vía constructor con promoted properties |
| Form Requests | ✅ | 12 FormRequests con `authorize()` y `rules()` completos |
| API Resources | ✅ | 4 Resources (`UserResource`, `CursoResource`, `InformeDocenteResource`, `InformeCoordinacionResource`) |
| Enum tipado | ✅ | `Rol` backed enum con `values()` y `label()` |
| Route Model Binding | ✅ | Configurado en `AppServiceProvider` con patrones numéricos |
| Middleware de autorización | ✅ | `CheckRole` con soporte multi-rol |

### Base de Datos

| Aspecto | Estado | Detalles |
|---------|--------|----------|
| Migraciones consolidadas | ✅ | 4 migraciones limpias, ordenadas y con FK constraints |
| Seeder para admin | ✅ | `DatabaseSeeder` crea usuario administrador con `firstOrCreate` |
| Transacciones atómicas | ✅ | Todas las operaciones multi-tabla usan `DB::beginTransaction()` |
| Paginación | ✅ | `AdminController::informesView()` y vistas de usuarios/cursos paginan |

### Seguridad (lo que sí está bien)

| Aspecto | Estado | Detalles |
|---------|--------|----------|
| Autenticación Google OAuth | ✅ | Validación de dominio `@farusac.edu.gt` |
| Protección CSRF | ✅ | Middleware por defecto de Laravel activo |
| Validación de dominio | ✅ | `AuthController::callback()` verifica dominio explícitamente |
| Autorización por recurso | ✅ | Verificación `usuario_id !== $user->id` en edición/eliminación |
| Manejo de errores genéricos | ✅ | Los catch blocks devuelven mensajes genéricos, no `$e->getMessage()` |
| Cookies HttpOnly | ✅ | Configurado en `session.php` |
| Serialización JSON de sesiones | ✅ | `serialization => 'json'` previene ataques de deserialización PHP |

### Frontend

| Aspecto | Estado | Detalles |
|---------|--------|----------|
| Layout maestro | ✅ | `layouts/app.blade.php` con partials y components |
| CSS modularizado | ✅ | CSS restructurado de ~5,920 → ~868 líneas |
| Componentes Blade | ✅ | 5 components (`modal`, `confirm-modal`, `toast`, `informe-table`, `report-preview-modal`) |
| JS separado por vista | ✅ | 11 archivos JS específicos por funcionalidad |

---

## 📋 Inventario del Sistema

### Métricas generales

| Categoría | Archivos | Líneas aprox. |
|-----------|----------|---------------|
| Controllers | 5 | 1,337 |
| Services | 8 | 2,081 |
| Models | 9 | 448 |
| Form Requests | 12 | ~700 |
| Resources | 4 | ~148 |
| Migrations | 4 | ~271 |
| Views (Blade) | 23 | ~7,000 |
| JavaScript | 11 | ~2,865 |
| CSS | ~20 | ~870 |
| **Total Backend PHP** | **~45** | **~5,000** |

### Mapa de rutas

| Grupo | Rutas | Protección |
|-------|-------|------------|
| Auth (público) | 4 | Ninguna |
| Docente + Coordinador | 24 | `auth` + `role:docente,coordinador` |
| Docente exclusivo | 1 | `auth` + `role:docente` |
| Coordinador exclusivo | 8 | `auth` + `role:coordinador` |
| Administrador | 19 | `auth` + `role:administrador` |
| **Total** | **56** | — |

---

## 📌 Plan de Acción Priorizado

### Fase 1: Bloqueos Críticos (OBLIGATORIOS antes de desplegar)

| # | Acción | Esfuerzo | Archivos afectados |
|---|--------|----------|--------------------|
| 1 | Configurar `.env` para producción (APP_DEBUG=false, rotar APP_KEY y GOOGLE_CLIENT_SECRET) | 30 min | `.env` |
| 2 | Agregar rate limiting a rutas OAuth | ✅ Resuelto | `routes/web.php` |
| 3 | Verificar `.gitignore` incluya `.env` | 2 min | `.gitignore` |
| 4 | Escribir tests mínimos de autenticación y autorización | 2-4 hrs | `tests/Feature/` |

### Fase 2: Observaciones Importantes (Altamente recomendadas)

| # | Acción | Esfuerzo | Archivos afectados |
|---|--------|----------|--------------------|
| 5 | Eliminar `Schema::hasColumn()` y `DB::table()` del `CoordinadorController` | ✅ Resuelto | `CoordinadorController.php` |
| 6 | Eliminar fallback `@exec()` de ChromiumPdfService | ✅ Resuelto | `ChromiumPdfService.php` |
| 7 | Agregar `$hidden = ['google_id']` al modelo User | ✅ Resuelto | `User.php` |
| 8 | Eliminar trait `HandlesInformeData` | 2 min | `app/Traits/HandlesInformeData.php` |
| 9 | Limpiar `UserFactory::unverified()` | ✅ Resuelto | `UserFactory.php` |
| 10 | Extraer constante para días de bloqueo | ✅ Resuelto | `config/informes.php`, modelos, servicios |
| 11 | Sanitizar IDs con `intval` en descargas por selección | ✅ Resuelto | `AdminController.php`, `InformeExportService.php` |
| 12 | Cachear o paginar `getCatalogoCursos()` | ✅ Resuelto | `CatalogoService.php`, `CursoService.php` |

### Fase 3: Mejoras para después del lanzamiento

| # | Acción | Esfuerzo |
|---|--------|----------|
| 13 | Implementar SoftDeletes en modelos principales | 1-2 hrs |
| 14 | Agregar logging estructurado y monitoreo | 2-3 hrs |
| 15 | Configurar backups automáticos de BD | 1 hr |
| 16 | Implementar cola de jobs para generación de PDFs masivos | 3-4 hrs |
| 17 | Agregar tests de integración completos | 4-8 hrs |

---

## 🔧 Checklist de Despliegue

```
Antes del deploy:
[ ] .env configurado para producción (APP_DEBUG=false, APP_ENV=production)
[ ] APP_KEY rotado con php artisan key:generate
[ ] GOOGLE_CLIENT_SECRET rotado en Google Cloud Console
[ ] GOOGLE_REDIRECT_URI apuntando al dominio real con HTTPS
[ ] GOOGLE_SSL_CERT_PATH configurado para el servidor (o true para default)
[ ] DB_PASSWORD configurado con contraseña segura
[ ] SESSION_DRIVER=database (o redis)
[ ] SESSION_SECURE_COOKIE=true
[ ] Rate limiting configurado en rutas OAuth
[ ] .env excluido de control de versiones
[ ] php artisan config:cache ejecutado
[ ] php artisan route:cache ejecutado  
[ ] php artisan view:cache ejecutado
[ ] php artisan migrate --force ejecutado
[ ] php artisan db:seed ejecutado (usuario admin)
[ ] Chromium/Chrome/Edge instalado en el servidor
[ ] Extensión ZipArchive de PHP habilitada
[ ] Permisos de storage/ y bootstrap/cache/ configurados (775)
[ ] HTTPS configurado con certificado SSL válido
```

---

> [!IMPORTANT]
> **Tiempo estimado total para resolver bloqueos críticos:** 3-5 horas de desarrollo. Los ítems de la Fase 1 son indispensables. Los de la Fase 2 son altamente recomendados pero el sistema puede funcionar sin ellos en un despliegue inicial controlado.
