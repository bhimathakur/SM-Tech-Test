<?php

namespace unit\CartItem;

use App\CartItem\Database;
use PHPUnit\Framework\TestCase;
use PDO;

/**
 * This class test the db connection
 */
class DatabaseTest extends TestCase
{


    /**
     * This function test the db connection
     * @test
     * @return void
     */
    public function connection(): void
    {
        $servername = 'localhost';
        $username = 'root';
        $password = '';

        $db = new Database();
        $result = $db->connect();
        $expected = new PDO("mysql:host=$servername;dbname=test", $username, $password);
        $this->assertEquals($expected, $result);
    }

    /**
     * This function test the query function fetch data form db
     * @test
     * @return void
     */
    public function query(): void
    {
        $db = new Database();
        $result = $db->query('select id from sku limit 3');
        $this->assertEquals(3, count($result));
    }
}
