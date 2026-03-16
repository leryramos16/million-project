<?php

class AdminController {

use Controller;

    public function index()
    {
        Auth::requireLogin();
        Auth::requireAdmin();
        
        $this->view('admin/dashboard');
    }

    public function viewPendingRequests()
    {
        Auth::requireLogin();
        Auth::requireAdmin();


        $questModel = new Quests;
        $questModel->getPendingRequests();
        $data['quests'] = $questModel->getPendingRequests();

        $this->view('admin/pendingquest', $data);
    }
    
}