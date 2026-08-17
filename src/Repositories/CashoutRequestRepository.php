<?php

namespace Leryr\Mymillionpesoproject\Repositories;

use PDO;

class CashoutRequestRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO cashout_requests
                (user_id, coins_requested, peso_amount, payment_method, account_name, account_number, status)
             VALUES
                (:user_id, :coins_requested, :peso_amount, :payment_method, :account_name, :account_number, \'pending\')'
        );

        $stmt->execute([
            'user_id' => $data['user_id'],
            'coins_requested' => $data['coins_requested'],
            'peso_amount' => $data['peso_amount'],
            'payment_method' => $data['payment_method'],
            'account_name' => $data['account_name'],
            'account_number' => $data['account_number'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM cashout_requests WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function pending(): array
    {
        return $this->db->query(
            "SELECT c.*, u.username
             FROM cashout_requests c
             JOIN users u ON u.id = c.user_id
             WHERE c.status = 'pending'
             ORDER BY c.requested_at ASC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function byUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM cashout_requests WHERE user_id = :user_id ORDER BY requested_at DESC LIMIT 20'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markPaid(int $id): void
    {
        $stmt = $this->db->prepare(
            "UPDATE cashout_requests SET status = 'paid', processed_at = NOW() WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);
    }

    public function markRejected(int $id, ?string $note): void
    {
        $stmt = $this->db->prepare(
            "UPDATE cashout_requests SET status = 'rejected', admin_note = :note, processed_at = NOW() WHERE id = :id"
        );
        $stmt->execute(['id' => $id, 'note' => $note]);
    }

    public function sumByStatus(string $status): int
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(peso_amount), 0) FROM cashout_requests WHERE status = :status'
        );
        $stmt->execute(['status' => $status]);

        return (int) $stmt->fetchColumn();
    }
}
