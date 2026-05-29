<?php
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            );
        } catch (PDOException $e) {
            die('<div style="direction:rtl;font-family:Arial;padding:30px;background:#fee;border:2px solid #f00;border-radius:8px;margin:20px">
                <h3>❌ خطأ في الاتصال بقاعدة البيانات</h3>
                <p>تأكد من تشغيل XAMPP وإنشاء قاعدة البيانات <strong>pos_system</strong></p>
                <p style="color:#999;font-size:13px">' . $e->getMessage() . '</p>
            </div>');
        }
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConn(): PDO {
        return $this->conn;
    }

    public function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchOne(string $sql, array $params = []): array|false {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchColumn(string $sql, array $params = []): mixed {
        return $this->query($sql, $params)->fetchColumn();
    }

    public function insert(string $table, array $data): int {
        $cols   = implode(', ', array_keys($data));
        $places = implode(', ', array_fill(0, count($data), '?'));
        $this->query("INSERT INTO {$table} ({$cols}) VALUES ({$places})", array_values($data));
        return (int)$this->conn->lastInsertId();
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): int {
        $set = implode(', ', array_map(fn($k) => "{$k} = ?", array_keys($data)));
        $stmt = $this->query(
            "UPDATE {$table} SET {$set} WHERE {$where}",
            array_merge(array_values($data), $whereParams)
        );
        return $stmt->rowCount();
    }

    public function delete(string $table, string $where, array $params = []): int {
        return $this->query("DELETE FROM {$table} WHERE {$where}", $params)->rowCount();
    }

    public function beginTransaction(): void { $this->conn->beginTransaction(); }
    public function commit(): void           { $this->conn->commit(); }
    public function rollBack(): void         { $this->conn->rollBack(); }
    public function lastInsertId(): int      { return (int)$this->conn->lastInsertId(); }

    private function __clone() {}
}
