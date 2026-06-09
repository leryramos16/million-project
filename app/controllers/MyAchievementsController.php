<?php

class MyAchievementsController
{
    use Controller;

    public function index()
    {
        $this->view('myachievements');
    }
}