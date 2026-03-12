<?php

    if($_SERVER['SERVER_NAME'] == 'localhost')
        {
            /**database config */
           define('DBNAME', 'millionproject');
           define('DBHOST', 'localhost'); 
           define('DBUSER', 'root'); 
           define('DBPASS', ''); 

           define('SITE_URL', 'http://localhost/mymillionpesoproject/public');



           define('ROOT', 'http://localhost/mymillionpesoproject/public');
        } else {
            /**database config when deploying */
           define('DBNAME', 'millionproject');
           define('DBHOST', 'localhost'); 
           define('DBUSER', 'root'); 
           define('DBPASS', ''); 
            
           define('ROOT', 'https://www.yourwebsite.com');
        }


    define('APP_NAME', "My Million Peso Project");
    define('APP_DESC', "Getting Rich Project");

    define('DEBUG', true);

    /**  create PDO connection */

    try {
        $conn = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Database Connection failed: " . $e->getMessage());
    }