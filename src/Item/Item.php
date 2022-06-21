<?php

namespace App\Item;

use App\CartItem\DBInterface;

/**
 * This class is used to get the item data
 */
class Item
{
    private DBInterface $db;
    public function __construct(DBInterface $db)
    {
        $this->db = $db;
    }

    /**
     * This function return all the items
     * @return mixed
     */
    public function getItems()
    {
        return $this->db->query("select id, item, price from sku");
    }
}
