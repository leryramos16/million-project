<?php

namespace Leryr\Mymillionpesoproject\Services;

use Leryr\Mymillionpesoproject\Repositories\CashoutRequestRepository;
use Leryr\Mymillionpesoproject\Repositories\QuestRepository;
use Leryr\Mymillionpesoproject\Repositories\UserRepository;
use PDO;

class CashoutService
{
    private const MIN_COINS = 50;
    private const COIN_TO_PESO = 1; // 1 coin = ₱1, fixed

    public function __construct(
        private PDO $db,
        private CashoutRequestRepository $cashouts,
        private UserRepository $users,
        private QuestRepository $quests
    ) {
    }

    public function request(int $userId, array $data): array
    {
        $coins = (int) ($data['coins'] ?? 0);
        $method = trim($data['payment_method'] ?? '');
        $accountName = trim($data['account_name'] ?? '');
        $accountNumber = trim($data['account_number'] ?? '');

        if ($coins < self::MIN_COINS) {
            return ['success' => false, 'message' => 'Minimum cashout is ' . self::MIN_COINS . ' coins'];
        }
        if ($method === '' || $accountName === '' || $accountNumber === '') {
            return ['success' => false, 'message' => 'Payment method, account name, and account number are required'];
        }

        $this->db->beginTransaction();

        try {
            $user = $this->users->findById($userId);

            if (!$user || (int) $user['coins'] < $coins) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Not enough coins'];
            }

            $this->users->adjustCoins($userId, -$coins);

            $id = $this->cashouts->create([
                'user_id' => $userId,
                'coins_requested' => $coins,
                'peso_amount' => $coins * self::COIN_TO_PESO,
                'payment_method' => $method,
                'account_name' => $accountName,
                'account_number' => $accountNumber,
            ]);

            $this->db->commit();

            return ['success' => true, 'id' => $id];
        } catch (\Throwable $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Something went wrong, try again'];
        }
    }

    public function history(int $userId): array
    {
        return $this->cashouts->byUser($userId);
    }

    public function pending(): array
    {
        return $this->cashouts->pending();
    }

    public function markPaid(int $id): bool
    {
        $request = $this->cashouts->findById($id);

        if (!$request || $request['status'] !== 'pending') {
            return false;
        }

        $this->cashouts->markPaid($id);
        return true;
    }

    public function reject(int $id, ?string $note): bool
    {
        $request = $this->cashouts->findById($id);

        if (!$request || $request['status'] !== 'pending') {
            return false;
        }

        $this->db->beginTransaction();

        try {
            $this->users->adjustCoins((int) $request['user_id'], (int) $request['coins_requested']);
            $this->cashouts->markRejected($id, $note);
            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /** The reserve dashboard: what you've collected, paid out, still owe, and have actually kept. */
    public function ledger(): array
    {
        $collected = $this->quests->sumAmountPaid();
        $paidOut = $this->cashouts->sumByStatus('paid');
        $pendingPayout = $this->cashouts->sumByStatus('pending');
        $coinsOutstanding = $this->users->sumCoins();

        return [
            'pesos_collected' => $collected,
            'pesos_paid_out' => $paidOut,
            'pesos_pending_payout' => $pendingPayout,
            'coins_outstanding' => $coinsOutstanding,
            'your_margin' => $collected - $paidOut - $coinsOutstanding,
        ];
    }
}
