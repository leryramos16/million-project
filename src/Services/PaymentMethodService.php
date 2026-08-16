<?php

namespace Leryr\Mymillionpesoproject\Services;

use Leryr\Mymillionpesoproject\Repositories\PaymentMethodRepository;

class PaymentMethodService
{
    private const UPLOAD_DIR = __DIR__ . '/../../public/uploads/qrcodes/';
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/jpg'];

    public function __construct(private PaymentMethodRepository $repository)
    {
    }

    public function listActive(): array
    {
        return $this->repository->allActive();
    }

    public function listAll(): array
    {
        return $this->repository->all();
    }

    public function find(int $id): ?array
    {
        return $this->repository->findById($id);
    }

    public function create(array $data, ?array $file): array
    {
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $qrFilename = null;

        if ($file) {
            $result = $this->storeQrImage($file);

            if (!$result['success']) {
                return $result;
            }

            $qrFilename = $result['filename'];
        }

        $id = $this->repository->create([
            'method' => $data['method'],
            'label' => trim($data['label']),
            'account_name' => trim($data['account_name']),
            'account_number' => trim($data['account_number']),
            'qr_code_image' => $qrFilename,
            'instructions' => trim($data['instructions'] ?? ''),
            'is_active' => isset($data['is_active']) ? 1 : 0,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        return ['success' => true, 'id' => $id];
    }

    public function update(int $id, array $data, ?array $file): array
    {
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $payload = [
            'method' => $data['method'],
            'label' => trim($data['label']),
            'account_name' => trim($data['account_name']),
            'account_number' => trim($data['account_number']),
            'instructions' => trim($data['instructions'] ?? ''),
            'is_active' => isset($data['is_active']) ? 1 : 0,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];

        if ($file) {
            $result = $this->storeQrImage($file);

            if (!$result['success']) {
                return $result;
            }

            $payload['qr_code_image'] = $result['filename'];
        }

        $this->repository->update($id, $payload);

        return ['success' => true];
    }

    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (empty($data['label'])) {
            $errors['label'] = 'Label is required';
        }
        if (empty($data['account_name'])) {
            $errors['account_name'] = 'Account name is required';
        }
        if (empty($data['account_number'])) {
            $errors['account_number'] = 'Account number is required';
        }

        return $errors;
    }

    private function storeQrImage(array $file): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Failed to upload QR code image'];
        }

        if (!in_array($file['type'], self::ALLOWED_TYPES, true)) {
            return ['success' => false, 'message' => 'Only JPG and PNG files are allowed'];
        }

        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0777, true);
        }

        $filename = time() . '_' . basename($file['name']);

        if (!move_uploaded_file($file['tmp_name'], self::UPLOAD_DIR . $filename)) {
            return ['success' => false, 'message' => 'Failed to save QR code image'];
        }

        return ['success' => true, 'filename' => $filename];
    }
}
