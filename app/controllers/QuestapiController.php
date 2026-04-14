<?php

class QuestapiController
{
    use Controller;

    public function index()
    {
        header('Content-Type: application/json');

        $questModel = $this->model('Quests');
        $quests = $questModel->findAll();

        echo json_encode($quests);
    }
}