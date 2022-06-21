<?php

namespace App\CartItem;

/**
 * This class checks the offer on the item
 */
class SpecialOffer
{
    private DBInterface $db;

    /**
     * @param DBInterface $database
     */
    public function __construct(DBInterface $database)
    {
        $this->db = $database;
    }

   /**
     * This function return the special offer on the item.
     * @param int $itemId
     * @return mixed
     */
    public function getOfferOnItem(int $itemId)
    {
        $parameters = ['itemId' => $itemId];
        return $this->db->query(
            "select soi.quantity, soi.price, soi.offer_with_other_item, soi.offer_with_other_item_price, sku.price as itemPrice  
                    from special_offer_on_item as soi
                    inner join sku on sku.id = soi.item_id
                    where item_id = :itemId 
                    order by quantity desc ", $parameters
        );
    }
}
