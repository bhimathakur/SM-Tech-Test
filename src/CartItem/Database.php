<?php

namespace App\CartItem;

use PDO;
use PDOException;

/**
 * This class make the connection with database
 */
class Database implements DBInterface
{
    private PDO $conn;
    private array $config = array(
        'username' => 'root',
        'password' => '',
        'hostname' => 'localhost',
        'database' => 'stock_market5'
    );

    public function __construct()
    {
        $this->connect();
    }

    /**
     * This function make the connection with db
     * @return PDO
     */
    public function connect(): PDO
    {
        $db = $this->config;
        $servername = $db['hostname'];
        $username = $db['username'];
        $password = $db['password'];

        $this->conn = new PDO("mysql:host=$servername;dbname=stock_market", $username, $password);
        return $this->conn;
    }

    /**
     * This is generalized function fetch the data from database
     * @param string $query
     * @param array|null $parameters
     * @param string $fetchRecord
     * @return mixed
     */
    public function query(string $query, ?array $parameters = null, string $fetchRecord = 'all')
    {
        $db  = $this->connect();
        $fetch = $fetchRecord === 'all' ? 'fetchAll':'fetch';
        $stmt = $db->prepare($query);
        $stmt->execute($parameters);
        return $stmt->$fetch(PDO::FETCH_ASSOC);
    }
}
