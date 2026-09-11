<?php
/**
 * MySQL/MariaDB connection + first-run schema bootstrap.
 *
 * Schema is scalable by design: core tables (schema_migrations, admin_users)
 * are created here, and everything project-specific lives in numbered SQL
 * files under database/migrations/. Any file not yet recorded in
 * schema_migrations is executed automatically on the next request — no
 * separate deploy step is needed. See AGENT.md for the migration convention.
 */

final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);

        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Emulated prepares (PDO's default) so a named placeholder like :q can be
            // reused multiple times in one query, e.g. across several LIKE clauses.
            PDO::ATTR_EMULATE_PREPARES => true,
        ]);

        self::$pdo = $pdo;

        self::bootstrap(); // idempotent, safe to run every request

        return $pdo;
    }

    private static function bootstrap(): void
    {
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS schema_migrations (
                filename VARCHAR(180) PRIMARY KEY,
                applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS admin_users (
                id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                username VARCHAR(60) NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_username (username)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        self::runPendingMigrations();
        self::seedAdmin();
    }

    /**
     * Applies any database/migrations/*.sql file not yet recorded in
     * schema_migrations, in filename order (hence the 0001_, 0002_... prefix
     * convention). Each file may contain multiple ";"-separated statements —
     * keep migrations simple (no semicolons inside string/default literals).
     */
    private static function runPendingMigrations(): void
    {
        $dir = BASE_DIR . '/database/migrations';
        if (!is_dir($dir)) {
            return;
        }

        $applied = self::$pdo->query('SELECT filename FROM schema_migrations')->fetchAll(PDO::FETCH_COLUMN);
        $files = glob($dir . '/*.sql');
        sort($files);

        foreach ($files as $file) {
            $name = basename($file);
            if (in_array($name, $applied, true)) {
                continue;
            }

            $sql = file_get_contents($file);
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            foreach ($statements as $statement) {
                self::$pdo->exec($statement);
            }

            $stmt = self::$pdo->prepare('INSERT INTO schema_migrations (filename) VALUES (?)');
            $stmt->execute([$name]);
        }
    }

    private static function seedAdmin(): void
    {
        $count = (int) self::$pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
        if ($count > 0) {
            return;
        }

        $password = bin2hex(random_bytes(6)); // 12-char random hex password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = self::$pdo->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)');
        $stmt->execute(['admin', $hash]);

        // Written once so the operator can retrieve it, then delete this file.
        file_put_contents(
            DATA_DIR . '/INITIAL_ADMIN_PASSWORD.txt',
            "username: admin\npassword: {$password}\n\nDelete this file after logging in and changing the password.\n"
        );
    }
}
