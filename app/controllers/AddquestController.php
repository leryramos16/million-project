<?php

class AddquestController
{
    use Controller;


    public function index()
    {
        Auth::requireLogin();

        $this->view('addquest');
    }
}