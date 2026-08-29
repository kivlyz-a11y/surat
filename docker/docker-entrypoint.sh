#!/bin/bash
set -e

echo "=== Memulai Inisialisasi Aplikasi SIM-SURAT di Coolify/Docker ==="

# Pastikan direktori writable dan uploads tersedia
mkdir -p /var/www/html/writable/cache \
         /var/www/html/writable/logs \
         /var/www/html/writable/session \
         /var/www/html/writable/uploads/surat

# Atur hak akses permission
chown -R www-data:www-data /var/www/html/writable
chmod -R 775 /var/www/html/writable

# Ekspor semua environment variables ke file .env agar terbaca sempurna oleh Apache & PHP
DB_TARGET="${DB_HOST:-${database_default_hostname:-localhost}}"
DB_NAME_TARGET="${DB_NAME:-${DB_DATABASE:-${database_default_database:-db_nomor_surat}}}"
DB_USER_TARGET="${DB_USER:-${database_default_username:-root}}"
DB_PASS_TARGET="${DB_PASS:-${DB_PASSWORD:-${database_default_password:-}}}"
DB_PORT_TARGET="${DB_PORT:-${database_default_port:-3306}}"
DB_DRIVER_TARGET="${DB_DRIVER:-${database_default_DBDriver:-MySQLi}}"
BASE_URL_TARGET="${APP_BASEURL:-${app_baseURL:-${APP_URL:-http://localhost/}}}"
ENV_TARGET="${CI_ENVIRONMENT:-production}"

cat <<EOF > /var/www/html/.env
CI_ENVIRONMENT=${ENV_TARGET}

app.baseURL=${BASE_URL_TARGET}
APP_BASEURL=${BASE_URL_TARGET}
app.appTimezone=Asia/Jakarta

database.default.hostname=${DB_TARGET}
database.default.database=${DB_NAME_TARGET}
database.default.username=${DB_USER_TARGET}
database.default.password=${DB_PASS_TARGET}
database.default.DBDriver=${DB_DRIVER_TARGET}
database.default.port=${DB_PORT_TARGET}
database.default.charset=utf8mb4
database.default.DBCollat=utf8mb4_unicode_ci

DB_HOST=${DB_TARGET}
DB_NAME=${DB_NAME_TARGET}
DB_USER=${DB_USER_TARGET}
DB_PASS=${DB_PASS_TARGET}
DB_PORT=${DB_PORT_TARGET}
DB_DRIVER=${DB_DRIVER_TARGET}

security.csrfProtection=session
security.tokenName=csrf_token_surat
security.cookieName=csrf_cookie_surat
security.expires=7200
security.regenerate=true
security.redirect=false
EOF

chown www-data:www-data /var/www/html/.env
chmod 644 /var/www/html/.env

# Tunggu database siap jika variabel DB host tersedia
if [ -n "$DB_TARGET" ] && [ "$DB_TARGET" != "localhost" ]; then
    echo "Menunggu koneksi ke database di $DB_TARGET:$DB_PORT_TARGET..."
    
    # Simple retry loop with PHP connection test
    MAX_TRIES=30
    COUNT=0
    until php -r "
        \$mysqli = @new mysqli('$DB_TARGET', '$DB_USER_TARGET', '$DB_PASS_TARGET', '$DB_NAME_TARGET', (int)'$DB_PORT_TARGET');
        if (\$mysqli->connect_error) { exit(1); }
        exit(0);
    " 2>/dev/null || [ $COUNT -eq $MAX_TRIES ]; do
        COUNT=$((COUNT+1))
        echo "Database belum siap, mencoba kembali ($COUNT/$MAX_TRIES)..."
        sleep 2
    done

    if [ $COUNT -lt $MAX_TRIES ]; then
        echo "Koneksi database berhasil!"
        
        # Jalankan migrasi database otomatis
        echo "Menjalankan migrasi tabel database..."
        php spark migrate --force || true

        # Jalankan seeder jika database masih baru / belum ada user
        php -r "
            require '/var/www/html/app/Config/Paths.php';
            \$paths = new Config\Paths();
            require \$paths->systemDirectory . '/Boot.php';
            \CodeIgniter\Boot::bootTest(\$paths);
            \$db = \Config\Database::connect();
            \$userCount = \$db->table('users')->countAllResults();
            if (\$userCount == 0) {
                echo 'Database kosong, menjalankan seeder awal... ' . PHP_EOL;
                passthru('php spark db:seed SuratSeeder');
            } else {
                echo 'Data awal sudah tersedia (Users count: ' . \$userCount . ').' . PHP_EOL;
            }
        " 2>/dev/null || true
    else
        echo "Peringatan: Tidak dapat terhubung ke database dalam batas waktu. Aplikasi tetap dijalankan."
    fi
fi

# Bersihkan cache framework
php spark cache:clear 2>/dev/null || true

echo "=== Inisialisasi Selesai. Menjalankan Apache Web Server... ==="
exec "$@"
