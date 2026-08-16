<?php

namespace Leryr\Mymillionpesoproject\Repositories;

use PDO;

class PaymentMethodRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM payment_methods ORDER BY sort_order ASC, id ASC')
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allActive(): array
    {
        return $this->db->query(
            "SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY sort_order ASC, id ASC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM payment_methods WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO payment_methods
                (method, label, account_name, account_number, qr_code_image, instructions, is_active, sort_order)
             VALUES
                (:method, :label, :account_name, :account_number, :qr_code_image, :instructions, :is_active, :sort_order)'
        );

        $stmt->execute([
            'method' => $data['method'],
            'label' => $data['label'],
            'account_name' => $data['account_name'],
            'account_number' => $data['account_number'],
            'qr_code_image' => $data['qr_code_image'] ?? null,
            'instructions' => $data['instructions'] ?? null,
            'is_active' => $data['is_active'] ?? 1,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $fields = ['method', 'label', 'account_name', 'account_number', 'instructions', 'is_active', 'sort_order'];

        if (!empty($data['qr_code_image'])) {
            $fields[] = 'qr_code_image';
        }

        $set = implode(', ', array_map(fn($f) => "$f = :$f", $fields));

        $stmt = $this->db->prepare("UPDATE payment_methods SET $set WHERE id = :id");
        $stmt->execute([...array_intersect_key($data, array_flip($fields)), 'id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM payment_methods WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
