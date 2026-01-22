<?php
class database
{
    private static $servername = "localhost";
    private static $username = "root";
    private static $password = "";
    private static $dbname = "sql_injection";

    public static function dbConnection()
    {
        // Create connection
        $conn = new mysqli(self::$servername, self::$username, self::$password, self::$dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        // Wichtig: Charset setzen
        $conn->set_charset("utf8mb4");
        return $conn;
    }
}
