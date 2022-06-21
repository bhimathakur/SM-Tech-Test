<?php

namespace unit\CartItem;

use App\CartItem\CalculateItemPrice;
use App\CartItem\Database;
use App\CartItem\SpecialOffer;
use PHPUnit\Framework\TestCase;

/**
 * This class test item price calculation with special offer and without offer on item
 */
class CalculateItemPriceTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        $this->db = $this->getMockBuilder(Database::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * This function test the item price calculation with special offer and without offer on item
     * @test
     * @dataProvider calculateItemPriceDataProvider
     */
    public function calculateItemPrice(array $items, array $itemsData): void
    {
        $this->execute($items, $itemsData);
    }

    /**
     * This function test the item price calculation with special offer on other item.
     * Suppose user buy 10 ‘D’s and 6 ‘A’s, 6 of the ‘D’s will cost 5 each items
     * other 4 will cost 15(D item unit price) each.
     * @test
     * @dataProvider calculateItemPriceWithOfferOnOtherItemDataProvider
     */
    public function calculateItemPriceWithOfferOnOtherItem(array $items, array $itemsData): void
    {
        $this->execute($items, $itemsData);
    }

    /**
     * This function set the mock the dependent classes and set data for methods.
     */
    private function mockAndSetMethodData(array $itemDetail, array $items)
    {
        $specialOffers = $this->getMockBuilder(SpecialOffer::class)->disableOriginalConstructor()->getMock();
        $item = $this->getMockBuilder(CalculateItemPrice::class)->onlyMethods(['getDetails'])
            ->setConstructorArgs([$this->db, $specialOffers])->getMock();
        $item->setItems($items);
        $item->method('getDetails')->willReturn($itemDetail['item_details']);
        $specialOffers->method('getOfferOnItem')->willReturn($itemDetail['offer']);
        return $item;
    }

    /**
     * This function execute the test assertion
     */
    private function execute(array $items, array $itemsData): void
    {
        $totalExpectedPrice = 0;
        $grandTotal = 0;
        foreach ($itemsData as $itemDetail) {
            $item = $this->mockAndSetMethodData($itemDetail, $items);
            $price = $item->calculatePrice($itemDetail['id'], $itemDetail['qty']);
            $this->assertEquals($price, $itemDetail['expected_item_price']);
            $grandTotal += $price;
            $totalExpectedPrice += $itemDetail['expected_item_price'];
        }
        $this->assertEquals($totalExpectedPrice, $grandTotal);
    }

    /**
     * This function test the get item details function
     * @test
     * @dataProvider itemDetailsDataProvider
     */
    public function getItemDetails($itemDetails, $expectedResult): void
    {
        $specialOffers = $this->getMockBuilder(SpecialOffer::class)->disableOriginalConstructor()->getMock();
        $item = new CalculateItemPrice($this->db, $specialOffers);
        $this->db->method("query")->willReturn($itemDetails);
        $result = $item->getDetails(1);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * This function provide the data for test different possible cases for item price calculation
     * like special offer applicable on item and without special offer on item.
     * @return array[]
     */
    public function calculateItemPriceDataProvider(): array
    {
        $items = [
            ['id' => 2, 'qty' => 6],
            ['id' => 3, 'qty' => 10],
            ['id' => 5, 'qty' => 15],
        ];
        $itemsWithOfferData = [
            ['id' => 2, 'qty' => 6,
                'item_details' => ['id' => 2, 'item' => 'B', 'price' => 30],
                'offer' => [
                    ['quantity' => 2, 'price' => 45, 'offer_with_other_item' => 0,
                        'offer_with_other_item_price' => 0, 'itemPrice' => 30
                    ]
                ],
                'expected_item_price' => 135,
            ],
            ['id' => 3, 'qty' => 10,
                'item_details' => ['id' => 3, 'item' => 'C', 'price' => 20],
                'offer' => [
                    [
                        'quantity' => 3, 'price' => 50, 'offer_with_other_item' => 0,
                        'offer_with_other_item_price' => 0, 'itemPrice' => 20
                    ],
                    [
                        'quantity' => 2, 'price' => 38, 'offer_with_other_item' => 0,
                        'offer_with_other_item_price' => 0, 'itemPrice' => 20
                    ]
                ],
                'expected_item_price' => 170,
            ],
            ['id' => 5, 'qty' => 15,
                'item_details' => ['id' => 5, 'item' => 'E', 'price' => 5],
                'offer' => [],
                'expected_item_price' => 75,
            ],

        ];

        $itemsData = [
            ['id' => 2, 'qty' => 6,
                'item_details' => ['id' => 2, 'item' => 'B', 'price' => 30],
                'expected_item_price' => 180,
                'offer' => [],
            ],
            ['id' => 3, 'qty' => 10,
                'item_details' => ['id' => 3, 'item' => 'C', 'price' => 20],
                'expected_item_price' => 200,
                'offer' => [],
            ],
            ['id' => 5, 'qty' => 15,
                'item_details' => ['id' => 5, 'item' => 'E', 'price' => 5],
                'expected_item_price' => 75,
                'offer' => [],
            ]
        ];
        return [
            ['items' => $items, 'items_with_offer_data' => $itemsWithOfferData],
            ['items' => $items, 'items_with_offer_data' => $itemsData]
        ];
    }

    /**
     * This function provide the data for test different possible cases for item price calculation
     * like special offer applicable on item and without special offer on item.
     * @return array[]
     */
    public function calculateItemPriceWithOfferOnOtherItemDataProvider(): array
    {
        $items = [
            ['id' => 1, 'qty' => 6],
            ['id' => 4, 'qty' => 10],
        ];
        $itemsWithOfferData = [
            ['id' => 1, 'qty' => 6,
                'item_details' => ['id' => 1, 'item' => 'A', 'price' => 50],
                'offer' => [
                    [
                        'quantity' => 3, 'price' => 130, 'offer_with_other_item' => 0,
                        'offer_with_other_item_price' => 0, 'itemPrice' => 50
                    ]
                ],
                'expected_item_price' => 260,
            ],
            ['id' => 4, 'qty' => 10,
                'item_details' => ['id' => 4, 'item' => 'D', 'price' => 15],
                'offer' => [
                    [
                        'quantity' => 2, 'price' => 0, 'offer_with_other_item' => 1,
                        'offer_with_other_item_price' => 5, 'itemPrice' => 15
                    ]
                ],
                'expected_item_price' => 90,
            ]
        ];

        return [
            ['items' => $items, 'items_with_offer_data' => $itemsWithOfferData],
        ];
    }

    /**
     * This function provide the data for test get item methods with possible cases.
     * @return array
     */
    public function itemDetailsDataProvider(): array
    {
        $itemDetails = ['id' => 3, 'item' => 'C', 'price' => 20];
        return [
            ['item' => $itemDetails, 'expected_result' => $itemDetails],
            ['item' => null, 'expected_result' => false],
        ];
    }
}
