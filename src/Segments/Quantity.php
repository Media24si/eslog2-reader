<?php

namespace Media24si\eSlog2Reader\Segments;

use SimpleXMLElement;

class Quantity
{
    public const TYPE_INVOICED_QUANTITY_CODE = 47;

    public function getQuantity(SimpleXMLElement $qty): array
    {

        $quantityTypeCode = match ((int) $qty->C_C186->D_6063) {
            self::TYPE_INVOICED_QUANTITY_CODE => "invoiced_quantity",
            default => null
        };
        return [
            "quantity_type_code_qualifier" => $quantityTypeCode,
            "quantity" => (string) $qty->C_C186->D_6060,
            "measurement_unit_code" => (string) $qty->C_C186->D_6411
        ];
    }
}
