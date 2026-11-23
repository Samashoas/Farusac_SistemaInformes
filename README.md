# 🏛️ Guía Práctica de Despliegue en Debian 12 — FARUSAC Sistema de Informes

**Entorno:** Máquina Virtual Local (Debian 12 Bookworm)  
**Objetivo:** Simular con 100% de fidelidad el despliegue real en producción, utilizando un dominio real gratuito y permanente con HTTPS para validar Google OAuth institucional.  
**Restricción clave:** Sin modificar archivos de configuración del sistema operativo sensibles (como `php.ini`). Toda la configuración se maneja mediante Git, `.env`, `.htaccess` y configuración básica de Apache.

---

## 📌 1. Arquitectura de Despliegue Confirmada: Subdirectorio / Subruta (`/informes`)

El despliegue del sistema en producción se realizará de forma definitiva bajo la modalidad de **Subdirectorio / Subruta**:

* **Dominio institucional existente:** `https://farusac.com` (sitio web principal de la Facultad de Arquitectura).
* **Ruta asignada al Sistema de Informes:** `https://farusac.com/informes`
* **Entorno de práctica en tu VM local:** `https://farusac-pruebas.ngrok-free.app/informes`

### ¿Qué implica técnicamente desplegar bajo un Subdirectorio?
A diferencia de un subdominio (donde la raíz del servidor apunta directamente a la carpeta pública), en un despliegue por subdirectorio el dominio principal sirve el portal institucional y la subruta `/informes` se conecta a Laravel. Para que esto funcione de forma transparente y sin errores de rutas, esta guía implementa los 4 pilares técnicos requeridos:

1. **Directiva `Alias /informes` en Apache:** Le indica al servidor web que cualquier petición dirigida a `/informes` debe resolverse en el directorio `/var/www/Farusac_SistemaInformes/Backend/public`.
2. **Directiva `RewriteBase /informes/`:** Instruye al motor de reescritura de Apache para que todas las rutas internas de Laravel (como `/login`, `/docente/dashboard`, etc.) se resuelvan respetando el prefijo `/informes/` y nunca se vayan a la raíz del dominio principal.
3. **Variable `APP_URL` en `.env`:** Se define como `https://farusac.com/informes` (o la URL de ngrok con `/informes`). Con esto, los helpers `route()`, `url()`, la carga de estilos CSS/JS y las redirecciones del sistema anteponen automáticamente la subruta `/informes`.
4. **Google OAuth con Subruta:** Google Cloud Console requiere que el **Origen de JavaScript** sea el dominio base sin rutas (`https://farusac.com`), mientras que el **URI de Redirección Autorizado** incluya la subruta completa (`https://farusac.com/informes/auth/google/callback`).

Toda la configuración en esta guía está diseñada y optimizada al 100% para esta arquitectura de subdirectorio.

---

## 🌐 2. Dominio Real Gratuito y Permanente con HTTPS

Google OAuth rechaza direcciones IP directas (como `http://192.168.1.50`) y exige **HTTPS con un certificado SSL válido**. Además, necesitas que la URL sea **fija y permanente** para no tener que modificar Google Cloud Console cada vez que reinicies la máquina virtual.

Para lograr esto sin costo, sin registrar tarjetas y sin abrir puertos en tu router doméstico, utilizaremos **ngrok con Dominio Estático Gratuito**.

### 2.1 Obtener el Dominio Estático Gratuito en ngrok
1. Regístrate gratis en [ngrok.com](https://ngrok.com) (puedes iniciar sesión con Google o GitHub).
2. En el panel izquierdo, ve a **Cloud Edge** > **Domains** (o directo a [dashboard.ngrok.com/cloud-edge/domains](https://dashboard.ngrok.com/cloud-edge/domains)).
3. Haz clic en **Create Domain** (o *New Domain*).
4. ngrok te asignará un subdominio gratuito permanente para siempre, por ejemplo:
   ```text
   farusac-pruebas.ngrok-free.app
   ```
   *(Copia este dominio, será tu URL pública fija).*
5. En el menú izquierdo ve a **Getting Started** > **Your Authtoken** y copia tu token secreto de autenticación.

### 2.2 Instalar y Conectar ngrok en Debian 12
En la terminal de tu VM Debian:

```bash
# 1. Instalar repositorio oficial de ngrok
curl -sSL https://ngrok-agent.s3.amazonaws.com/ngrok.asc \
  | sudo tee /etc/apt/trusted.gpg.d/ngrok.asc >/dev/null
echo "deb https://ngrok-agent.s3.amazonaws.com buster main" \
  | sudo tee /etc/apt/sources.list.d/ngrok.list
sudo apt update && sudo apt install -y ngrok

# 2. Configurar tu token de autenticación
ngrok config add-authtoken TU_AUTHTOKEN_AQUI

# 3. Iniciar el túnel hacia Apache (puerto 80) usando tu dominio estático:
ngrok http 80 --url=farusac-pruebas.ngrok-free.app
```

Verás en pantalla que tu túnel está activo con HTTPS:
```text
Forwarding   https://farusac-pruebas.ngrok-free.app -> http://localhost:80
```

> [!TIP]
> Puedes dejar este comando corriendo en una pestaña separada de tu terminal o ejecutarlo en segundo plano. Tu dominio `https://farusac-pruebas.ngrok-free.app` **nunca cambiará**, lo que permite configurar Google OAuth una sola vez.

---

## ⚙️ 3. Preparación del Servidor Debian 12 (Sin tocar `php.ini`)

Todo lo requerido por Laravel y el sistema de FARUSAC se instala con paquetes estándar de Debian sin necesidad de modificar `php.ini`.

### 3.1 Instalar el Stack Web (Apache, PHP 8.2, MariaDB, Git, Composer)
Ejecuta en tu terminal de Debian:

```bash
# Actualizar lista de paquetes
sudo apt update && sudo apt upgrade -y

# Instalar Apache, MariaDB, Git, Unzip y Curl
sudo apt install -y apache2 mariadb-server git curl unzip

# Instalar PHP 8.2 y las extensiones exactas que requiere Laravel
sudo apt install -y php8.2 libapache2-mod-php8.2 php8.2-mysql php8.2-mbstring \
                    php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath

# Instalar Chromium Headless (necesario para generar los PDFs institucionales)
sudo apt install -y chromium chromium-sandbox

# Instalar Composer globalmente
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 3.2 Habilitar Módulos de Apache Indispensables
Laravel necesita `mod_rewrite` y `mod_headers` para gestionar las rutas y la seguridad:

```bash
sudo a2enmod rewrite headers
sudo systemctl restart apache2
```

---

## 🗄️ 4. Configuración y Creación de la Base de Datos (MariaDB / MySQL)

Antes de que Laravel pueda crear las tablas, el motor de base de datos debe estar activo y configurado con una base de datos vacía y un usuario con permisos asignados.

### 4.1 Verificar que el Servicio esté en Ejecución
En Debian 12, MariaDB se inicia automáticamente al instalarlo. Puedes verificar su estado con:

```bash
sudo systemctl status mariadb
```
*(Debe mostrar `active (running)` en color verde).*

### 4.2 Crear la Base de Datos y el Usuario de la Aplicación
Accede a la consola de MariaDB como superusuario:

```bash
sudo mysql -u root
```

Dentro del prompt interactivo `MariaDB [(none)]>`, ejecuta las siguientes instrucciones SQL una por una:

```sql
-- 1. Crear la base de datos con soporte completo para caracteres en español (tildes, eñes) y emojis
CREATE DATABASE farusac_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Crear un usuario exclusivo para la aplicación (reemplaza 'TuPasswordSeguro2026!' por una clave robusta)
CREATE USER 'farusac_user'@'localhost' IDENTIFIED BY 'TuPasswordSeguro2026!';

-- 3. Otorgar permisos únicamente sobre la base de datos del sistema (principio de menor privilegio)
GRANT ALL PRIVILEGES ON farusac_db.* TO 'farusac_user'@'localhost';

-- 4. Recargar los privilegios y salir
FLUSH PRIVILEGES;
EXIT;
```

### 4.3 Probar la Conexión del Nuevo Usuario
Verifica desde la terminal que el nuevo usuario pueda conectarse exitosamente a la base de datos recién creada:

```bash
mysql -u farusac_user -p farusac_db
```
*Introduce la contraseña configurada. Si entras a la consola `MariaDB [farusac_db]>`, la conexión está perfecta. Escribe `exit` para salir.*

---

## 📁 5. Despliegue de la Aplicación mediante Git Pull

Este es el proceso exacto que ejecutará el administrador del servidor.

### 5.1 Clonar el Repositorio
Clonamos el proyecto en la carpeta estándar de aplicaciones web `/var/www`:

```bash
cd /var/www
sudo git clone <URL_DE_TU_REPOSITORIO_GIT> Farusac_SistemaInformes
cd Farusac_SistemaInformes/Backend
```

### 5.2 Instalar Dependencias de PHP
```bash
sudo composer install --no-dev --optimize-autoloader
```

### 5.3 Configurar el Archivo de Entorno (`.env`)
Copia la plantilla y genera una llave criptográfica única para el servidor:

```bash
sudo cp .env.example .env
sudo php artisan key:generate
```

Edita el archivo `.env`:
```bash
sudo nano .env
```

---

### 📋 5.3.1 Desglose Completo del Archivo `.env` para Producción (¿Qué es cada cosa y por qué?)

A continuación se explica cada bloque de variables que debe tener tu archivo `.env` en el servidor de producción o en tu entorno de pruebas:

#### 1. Identidad y Seguridad Básica de la Aplicación (`APP_*`)
| Variable | Valor en Producción | ¿Qué hace y por qué se configura así? |
| :--- | :--- | :--- |
| `APP_NAME` | `"FARUSAC - Sistema de Informes"` | Nombre institucional del sistema. Aparece en correos, títulos de pestañas del navegador y encabezados. |
| `APP_ENV` | `production` | **Crítico.** Le indica a Laravel que está en un servidor real. Desactiva helpers de desarrollo y activa confirmaciones de seguridad automáticas (como evitar que alguien borre la base de datos por error). En desarrollo se usa `local`. |
| `APP_KEY` | `base64:xxxx...` | Clave de cifrado simétrico (AES-256) de 32 bytes. Se usa para cifrar cookies de sesión y tokens de seguridad. **Se genera en el servidor con `php artisan key:generate` y nunca debe compartirse ni copiarse de tu PC local.** |
| `APP_DEBUG` | `false` | **Riesgo Crítico de Seguridad.** Si se deja en `true`, cualquier error imprevisto mostrará al usuario la pantalla completa de depuración con tus contraseñas, consultas SQL y variables de entorno. En producción **siempre debe ser `false`**. |
| `APP_URL` | `https://farusac.com/informes` | URL base oficial del sistema con HTTPS y la subruta. Laravel la utiliza para generar enlaces absolutos con `route()`, cargar estilos CSS/JS y construir el callback de Google OAuth. *(En tu práctica con ngrok usarás `https://farusac-pruebas.ngrok-free.app/informes`)*. |

#### 2. Registro de Errores y Depuración (`LOG_*`)
| Variable | Valor en Producción | ¿Qué hace y por qué se configura así? |
| :--- | :--- | :--- |
| `LOG_CHANNEL` | `stack` | Canal principal de registro de eventos. |
| `LOG_STACK` | `daily` | En desarrollo suele usarse `single` (un solo archivo infinito). En producción se recomienda **`daily`** para que Laravel cree un archivo de log diario (ej. `laravel-2026-11-23.log`) y los rote automáticamente, evitando que un único archivo crezca varios gigabytes y llene el disco del servidor. |
| `LOG_LEVEL` | `error` | En desarrollo es `debug` (registra todo). En producción se configura como **`error`** o **`warning`** para registrar únicamente fallos y errores reales sin saturar el almacenamiento. |

#### 3. Base de Datos (`DB_*`)
| Variable | Valor en Producción | ¿Qué hace y por qué se configura así? |
| :--- | :--- | :--- |
| `DB_CONNECTION` | `mysql` | Driver de conexión para MySQL / MariaDB. |
| `DB_HOST` | `127.0.0.1` | Dirección IP del servidor de base de datos. Se utiliza `127.0.0.1` en lugar de `localhost` porque en Linux y Windows `localhost` a menudo intenta resolver por IPv6 o buscar un socket UNIX que puede fallar. |
| `DB_PORT` | `3306` | Puerto estándar del motor de base de datos. |
| `DB_DATABASE` | `farusac_db` | Nombre de la base de datos en MariaDB/MySQL. |
| `DB_USERNAME` | `farusac_user` | Usuario de base de datos. **Nunca uses `root` en producción.** Debe ser un usuario dedicado con permisos limitados a la base de datos del sistema. |
| `DB_PASSWORD` | `TuContraseñaSegura!` | Contraseña fuerte y exclusiva de la base de datos. |

#### 4. Sesiones y Cookies de Seguridad (`SESSION_*`)
| Variable | Valor en Producción | ¿Qué hace y por qué se configura así? |
| :--- | :--- | :--- |
| `SESSION_DRIVER` | `file` | Dónde se guardan las sesiones de usuario (`storage/framework/sessions`). Es rápido y suficiente para un servidor único. *(Si en el futuro la facultad usa un cluster con balanceador de carga, se cambia a `database`)*. |
| `SESSION_LIFETIME` | `120` | Minutos de inactividad antes de que el sistema cierre la sesión automáticamente (120 minutos = 2 horas). |
| `SESSION_SECURE_COOKIE` | `true` | **Seguridad HTTPS.** Obliga al navegador a transmitir la cookie de sesión únicamente a través de conexiones cifradas HTTPS, impidiendo que sea interceptada en redes públicas. |
| `SESSION_SAME_SITE` | `lax` | Política de cookies para mitigar ataques CSRF (Cross-Site Request Forgery). |

#### 5. Colas y Caché (`QUEUE_*`, `CACHE_*`)
| Variable | Valor en Producción | ¿Qué hace y por qué se configura así? |
| :--- | :--- | :--- |
| `QUEUE_CONNECTION` | `sync` | Las tareas se ejecutan de inmediato en el mismo ciclo de la petición. Como el sistema no requiere workers en segundo plano ni colas asíncronas, se configura como `sync` (lo que elimina la necesidad de tablas de jobs). |
| `CACHE_STORE` | `file` | Almacenamiento de caché en archivos dentro de `storage/framework/cache`. Se utiliza para almacenar en memoria el catálogo de cursos y evitar consultas masivas repetitivas a la base de datos. |

#### 6. Autenticación Google OAuth (`GOOGLE_*`)
| Variable | Valor en Producción | ¿Qué hace y por qué se configura así? |
| :--- | :--- | :--- |
| `GOOGLE_CLIENT_ID` | `xxxx.apps.googleusercontent.com` | ID público de tu proyecto en Google Cloud Console. Identifica a la aplicación ante Google. |
| `GOOGLE_CLIENT_SECRET` | `GOCSPX-xxxx...` | Llave privada secreta de Google Cloud. Permite autenticar el intercambio de tokens de sesión. **Nunca debe compartirse ni subirse a Git.** |
| `GOOGLE_REDIRECT_URI` | `https://farusac.com/informes/auth/google/callback` | Dirección exacta a donde Google devolverá al usuario autenticado. **Debe coincidir carácter por carácter** con la registrada en Google Cloud Console y con el protocolo `https://`. |
| `GOOGLE_ALLOWED_DOMAIN` | `farusac.edu.gt` | Restricción institucional estricta. Si un usuario intenta iniciar sesión con una cuenta de Google externa (ej. `@gmail.com`), el sistema rechazará el acceso automáticamente. |
| `GOOGLE_SSL_CERT_PATH` | *(Vacío)* | **Dejar completamente vacío en Linux.** En Windows se requería `C:\php\cacert.pem`. En servidores Debian/Linux, al dejarlo vacío, el sistema utiliza de forma nativa los certificados raíz seguros del sistema operativo (`/etc/ssl/certs/ca-certificates.crt`). |

#### 7. Parámetros del Sistema FARUSAC e Infraestructura
| Variable | Valor en Producción | ¿Qué hace y por qué se configura así? |
| :--- | :--- | :--- |
| `INFORMES_DIAS_BLOQUEO` | `3` | Define la cantidad de días continuos que tienen los docentes y coordinadores para editar o eliminar sus informes después de enviarlos, antes de que el sistema los bloquee para revisión. |
| `CHROMIUM_PATH` | *(Vacío)* | Ruta al binario del navegador headless para generar PDFs. Si se deja vacío, nuestro servicio autodetecta automáticamente `/usr/bin/chromium` o `/usr/bin/google-chrome` en Linux Debian. |

---

### 📄 Plantilla Completa de `.env` para Producción

Puedes copiar este bloque directamente en tu archivo `/var/www/Farusac_SistemaInformes/Backend/.env` en el servidor:

```ini
# =========================================================================
# CONFIGURACIÓN GENERAL DEL SISTEMA
# =========================================================================
APP_NAME="FARUSAC - Sistema de Informes"
APP_ENV=production
APP_KEY=base64:COPIA_O_GENERA_CON_PHP_ARTISAN_KEY_GENERATE
APP_DEBUG=false
APP_URL=https://farusac.com/informes

APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_FAKER_LOCALE=es_ES

# =========================================================================
# REGISTRO DE ERRORES (LOGS)
# =========================================================================
LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=error

# =========================================================================
# BASE DE DATOS MARIADB / MYSQL
# =========================================================================
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=farusac_db
DB_USERNAME=farusac_user
DB_PASSWORD=TuPasswordSeguroProduccion2026!

# =========================================================================
# SESIONES Y SEGURIDAD HTTPS
# =========================================================================
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

# =========================================================================
# COLAS Y CACHÉ
# =========================================================================
QUEUE_CONNECTION=sync
CACHE_STORE=file
FILESYSTEM_DISK=local

# =========================================================================
# AUTENTICACIÓN GOOGLE OAUTH INSTITUCIONAL
# =========================================================================
GOOGLE_CLIENT_ID=504974444658-xxxxxxxxxxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-xxxxxxxxxxxxxxxxxxxx
GOOGLE_REDIRECT_URI=https://farusac.com/informes/auth/google/callback
GOOGLE_ALLOWED_DOMAIN=farusac.edu.gt
GOOGLE_SSL_CERT_PATH=

# =========================================================================
# REGLAS DE NEGOCIO Y GENERACIÓN DE REPORTES PDF
# =========================================================================
INFORMES_DIAS_BLOQUEO=3
CHROMIUM_PATH=
```

### 5.4 Permisos de Directorios (Crucial para Apache)
Apache en Debian corre bajo el usuario `www-data`. Otórgale permisos exclusivos a las carpetas de escritura:

```bash
sudo chown -R www-data:www-data /var/www/Farusac_SistemaInformes/Backend/storage
sudo chown -R www-data:www-data /var/www/Farusac_SistemaInformes/Backend/bootstrap/cache
sudo chmod -R 775 /var/www/Farusac_SistemaInformes/Backend/storage
sudo chmod -R 775 /var/www/Farusac_SistemaInformes/Backend/bootstrap/cache
```

### 5.5 Construir y Levantar la Base de Datos (Migraciones y Seeders)

Una vez creada la base de datos en MariaDB (Paso 4) y configuradas las credenciales en el `.env` (Paso 5.3), Laravel se encarga de crear toda la estructura de tablas y los registros iniciales necesarios para que el sistema funcione.

#### 5.5.1 ¿Qué ocurre internamente al levantar la base de datos?
El sistema cuenta con **4 migraciones consolidadas y optimizadas** ubicadas en `database/migrations/`:
1. `0001_01_01_000000_create_users_table.php`: Crea la tabla `usuarios` (con soporte para roles de docente, coordinador y administrador, Google ID y estado), así como las tablas de restablecimiento de contraseñas y `sessions`.
2. `0001_01_01_000001_create_cache_table.php`: Crea las tablas `cache` y `cache_locks` para optimizar el rendimiento y almacenar en memoria el catálogo de cursos.
3. `0001_01_01_000002_create_jobs_table.php`: Tablas estándar de soporte de Laravel.
4. `0001_01_01_000003_create_farusac_tables.php`: Crea todas las tablas del dominio institucional de FARUSAC: `docentes`, `cursos`, `asignaciones`, `informes` (informes mensuales docentes con sus actividades) e `informes_coordinacion` (informes consolidados de coordinación).

#### 5.5.2 Ejecutar las Migraciones en Producción
Para crear todas las tablas, ejecuta desde la carpeta `Backend`:

```bash
sudo php artisan migrate --force
```

> [!IMPORTANT] ¿Por qué es obligatorio el parámetro `--force` en producción?
> Cuando `APP_ENV=production`, Laravel activa un mecanismo de seguridad estricto que bloquea la ejecución de migraciones y detiene el proceso solicitando una confirmación manual interactiva:
> `Application In Production! Do you really wish to run this command? [yes/no]`
> El parámetro `--force` le confirma a Laravel que la acción es deliberada y autorizada, evitando que el comando falle o se quede esperando indefinidamente en despliegues automatizados o terminales sin prompt interactivo.

#### 5.5.3 Poblar Datos Iniciales con Seeders
Una vez creadas las tablas, debes generar el usuario Administrador inicial para poder ingresar al sistema y gestionar a los docentes y coordinadores:

```bash
sudo php artisan db:seed --force
```

* **¿Qué hace `DatabaseSeeder`?**
  Crea la cuenta del Administrador principal del sistema:
  - **Correo:** `admin@farusac.edu.gt`
  - **Nombre:** Administrador FARUSAC
  - **Rol:** `administrador`
  - **Estado:** `activo`
  *El seeder utiliza `firstOrCreate()`, lo que significa que es completamente seguro ejecutarlo: si el administrador ya existe, no causará ningún error de duplicidad ni alterará información existente.*

#### 5.5.4 Verificación del Estado de la Base de Datos
Puedes confirmar que la base de datos se levantó a la perfección con los siguientes métodos:

1. **Verificar el estado de las migraciones desde Laravel:**
   ```bash
   sudo php artisan migrate:status
   ```
   *Todas las 4 migraciones deben aparecer con `Ran? [Yes]` y su respectivo número de lote (Batch).*

2. **Verificar las tablas directamente en MariaDB:**
   ```bash
   mysql -u farusac_user -p farusac_db -e "SHOW TABLES;"
   ```
   *(Ingresa la contraseña del usuario. Verás el listado de todas las tablas: `usuarios`, `cursos`, `docentes`, `asignaciones`, `informes`, etc.).*

3. **Verificar el usuario administrador creado:**
   ```bash
   mysql -u farusac_user -p farusac_db -e "SELECT id, nombre, correo, rol, estado FROM usuarios;"
   ```
   *(Confirmará la existencia de `admin@farusac.edu.gt` con rol `administrador`).*

#### 5.5.5 Regla de Oro en Producción: `migrate --force` vs `migrate:fresh`

> [!CAUTION] ¡PELIGRO CRÍTICO: NUNCA USES `migrate:fresh` EN PRODUCCIÓN!
> - **`php artisan migrate --force`**: Es seguro para producción. Lee la tabla `migrations` y **únicamente ejecuta los archivos nuevos** que aún no han sido aplicados, sin tocar ni borrar los datos existentes.
> - **`php artisan migrate:fresh` o `migrate:refresh`**: **BORRA (DROP) TODAS LAS TABLAS DE LA BASE DE DATOS** antes de volver a crearlas. Si alguien ejecuta este comando en producción, **se perderán irreversiblemente todos los informes mensuales, firmas y usuarios** de la facultad.

#### 5.5.6 Diagnóstico de Problemas Comunes de Conexión
Si al ejecutar `php artisan migrate` obtienes algún error:

- **Error `SQLSTATE[HY000] [2002] Connection refused`:**
  - El motor MariaDB está apagado o la IP está mal configurada.
  - Verifica que el servicio esté activo: `sudo systemctl status mariadb`. Si está inactivo, inícialo con `sudo systemctl start mariadb`.
  - Asegúrate de que en el archivo `.env` tengas `DB_HOST=127.0.0.1` (evita usar `localhost` ya que puede intentar sockets UNIX no configurados o IPv6 `::1`).
- **Error `SQLSTATE[HY000] [1045] Access denied for user`:**
  - El usuario o contraseña en `.env` no coinciden con los otorgados en el Paso 4.2.
  - Prueba ingresar manualmente con `mysql -u farusac_user -p farusac_db`. Si no puedes ingresar, vuelve a asignar los permisos desde `sudo mysql -u root` con:
    ```sql
    ALTER USER 'farusac_user'@'localhost' IDENTIFIED BY 'TuPasswordSeguro2026!';
    GRANT ALL PRIVILEGES ON farusac_db.* TO 'farusac_user'@'localhost';
    FLUSH PRIVILEGES;
    ```

---

## 🌐 6. Configurar Apache para la Subruta `/informes`

Para simular exactamente `farusac.com/informes` sin tocar la configuración global de Apache:

1. Crea un archivo de configuración para el sitio:
   ```bash
   sudo nano /etc/apache2/sites-available/farusac.conf
   ```

2. Agrega la configuración utilizando la directiva `Alias`:

```apache
<VirtualHost *:80>
    ServerName localhost

    # Redirigir la subruta /informes directamente a la carpeta public de Laravel
    Alias /informes /var/www/Farusac_SistemaInformes/Backend/public

    <Directory /var/www/Farusac_SistemaInformes/Backend/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted

        # Configuración para que Laravel maneje las rutas bajo /informes
        RewriteEngine On
        RewriteBase /informes/
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^ index.php [L]
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/farusac_error.log
    CustomLog ${APACHE_LOG_DIR}/farusac_access.log combined
</VirtualHost>
```

3. Habilita el sitio y recarga Apache:
   ```bash
   sudo a2ensite farusac.conf
   sudo apache2ctl configtest    # Debe responder "Syntax OK"
   sudo systemctl reload apache2
   ```

---

## 🔑 7. Configuración Completa en Google Cloud Console

Sigue este paso a paso detallado para dar de alta tu URL gratuita en Google:

### 7.1 Crear o Seleccionar el Proyecto
1. Ingresa a [Google Cloud Console](https://console.cloud.google.com/).
2. En la barra superior haz clic en el selector de proyectos y haz clic en **Proyecto Nuevo** (ej. nombre: `FARUSAC-Informes`).

### 7.2 Configurar la Pantalla de Consentimiento de OAuth
1. En el menú de navegación izquierdo, dirígete a **APIs y servicios** > **Pantalla de consentimiento de OAuth**.
2. **Tipo de usuario (User Type):**
   * **Interno:** Selecciónalo si tu cuenta de Google pertenece a una organización de Google Workspace de la USAC (`@farusac.edu.gt` o `@profesor.usac.edu.gt`). Con esta opción solo usuarios institucionales pueden iniciar sesión y no requiere verificación de Google.
   * **Externo:** Selecciónalo si estás haciendo pruebas con correos `@gmail.com` personales.
3. Haz clic en **Crear**.
4. Completa la información básica requerida:
   * **Nombre de la aplicación:** `FARUSAC Sistema de Informes`
   * **Correo de soporte del usuario:** Tu correo institucional o personal.
   * **Logotipo de la aplicación:** Opcional.
   * **Datos de contacto del desarrollador:** Tu correo.
5. Haz clic en **Guardar y continuar**.
6. En la pestaña **Permisos (Scopes)**:
   * No agregues permisos restringidos; Laravel Socialite solo requiere los permisos básicos por defecto (`email`, `profile`, `openid`).
   * Haz clic en **Guardar y continuar**.
7. En la pestaña **Usuarios de prueba (Test Users)** *(Solo aplica si seleccionaste "Externo")*:
   * Haz clic en **+ ADD USERS**.
   * Escribe las direcciones de correo electrónico que usarás para probar el inicio de sesión.
   * Haz clic en **Guardar y continuar**.

### 7.3 Crear el ID de Cliente OAuth 2.0
1. En el menú lateral izquierdo, ve a **APIs y servicios** > **Credenciales**.
2. En la barra superior, haz clic en **+ CREAR CREDENCIALES** > **ID de cliente de OAuth**.
3. En el campo **Tipo de aplicación**, selecciona: **Aplicación web**.
4. En **Nombre**: `FARUSAC Web Client`.
5. **Configurar Orígenes de JavaScript autorizados:**
   * Haz clic en **+ AGREGAR URI**.
   * Pega la URL base de tu dominio ngrok:
     ```text
     https://farusac-pruebas.ngrok-free.app
     ```
     > [!IMPORTANT]
     > Google **no permite barras `/` al final ni subdirectorios** en este campo. Debe ser estrictamente el protocolo y nombre de dominio base (`https://tu-dominio.ngrok-free.app`).

6. **Configurar URIs de redireccionamiento autorizados:**
   * Haz clic en **+ AGREGAR URI**.
   * Pega la URL completa del callback con la subruta `/informes`:
     ```text
     https://farusac-pruebas.ngrok-free.app/informes/auth/google/callback
     ```
   * *(Opcional)* Puedes agregar también la versión sin subruta por si deseas probar en la raíz:
     ```text
     https://farusac-pruebas.ngrok-free.app/auth/google/callback
     ```

7. Haz clic en el botón azul **Crear**.
8. Se abrirá una ventana emergente titulada **Cliente de OAuth creado**:
   * Copia el **ID de cliente** (formato: `xxxxxxxxxxxx-xxxxxxxxxxxxxxxx.apps.googleusercontent.com`).
   * Copia el **Secreto de cliente** (formato: `GOCSPX-xxxxxxxxxxxxxxxxxxxxxxxx`).

---

## ⚡ 8. Sincronizar Laravel y Probar el Sistema

1. En tu VM Debian, pega las credenciales recién obtenidas en tu archivo `/var/www/Farusac_SistemaInformes/Backend/.env`:
   ```ini
   GOOGLE_CLIENT_ID=xxxxxxxxxxxx-xxxxxxxxxxxxxxxx.apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=GOCSPX-xxxxxxxxxxxxxxxxxxxxxxxx
   GOOGLE_REDIRECT_URI=https://farusac-pruebas.ngrok-free.app/informes/auth/google/callback
   ```

2. Limpia y optimiza la caché de configuración:
   ```bash
   cd /var/www/Farusac_SistemaInformes/Backend
   sudo php artisan optimize:clear
   sudo php artisan optimize
   ```

3. **Prueba de Acceso:**
   * Abre un navegador web en tu computadora Windows o teléfono celular.
   * Navega a: `https://farusac-pruebas.ngrok-free.app/informes/login`
   * Haz clic en **"Iniciar sesión con Google"**.
   * Google te solicitará autorización y, tras aceptar, te redirigirá a tu sistema en Debian 12 con la sesión iniciada correctamente.

---

## 🔄 9. Script Automatizado para Futuras Actualizaciones (`deploy.sh`)

Para que el administrador pueda desplegar actualizaciones futuras en un solo paso, crea un script en `/var/www/Farusac_SistemaInformes/deploy.sh`:

```bash
sudo nano /var/www/Farusac_SistemaInformes/deploy.sh
```

Pega el siguiente contenido:

```bash
#!/bin/bash
set -e

echo "🚀 Iniciando despliegue de FARUSAC Sistema de Informes..."

cd /var/www/Farusac_SistemaInformes

# 1. Bajar últimos cambios de Git
echo "📥 Descargando código del repositorio..."
git pull origin main

cd Backend

# 2. Instalar/actualizar dependencias
echo "📦 Actualizando paquetes de Composer..."
composer install --no-dev --optimize-autoloader --no-interaction

# 3. Ejecutar migraciones pendientes
echo "🗄️ Ejecutando migraciones de base de datos..."
php artisan migrate --force

# 4. Limpiar y reconstruir caché de producción
echo "⚡ Optimizando caché de Laravel..."
php artisan optimize

# 5. Asegurar permisos correctos
echo "🔒 Verificando permisos de almacenamiento..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "✅ ¡Despliegue completado con éxito!"
```

Dale permisos de ejecución:
```bash
sudo chmod +x /var/www/Farusac_SistemaInformes/deploy.sh
```

A partir de este momento, cada vez que haya cambios en el repositorio, el administrador solo ejecutará:
```bash
sudo /var/www/Farusac_SistemaInformes/deploy.sh
```
