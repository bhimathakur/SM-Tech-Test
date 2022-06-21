<?php

namespace unit\CartItem;

use App\CartItem\Database;
use App\CartItem\DBInterface;
use App\CartItem\SpecialOffer;
use PHPUnit\Framework\TestCase;

class SpecialOfferTest extends TestCase
{
    private DBInterface $db;
    private SpecialOffer $specialOffer;

    protected function setUp(): void
    {
        $this->db = $this->getMockBuilder(Database::class)->disableOriginalConstructor()->getMock();
        $this->specialOffer = new SpecialOffer($this->db);
    }

    /**
     * This function test the is special offer on the item or not
     * @test
     * @dataProvider offerOnItemDataProvider
     */
    public function getOfferOnItem(array $offerOnItem, array $expectedResult): void
    {
        $this->db->method('query')
            ->willReturn($offerOnItem);
        $result = $this->specialOffer->getOfferOnItem(3);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * This function provide the data to test the offer is applicable on the item or not.
     * @return array
     */
    public function offerOnItemDataProvider(): array
    {
        $offerOnItem = array(
            array(
                'quantity' => 3,
                'price' => 50,
                'offer_with_other_item' => 0,
                'offer_with_other_item_price' => 0,
                'item_price' => 20,
            ),
            array(
                'quantity' => 2,
                'price' => 38,
                'offer_with_other_item' => 0,
                'offer_with_other_item_price' => 0,
                'item_price' => 20,
            )
        );
        return [
            ['offer' => $offerOnItem, 'expected_result' => $offerOnItem],
            ['offer' => [], 'expected_result' => []],
        ];
    }
}
