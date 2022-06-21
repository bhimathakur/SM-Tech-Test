<?php

namespace App\CartItem;

/**
 * This class calculate the item price with special offer and without special offer
 *
 */
class CalculateItemPrice
{
    private DBInterface $db;
    private SpecialOffer $specialOffer;
    private array $items;

    /**
     * @param DBInterface $database
     * @param SpecialOffer $specialOffer
     */
    public function __construct(DBInterface $database, SpecialOffer $specialOffer)
    {
        $this->db = $database;
        $this->specialOffer = $specialOffer;
    }

    /**
     * This function set the selected items
     * @param array $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    /**
     * This function calculate item price with special offer and without offer
     * @param int $itemId
     * @param int $qty
     * @return false|float|int
     */
    public function calculatePrice(int $itemId, int $qty)
    {
        $itemDetails = $this->getDetails($itemId);
        $priceWithOffer = $this->getPriceWithOffer($itemDetails['id'], $qty);
        return $priceWithOffer ?? ($itemDetails['price'] * $qty);
    }

    /**
     * This function calculate the item price and return price if special offer is applicable on item.
     * otherwise it return null
     * @param int $itemId
     * @param int $qty
     * @return float|int|mixed|null
     */
    private function getPriceWithOffer(int $itemId, int $qty)
    {
        $offerOnItem = $this->specialOffer->getOfferOnItem($itemId);
        if (empty($offerOnItem)) {
            return null;
        }
        return $this->calculateItemPriceWithOffer($offerOnItem, $qty);
    }

    /**
     * This function calculate item price with offer and return the price.
     * @param $offerOnItem
     * @param $qty
     * @return float|int
     */
    private function calculateItemPriceWithOffer($offerOnItem, $qty)
    {
        $price = 0;
        foreach ($offerOnItem as $offer) {
            if ($offer['offer_with_other_item_price'] == 0 && $qty >= $offer['quantity']) {
                $offerOnItemQty = $qty / $offer['quantity'];
                $qty -= (int)$offerOnItemQty * $offer['quantity'];
                $price += $offer['price'] * ((int)$offerOnItemQty);
            } else {
                $offerOnOtherItemQty = $this->getItemOfferOnOtherItem($offer['offer_with_other_item']);
                if ($offerOnOtherItemQty > 0) {
                    if ($qty > $offerOnOtherItemQty) {
                        $offerOnItemQty = $qty / $offerOnOtherItemQty;
                        $price += ((int)$offerOnItemQty * $offerOnOtherItemQty) * $offer['offer_with_other_item_price'];
                        $qty -= (int)$offerOnItemQty * $offerOnOtherItemQty;
                    } else {
                        $price += $qty * $offer['offer_with_other_item_price'];
                        $qty -= (int)$qty * $offerOnOtherItemQty;
                    }
                }
            }
        }
        if ($qty > 0) {
            $price += $qty * $offerOnItem[0]['itemPrice'];
        }
        return $price;
    }

    /**
     * This function checks is given id exist in selected item's array and then return that item qty
     * @param int $itemId
     * @return int
     */
    private function getItemOfferOnOtherItem(int $itemId): int
    {
        $qty = 0;
        foreach ($this->items as $item) {
            if ($itemId == $item['id']) {
                $qty = $item['qty'];
            }
        }
        return $qty;
    }


    /**
     * This function return the item details
     * @param int $itemId
     * @return mixed
     */
    public function getDetails(int $itemId)
    {
        $parameters = ['id' => $itemId];
        return $this->db->query("select id, item, price from sku where id = :id", $parameters, 'single');
    }
}
