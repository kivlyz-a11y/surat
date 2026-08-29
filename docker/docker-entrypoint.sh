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

# Tunggu database siap jika variabel DB host tersedia
if [ -n "$database_default_hostname" ] || [ -n "$DB_HOST" ]; then
    DB_TARGET="${database_default_hostname:-${DB_HOST:-db}}"
    DB_PORT_TARGET="${database_default_port:-${DB_PORT:-3306}}"
    echo "Menunggu koneksi ke database di $DB_TARGET:$DB_PORT_TARGET..."
    
    # Simple retry loop with PHP connection test
    MAX_TRIES=30
    COUNT=0
    until php -r "
        \$host = getenv('database_default_hostname') ?: (getenv('DB_HOST') ?: 'db');
        \$user = getenv('database_default_username') ?: (getenv('DB_USER') ?: 'root');
        \$pass = getenv('database_default_password') ?: (getenv('DB_PASS') ?: (getenv('DB_PASSWORD') ?: ''));
        \$db   = getenv('database_default_database') ?: (getenv('DB_NAME') ?: (getenv('DB_DATABASE') ?: ''));
        \$port = (int)(getenv('database_default_port') ?: (getenv('DB_PORT') ?: 3306));
        \$mysqli = @new mysqli(\$host, \$user, \$pass, \$db, \$port);
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
