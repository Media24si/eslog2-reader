<?php

namespace Media24si\eSlog2Reader\Segments;

use SimpleXMLElement;

class AdditionalProductID
{
    public const ITEM_TYPE_SUPPLIER_ARTICLE_NUMBER = "SA";
    public const ITEM_TYPE_BUYER_ITEM_NUMBER = "IN";
    public const ITEM_TYPE_CLASS_ID = "ZZZ";
    public const PRODUCT_IDENTIFICATION = 5;
    public const ADDITIONAL_IDENTIFICATION = 1;

    public function getAdditionalProductID(SimpleXMLElement $pia): array
    {
        $itemID = (string) $pia->C_C212->D_7140;
        $type = match ((int) $pia->D_4347) {
            self::PRODUCT_IDENTIFICATION => "product_id",
            self::ADDITIONAL_IDENTIFICATION => "additional_id",
            default => ""
        };
        $data = match ((string) $pia->C_C212->D_7143) {
            self::ITEM_TYPE_SUPPLIER_ARTICLE_NUMBER => ["supplier_article_number" => $itemID],
            self::ITEM_TYPE_BUYER_ITEM_NUMBER => ["buyer_item_number" => $itemID],
            self::ITEM_TYPE_CLASS_ID => ["item_class_id" => $itemID],
            default => ""
        };
        if (empty($data)) {
            return [$type => $itemID];
        }
        return [$type => $data];
    }
}
