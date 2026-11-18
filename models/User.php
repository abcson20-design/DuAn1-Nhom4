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

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT id, fullname, email, phone, role, address, created_at FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
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

    public function getAll(int $limit = 50): array {
        $stmt = $this->db->prepare("SELECT id, fullname, email, phone, role, address, created_at 
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
