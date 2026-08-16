<?php

class Database
{
    private $host = "localhost";
    private $port = "3307";
    private $dbname = "millionproject";
    private $username = "root";
    private $password = "";

    public function connect()
    {
        try {
            $conn = new PDO(
                "mysql:host=$this->host;port=$this->port;dbname=$this->dbname;charset=utf8",
                $this->username,
                $this->password
            );
            return $conn;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}