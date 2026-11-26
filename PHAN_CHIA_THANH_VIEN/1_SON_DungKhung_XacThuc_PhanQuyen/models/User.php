<?php
require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {
    private static bool $adminChecked = false;

    public function __construct() {
        parent::__construct();
        if ($this->db && !self::$adminChecked) {
            self::$adminChecked = true;
            $this->ensureAdminExists();
        }
    }

    private function ensureAdminExists(): void {
        try {
            // Đảm bảo luôn có ít nhất 1 tài khoản Admin duan1@gmail.com
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
            $stmt->execute();
            if ((int)$stmt->fetchColumn() === 0) {
                $adminHash = password_hash('123456@', PASSWORD_DEFAULT);
                $this->db->exec("INSERT INTO users (fullname, email, phone, password, role, address, status) VALUES 
                    ('Quản trị viên Admin', 'duan1@gmail.com', '0988888888', '{$adminHash}', 'admin', 'Hà Nội', 'active');");
            }
        } catch (Exception $e) {}
    }

    public function login(string $email, string $password): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Kiểm tra trạng thái tài khoản: bị khóa hoặc cấm
            if (isset($user['status']) && in_array($user['status'], ['blocked', 'banned', 'inactive'])) {
                $_SESSION['login_error'] = 'Tài khoản của bạn đã bị tạm khóa. Vui lòng liên hệ Admin để được hỗ trợ!';
                return null;
            }

            // Xác thực mật khẩu duy nhất bằng chuẩn Bcrypt hash (password_verify)
            if (password_verify($password, $user['password'])) {
                unset($user['password']);
                return $user;
            }
        }
        return null;
    }

    public function register(string $fullname, string $email, string $phone, string $password, string $address = ''): int|false {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (fullname, email, phone, password, role, address) 
                VALUES (?, ?, ?, ?, 'customer', ?)";
        $stmt = $this->db->prepare($sql);
        $res = $stmt->execute([$fullname, $email, $phone, $hash, $address]);
        if ($res) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return ((int)$stmt->fetchColumn()) > 0;
    }

    /**
     * Kiểm tra số điện thoại đã thuộc về tài khoản khác chưa (chuẩn hóa bỏ dấu chấm, cách, gạch)
     */
    public function phoneExists(string $phone, ?int $exceptUserId = null): bool {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if ($clean === '') return false;
        $sql = "SELECT COUNT(*) FROM users WHERE REPLACE(REPLACE(REPLACE(phone, '.', ''), ' ', ''), '-', '') = ?";
        $params = [$clean];
        if ($exceptUserId !== null) {
            $sql .= " AND id <> ?";
            $params[] = $exceptUserId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return ((int)$stmt->fetchColumn()) > 0;
    }

    /**
     * Khóa / mở khóa tài khoản khách hàng (Admin)
     */
    public function setStatus(int $id, string $status): bool {
        if (!in_array($status, ['active', 'blocked'], true)) return false;
        $stmt = $this->db->prepare("UPDATE users SET status = ? WHERE id = ? AND role != 'admin'");
        return $stmt->execute([$status, $id]);
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT id, fullname, email, phone, role, address, status, created_at FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function getByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT id, fullname, email, phone, role, address, status, created_at FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function updateProfile(int $id, array $data): bool {
        $sql = "UPDATE users SET fullname = ?, phone = ?, address = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['fullname'],
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $id
        ]);
    }

    public function verifyUserPassword(int $userId, string $rawPassword): bool {
        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $hash = $stmt->fetchColumn();
        return $hash && password_verify($rawPassword, $hash);
    }

    public function updatePassword(int $userId, string $newPassword): bool {
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$newHash, $userId]);
    }

    public function setResetToken(string $email, string $token, string $expiresAt): bool {
        $this->ensureResetTokenColumns();
        $stmt = $this->db->prepare("UPDATE users SET reset_token = ?, reset_expires_at = ? WHERE email = ? AND role = 'customer'");
        return $stmt->execute([$token, $expiresAt, $email]);
    }

    public function getUserByResetToken(string $token): ?array {
        $this->ensureResetTokenColumns();
        if (empty($token)) return null;
        $stmt = $this->db->prepare("SELECT id, fullname, email, reset_expires_at FROM users WHERE reset_token = ? AND reset_expires_at > NOW() LIMIT 1");
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function clearResetToken(int $userId): bool {
        $this->ensureResetTokenColumns();
        $stmt = $this->db->prepare("UPDATE users SET reset_token = NULL, reset_expires_at = NULL WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    private function ensureResetTokenColumns(): void {
        static $checked = false;
        if ($checked) return;
        $checked = true;
        try {
            $cols = $this->db->query("SHOW COLUMNS FROM users LIKE 'reset_token'")->fetchAll();
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE users ADD COLUMN reset_token VARCHAR(64) NULL, ADD COLUMN reset_expires_at DATETIME NULL");
            }
        } catch (Throwable $e) {}
    }

    public function getAll(int $limit = 50): array {
        $stmt = $this->db->prepare("SELECT id, fullname, email, phone, role, address, status, created_at 
                                    FROM users ORDER BY id DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countCustomers(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'customer'");
        return (int)$stmt->fetchColumn();
    }
}
