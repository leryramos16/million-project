<?php 

class User {
    
    private $db;

    public function __construct()
    {
        $this->db =(new Database())->connect();
    }

    public function register($username, $email, $password)
    {
         $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
         $stmt = $this->db->prepare($sql);
         return $stmt->execute([$username, $email, $password]);
    }

    public function findByUsername($username) 
    {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

     public function findByEmail($email) 
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByUsernameOrEmail($usernameOrEmail)
    {
        $sql = "SELECT * FROM users WHERE username = :usernameOrEmail OR email = :usernameOrEmail LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usernameOrEmail' => $usernameOrEmail]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
       
    }

    public function updatePassword($user_id, $hashedPassword)
    {
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt =$this->db->prepare($sql);
        return $stmt->execute([$hashedPassword, $user_id]); 
    }

    public function updateRememberToken($id, $token)
    {
        $sql = "UPDATE users SET remember_token = :token WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['token' => $token, 'id' => $id]);
    }

    public function findByToken($token)
    {
        $sql = "SELECT * FROM users WHERE remember_token = :token LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    //Kunin ang user by id (para sa displaying info)
    public function getUserById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //Update profile image sa database
    public function updateProfileImage($user_id, $filename)
    {
        $sql = "UPDATE users SET profile_image = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$filename, $user_id]);
    }

     public function getAll()
    {
        
        $sql = "SELECT * FROM users";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}