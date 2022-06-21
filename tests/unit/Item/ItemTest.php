<?php

namespace unit\Item;

use App\CartItem\Database;
use App\CartItem\DBInterface;
use App\Item\Item;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{

    private DBInterface $db;
    private Item $item;

    protected function setUp(): void
    {
        $this->db = $this->getMockBuilder(Database::class)->disableOriginalConstructor()->getMock();
        $this->item = new Item($this->db);
    }

    /**
     * This function test the getItems function
     * @test
     * @param $item
     * @param $expectedResult
     * @return void
     * @dataProvider getItemsDataProvider()
     */
    public function getItems($item, $expectedResult): void
    {
        $this->db->method('query')
            ->willReturn($item);
        $result = $this->item->getItems();
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * This function provide the data to test getItems method.
     * @return array
     */
    public function getItemsDataProvider(): array
    {
        $items = array(
            array(
                'id' => 1,
                'item' => 'A',
                'price' => 50,
            ),
            array(
                'id' => 2,
                'item' => 'B',
                'price' => 30,
            ),
            array(
                'id' => 3,
                'item' => 'C',
                'price' => 20,
            ),
        );
        return [
          ['items' => $items, 'expected_result' => $items],
          ['items' => [], 'expected_result' => []]
        ];
    }
}