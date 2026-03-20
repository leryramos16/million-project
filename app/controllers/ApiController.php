<?php

class ApiController
{
    use Controller;

    private function jsonResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    private function jsonError($message, $status = 400)
    {
        $this->jsonResponse(['error' => $message], $status);
    }

    private function getJsonBody()
    {
        $body = file_get_contents('php://input');
        if (empty($body)) {
            return [];
        }

        $data = json_decode($body, true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            $this->jsonError('Invalid JSON payload.', 400);
        }

        return $data;
    }

    public function quests($filter = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->jsonError('Method Not Allowed', 405);
        }

        $questModel = new Quests();

        if ($filter === 'pending') {
            Auth::requireAdmin();
            return $this->jsonResponse(['quests' => $questModel->getPendingRequests()]);
        }

        if ($filter === 'all') {
            Auth::requireAdmin();
            return $this->jsonResponse(['quests' => array_merge($questModel->getAllQuests(), $questModel->getPendingRequests())]);
        }

        if ($filter !== null && is_numeric($filter)) {
            $quest = $questModel->findQuestById((int) $filter);
            if (!$quest) {
                return $this->jsonError('Quest not found.', 404);
            }

            return $this->jsonResponse(['quest' => $quest]);
        }

        return $this->jsonResponse(['quests' => $questModel->getAllQuests()]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonError('Method Not Allowed', 405);
        }

        Auth::requireLogin();

        $data = $this->getJsonBody();

        if (empty($data['title']) || empty($data['description'])) {
            return $this->jsonError('title and description are required.', 422);
        }

        $questModel = new Quests();

        $createData = [
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'payment_proof' => $data['payment_proof'] ?? null,
            'xp_reward' => $data['xp_reward'] ?? 0,
            'coins_reward' => $data['coins_reward'] ?? 0,
            'type' => $data['type'] ?? 'side_quests',
            'status' => 'pending'
        ];

        $newId = $questModel->create($createData);
        if (!$newId) {
            return $this->jsonError('Failed to create quest.', 500);
        }

        return $this->jsonResponse(['message' => 'Quest created.', 'id' => $newId], 201);
    }

    public function update($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'PATCH') {
            return $this->jsonError('Method Not Allowed', 405);
        }

        Auth::requireAdmin();

        if ($id === null || !is_numeric($id)) {
            return $this->jsonError('Quest ID required.', 400);
        }

        $data = $this->getJsonBody();

        if (empty($data['title']) || empty($data['description'])) {
            return $this->jsonError('title and description are required.', 422);
        }

        $questModel = new Quests();
        $quest = $questModel->findQuestById((int) $id);

        if (!$quest) {
            return $this->jsonError('Quest not found.', 404);
        }

        $updateData = [
            'id' => (int) $id,
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'xp_reward' => $data['xp_reward'] ?? $quest['xp_reward'],
            'coins_reward' => $data['coins_reward'] ?? $quest['coins_reward'],
            'type' => $data['type'] ?? $quest['type']
        ];

        $success = $questModel->updateQuest($updateData);
        if (!$success) {
            return $this->jsonError('Failed to update quest.', 500);
        }

        return $this->jsonResponse(['message' => 'Quest updated.']);
    }

    public function publish($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonError('Method Not Allowed', 405);
        }

        Auth::requireAdmin();

        if ($id === null || !is_numeric($id)) {
            return $this->jsonError('Quest ID required.', 400);
        }

        $questModel = new Quests();
        $quest = $questModel->findQuestById((int) $id);

        if (!$quest) {
            return $this->jsonError('Quest not found.', 404);
        }

        $success = $questModel->publishQuest((int) $id);
        if (!$success) {
            return $this->jsonError('Failed to publish quest.', 500);
        }

        return $this->jsonResponse(['message' => 'Quest published.']);
    }
}
