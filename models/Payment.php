<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

final class Payment extends BaseModel
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    public function __construct()
    {
        parent::__construct();
    }

    public static function find(int $id): ?array
    {
        $instance = new self();
        return $instance->getById($id);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function createPayment(int $orderId, string $orderCode, int $amount, string $gateway = 'sepay'): string
    {
        do {
            $txnRef = date('ymdHis') . random_int(1000, 9999);
            $stmt = $this->db->prepare("SELECT id FROM payments WHERE txn_ref = ?");
            $stmt->execute([$txnRef]);
        } while ($stmt->fetch());

        $sql = "INSERT INTO payments (order_id, order_code, gateway, txn_ref, amount, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, 'pending', NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId, $orderCode, $gateway, $txnRef, $amount]);

        return $txnRef;
    }

    public function findByTxnRef(string $txnRef): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE txn_ref = ? LIMIT 1");
        $stmt->execute([$txnRef]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByOrderCode(string $orderCode): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE order_code = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$orderCode]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByOrderId(int $orderId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE order_id = ? ORDER BY id DESC");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Ghi nhận thanh toán thành công (Idempotent: chỉ update khi status đang là pending).
     */
    public function markSuccess(int $paymentId, ?string $transactionNo, ?string $bankCode, string $responseCode, array $rawData): bool
    {
        $sql = "UPDATE payments 
                SET status = 'success', 
                    transaction_no = ?, 
                    bank_code = ?, 
                    response_code = ?, 
                    raw_data = ?, 
                    paid_at = NOW(), 
                    updated_at = NOW() 
                WHERE id = ? AND status = 'pending'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $transactionNo,
            $bankCode,
            $responseCode,
            json_encode($rawData, JSON_UNESCAPED_UNICODE),
            $paymentId,
        ]);

        return $stmt->rowCount() > 0;
    }
}
