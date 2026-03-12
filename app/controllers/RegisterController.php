<?php 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class RegisterController
{
    use Controller;
    public function index()
    {
         $data = [
        'username' => '',
        'email' => '',
        'password' => '',
        'password2' => '',
        'emailErr' => '',
        'usernameErr' => '',
        'passwordErr' => '',
        'password2Err' => '',
        'agree' => '',
        'agreeErr' => '',
        'success' => '',
    ];

         // Step 1: Check if success/error messages exist in session (after redirect)
        if (isset($_SESSION['success'])) {
            $data['success'] = $_SESSION['success'];
            unset($_SESSION['success']);
        }

         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $password2 = trim($_POST['password2']);
            //remember what user type
            $data['username'] = $username;
            $data['email'] = $email;
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $userModel = new User;
            $userModel = $this->model('User');
            $usernameExist = $userModel->findByUsername($username);
            $emailExists = $userModel->findByEmail($email);
            

        if (!preg_match("/^[a-zA-Z0-9_-]+$/", $username)) {
            $data['usernameErr'] = 'Username must contain only letters, numbers, underscores (_) or dashes (-).';
        } elseif (strlen($username) < 5) {
            $data['usernameErr'] = 'Username must be at least 5 characters long.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $data['emailErr'] = 'Invalid email format.';
        }
       
        if($usernameExist)
        {
            $data['usernameErr'] = '*Username already used';
        }

        if($emailExists)
        {
            $data['emailErr'] = 'Email already used!';
        }   
         if(strlen($password) < 8) {
            $data['passwordErr'] = '*Password must be at least 8 characters long';
        } 
        if ($password !== $password2) {
            $data['password2Err'] = '*Password dont match';
        
        }

         if(!isset($_POST['agree'])) {
            $data['agreeErr'] = '*You must agree the terms';
        } 

            if(empty($data['usernameErr']) &&
            empty($data['emailErr']) &&
            empty($data['passwordErr']) &&
            empty($data['password2Err']) &&
            empty($data['agreeErr'])
            )
        {
            
            $userModel->register($username, $email, $hashed);
                session_regenerate_id(true);
               //Store success message in session
                $_SESSION['success'] = 'Successfully registered!';

                //Redirect to avoid resubmission
                header("Location: " . ROOT . "/register");
                exit;
        }         
         else {

                
                // Store the errors temporarily in session
                unset($data['password'], $data['password2']);
                $_SESSION['form_data'] = $data;
                header("Location: " . ROOT . "/register");
                exit;
            }
        }

            // If errors were stored in session
            if (isset($_SESSION['form_data'])) {
                $data = array_merge($data, $_SESSION['form_data']);
                unset($_SESSION['form_data']);
            }

         $this->view('register', $data);
    }
}