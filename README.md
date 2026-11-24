# Guía de Despliegue en Debian 12 — FARUSAC Sistema de Informes

* **Arquitectura:** Subdirectorio institucional `/informes`.
* **Stack:** Apache 2.4, PHP 8.4, MariaDB 10.11+, Chromium (PDFs) y Composer.

---

## Requisitos Previos
* Servidor con Debian 12 y usuario con privilegios `sudo`.
* Dominio con certificado SSL/HTTPS activo.
* Proyecto en Google Cloud Console para Google OAuth.

---

## 1. Instalación del Stack Web (PHP 8.4, Apache, MariaDB y Chromium)

```bash
# 1.1 Actualizar repositorios e instalar paquetes base
sudo apt update && sudo apt upgrade -y
sudo apt install -y apt-transport-https ca-certificates curl lsb-release gnupg git unzip apache2 mariadb-server chromium chromium-sandbox

# 1.2 Agregar repositorio oficial de PHP 8.4 (deb.sury.org)
sudo curl -sSLo /usr/share/keyrings/deb.sury.org-php.gpg https://packages.sury.org/php/apt.gpg
echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/php.list
sudo apt update

# 1.3 Instalar PHP 8.4 y módulos necesarios
sudo apt install -y php8.4 php8.4-cli php8.4-common libapache2-mod-php8.4 \
                    php8.4-mysql php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip php8.4-bcmath

# 1.4 Habilitar versión por defecto y módulos de Apache
sudo update-alternatives --set php /usr/bin/php8.4
sudo a2enmod php8.4 rewrite headers
sudo systemctl restart apache2

# 1.5 Instalar Composer globalmente
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## 2. Configuración de Base de Datos (MariaDB)

```bash
# 2.1 Ingresar a MariaDB
sudo mysql -u root
```

Ejecutar las siguientes instrucciones SQL:
```sql
-- Crear base de datos en UTF-8
CREATE DATABASE farusac_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear usuario con contraseña segura
CREATE USER 'farusac_user'@'localhost' IDENTIFIED BY 'TuPasswordSeguro2026!';
GRANT ALL PRIVILEGES ON farusac_db.* TO 'farusac_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 3. Clonar Repositorio e Instalar Dependencias

```bash
# 3.1 Clonar el proyecto en /var/www
cd /var/www
sudo git clone <URL_DE_TU_REPOSITORIO_GIT> Farusac_SistemaInformes
cd Farusac_SistemaInformes/Backend

# 3.2 Instalar dependencias de producción
sudo composer install --no-dev --optimize-autoloader
```

---

## 4. Configurar Variables de Entorno (`.env`)

```bash
# 4.1 Crear archivo .env y generar clave criptográfica
sudo cp .env.example .env
sudo php artisan key:generate

# 4.2 Abrir archivo para edición
sudo nano .env
```

Configurar los siguientes valores:
```ini
# --- Aplicación ---
APP_NAME="FARUSAC - Sistema de Informes"
APP_ENV=production
APP_DEBUG=false

# URL de Producción Real:
APP_URL=https://farusac.com/informes
# (En entorno de pruebas VM usar: APP_URL=https://tu-dominio.ngrok-free.app/informes)

# --- Base de Datos ---
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=farusac_db
DB_USERNAME=farusac_user
DB_PASSWORD=TuPasswordSeguro2026!

# --- Sesiones y Seguridad HTTPS ---
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

# --- Google OAuth Institucional ---
GOOGLE_CLIENT_ID=xxxxxxxxxxxx-xxxxxxxxxxxxxxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-xxxxxxxxxxxxxxxxxxxxxxxx

# URI de Producción Real:
GOOGLE_REDIRECT_URI=https://farusac.com/informes/auth/google/callback
# (En entorno de pruebas VM usar: GOOGLE_REDIRECT_URI=https://tu-dominio.ngrok-free.app/informes/auth/google/callback)

GOOGLE_ALLOWED_DOMAIN=farusac.edu.gt
GOOGLE_SSL_CERT_PATH=

# --- Reportes PDF y Chromium ---
INFORMES_DIAS_BLOQUEO=3
CHROMIUM_PATH=
```
> **Nota:** Dejar `GOOGLE_SSL_CERT_PATH` y `CHROMIUM_PATH` vacíos en Linux; el sistema autodetecta los certificados y el binario de Chromium.

---

## 5. Permisos de Directorios de Escritura

```bash
# Asignar usuario www-data y permisos 775
sudo chown -R www-data:www-data /var/www/Farusac_SistemaInformes/Backend/storage
sudo chown -R www-data:www-data /var/www/Farusac_SistemaInformes/Backend/bootstrap/cache
sudo chmod -R 775 /var/www/Farusac_SistemaInformes/Backend/storage
sudo chmod -R 775 /var/www/Farusac_SistemaInformes/Backend/bootstrap/cache
```

---

## 6. Migraciones y Datos Iniciales

```bash
cd /var/www/Farusac_SistemaInformes/Backend

# Crear tablas del sistema
sudo php artisan migrate --force

# Crear usuario Administrador inicial (admin@farusac.edu.gt)
sudo php artisan db:seed --force
```

---

## 7. Configuración de Apache para el Subdirectorio `/informes`

Selecciona según tu entorno:

### Opción 7.A: Servidor de Producción Real (`farusac.com/informes`)
*(El servidor ya aloja el sitio web principal de la facultad en Apache; **no** se desactiva ningún sitio).*

1. Edita el VirtualHost SSL existente del dominio institucional (ej. `/etc/apache2/sites-available/farusac-ssl.conf` o el sitio activo en puerto `443`):
   ```bash
   sudo nano /etc/apache2/sites-available/farusac-ssl.conf
   ```

2. Agrega dentro del bloque `<VirtualHost *:443>` lo siguiente:
   ```apache
   # --- Módulo Sistema de Informes FARUSAC ---
   Alias /informes /var/www/Farusac_SistemaInformes/Backend/public

   <Directory /var/www/Farusac_SistemaInformes/Backend/public>
       Options -Indexes +FollowSymLinks
       AllowOverride All
       Require all granted

       RewriteEngine On
       RewriteBase /informes/
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteRule ^ index.php [L]
   </Directory>
   ```

3. Recarga Apache:
   ```bash
   sudo apache2ctl configtest
   sudo systemctl reload apache2
   ```

> **Alternativa por Enlace Simbólico en Producción:**  
> Si el sitio principal tiene su raíz en `/var/www/html`, basta con crear el enlace simbólico directo:
> ```bash
> sudo ln -s /var/www/Farusac_SistemaInformes/Backend/public /var/www/html/informes
> ```

---

### Opción 7.B: Entorno de Pruebas en VM (ngrok)
*(La máquina virtual solo se usa para simular el sistema).*

1. Crear archivo de configuración:
   ```bash
   sudo nano /etc/apache2/sites-available/farusac.conf
   ```

2. Pegar contenido:
   ```apache
   <VirtualHost *:80>
       ServerName localhost
       ServerAlias *

       Alias /informes /var/www/Farusac_SistemaInformes/Backend/public

       <Directory /var/www/Farusac_SistemaInformes/Backend/public>
           Options -Indexes +FollowSymLinks
           AllowOverride All
           Require all granted

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

3. Activar sitio y desactivar el default:
   ```bash
   sudo a2dissite 000-default.conf
   sudo a2ensite farusac.conf
   sudo apache2ctl configtest
   sudo systemctl reload apache2
   ```

---

## 8. Configuración en Google Cloud Console (OAuth 2.0)

En **APIs y servicios > Credenciales**, editar el **ID de cliente de OAuth 2.0**:

### Para Producción Real (`farusac.com`):
* **Orígenes de JavaScript autorizados:**
  `https://farusac.com` *(estrictamente sin subruta ni barra final)*
* **URI de redireccionamiento autorizados:**
  `https://farusac.com/informes/auth/google/callback`

### Para Entorno de Pruebas (ngrok):
* **Orígenes de JavaScript autorizados:**
  `https://tu-dominio.ngrok-free.app`
* **URI de redireccionamiento autorizados:**
  `https://tu-dominio.ngrok-free.app/informes/auth/google/callback`

---

## 9. Optimización de Caché y Prueba Final

```bash
cd /var/www/Farusac_SistemaInformes/Backend

# Compilar caché de rutas, configuración y vistas
sudo php artisan optimize

# Probar en navegador:
# Producción: https://farusac.com/informes/login
# Pruebas:    https://tu-dominio.ngrok-free.app/informes/login
```

---

## 10. Script para Actualizaciones Futuras (`deploy.sh`)

Para aplicar cambios futuros en un solo comando:

```bash
sudo nano /var/www/Farusac_SistemaInformes/deploy.sh
```

```bash
#!/bin/bash
set -e

echo ">>>>>>>> Iniciando despliegue de actualización... <<<<<<<<"
cd /var/www/Farusac_SistemaInformes

# 1. Bajar cambios de Git
git pull origin main

cd Backend

# 2. Actualizar dependencias
composer install --no-dev --optimize-autoloader --no-interaction

# 3. Aplicar migraciones pendientes
php artisan migrate --force

# 4. Optimizar caché de Laravel
php artisan optimize

# 5. Asegurar permisos correctos
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo ">>>>>>>> Actualización completada exitosamente! <<<<<<<<"
```

```bash
# Dar permisos de ejecución al script
sudo chmod +x /var/www/Farusac_SistemaInformes/deploy.sh
```

Uso:
```bash
sudo /var/www/Farusac_SistemaInformes/deploy.sh
```

---

## Posibles fallos

| Síntoma | Causa Probable | Solución Inmediata |
| :--- | :--- | :--- |
| **404 Not Found (Apache/2.4.71)** | `000-default.conf` intercepta la petición (en VM) o falta la directiva `Alias /informes` en el VirtualHost activo | Verificar `Alias /informes` en el VirtualHost SSL activo y recargar Apache (`sudo systemctl reload apache2`) |
| **405 Method Not Allowed** | Falta `RewriteBase /informes/` en la configuración de Apache | Agregar `RewriteBase /informes/` dentro del bloque `<Directory>` de Apache |
| **Error 500 al guardar/subir** | Permisos de escritura incorrectos en `storage` | `sudo chown -R www-data:www-data storage bootstrap/cache && sudo chmod -R 775 storage bootstrap/cache` |
| **Error de versión PHP en Composer** | PHP 8.4 no está activo en la consola | `sudo update-alternatives --set php /usr/bin/php8.4` |
| **Cambios no visibles en navegador** | Caché de vistas o configuración activa | `sudo php artisan optimize:clear && sudo php artisan optimize` |
