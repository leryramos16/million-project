<?php

class AdminController {

use Controller;

    public function index()
    {
        Auth::requireLogin();
        Auth::requireAdmin();

        $this->view('admin/dashboard');
    }
}