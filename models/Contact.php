<?php
require_once __DIR__ . '/BaseModel.php';

class Contact extends BaseModel {
    public function __construct() {
        parent::__construct();
    }

    public function create(array $data): bool {
        $sql = "INSERT INTO contacts (user_id, fullname, email, phone, subject, message, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'new')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['user_id'] ?? null,
            $data['fullname'],
            $data['email'],
            $data['phone'] ?? null,
            $data['subject'],
            $data['message']
        ]);
    }

    public function getAll(int $limit = 50): array {
        $stmt = $this->db->prepare("SELECT c.*, u.role as user_role 
                                    FROM contacts c 
                                    LEFT JOIN users u ON c.user_id = u.id 
                                    ORDER BY c.id DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countNew(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM contacts WHERE status = 'new'");
        return (int)$stmt->fetchColumn();
    }

    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare("UPDATE contacts SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM contacts WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
