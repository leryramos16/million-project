<?php

use Leryr\Mymillionpesoproject\Repositories\AchievementRepository;
use Leryr\Mymillionpesoproject\Repositories\PaymentMethodRepository;
use Leryr\Mymillionpesoproject\Repositories\QuestRepository;
use Leryr\Mymillionpesoproject\Repositories\UserRepository;
use Leryr\Mymillionpesoproject\Services\AchievementService;
use Leryr\Mymillionpesoproject\Services\PaymentMethodService;
use Leryr\Mymillionpesoproject\Services\QuestService;

class AdminController {

use Controller;

    private QuestService $questService;
    private UserRepository $userRepository;
    private PaymentMethodService $paymentMethodService;

    public function __construct()
    {
        $db = (new Database())->connect();
        $userRepository = new UserRepository($db);
        $achievementService = new AchievementService(new AchievementRepository($db), $userRepository);

        $this->questService = new QuestService(new QuestRepository($db), $userRepository, $achievementService);
        $this->userRepository = $userRepository;
        $this->paymentMethodService = new PaymentMethodService(new PaymentMethodRepository($db));
    }

    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        Auth::requireLogin();
        Auth::requireAdmin();

        $data['stats'] = $this->questService->adminStats();

        $this->view('admin/dashboard', $data);
    }

    public function viewPendingRequests()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        Auth::requireLogin();
        Auth::requireAdmin();

        $result = $this->questService->adminPending(1, 50);
        $data['quests'] = $result['data'];

        $this->view('admin/pendingquest', $data);
    }

    public function editQuest($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        Auth::requireLogin();
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $this->questService->adminUpdate((int) $id, $_POST);

            header("Location: " . ROOT . "/admin/viewPendingRequests");
            exit;
        }

        $data['quest'] = $this->questService->find((int) $id);
        $this->view('admin/editquest', $data);
    }

    public function publishQuest($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        Auth::requireLogin();
        Auth::requireAdmin();

        $this->questService->adminPublish((int) $id);
        $_SESSION['success'] = "Quest published successfully!";
        header("Location: " . ROOT . "/admin/viewPendingRequests");
    }

    public function users()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        Auth::requireLogin();
        Auth::requireAdmin();

        $data['users'] = $this->userRepository->all();
        $this->view('admin/users', $data);
    }

    public function paymentMethods()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        Auth::requireLogin();
        Auth::requireAdmin();

        $data['methods'] = $this->paymentMethodService->listAll();
        $this->view('admin/paymentmethods', $data);
    }

    public function createPaymentMethod()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        Auth::requireLogin();
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $file = (!empty($_FILES['qr_code']) && $_FILES['qr_code']['error'] === UPLOAD_ERR_OK) ? $_FILES['qr_code'] : null;
            $result = $this->paymentMethodService->create($_POST, $file);

            if ($result['success']) {
                $_SESSION['success'] = 'Payment method added!';
                header("Location: " . ROOT . "/admin/paymentMethods");
                exit;
            }

            $data['errors'] = $result['errors'] ?? [$result['message'] ?? 'Failed to save'];
            $data['old'] = $_POST;
        }

        $this->view('admin/paymentmethod_form', $data ?? []);
    }

    public function editPaymentMethod($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        Auth::requireLogin();
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $file = (!empty($_FILES['qr_code']) && $_FILES['qr_code']['error'] === UPLOAD_ERR_OK) ? $_FILES['qr_code'] : null;
            $result = $this->paymentMethodService->update((int) $id, $_POST, $file);

            if ($result['success']) {
                $_SESSION['success'] = 'Payment method updated!';
                header("Location: " . ROOT . "/admin/paymentMethods");
                exit;
            }

            $data['errors'] = $result['errors'] ?? [$result['message'] ?? 'Failed to save'];
        }

        $data['method'] = $this->paymentMethodService->find((int) $id);
        $this->view('admin/paymentmethod_form', $data);
    }

    public function deletePaymentMethod($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        Auth::requireLogin();
        Auth::requireAdmin();

        $this->paymentMethodService->delete((int) $id);
        $_SESSION['success'] = 'Payment method removed.';
        header("Location: " . ROOT . "/admin/paymentMethods");
    }

}
