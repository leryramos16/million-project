<?php

Trait Controller
{
     public function model($model) {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    } 

    public function view($name, $data = [])
    {
        if(!empty($data))
            extract($data);
        
        $filename = "../app/views/".$name.".view.php";
    if(file_exists($filename)) 
    {
        require $filename;
    }else{
         $filename = "../app/views/404.view.php";
         require $filename;
    }
    }
}