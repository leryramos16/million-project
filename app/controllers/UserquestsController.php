<?php

class UserquestsController
{
    use Controller;

    public function index()
    {
        $questModel = $this->model('Quests');

        $quests = $questModel->getAllQuests();

        $data = [
            'quests' => $quests
        ];

        $this->view('mainpage', $data);
    }
}