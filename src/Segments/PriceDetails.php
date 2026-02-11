<?php

namespace Media24si\eSlog2Reader\Segments;

use SimpleXMLElement;

class PriceDetails
{
    public const CALCULATION_NET = "AAA";
    public const CALCULATION_GROSS = "AAB";

    public function getPriceDetails(SimpleXMLElement $pri): array
    {
        $type = match ((string) $pri->C_C509->D_5125) {
            self::CALCULATION_NET => "net_calculation_price",
            self::CALCULATION_GROSS => "gross_calculation_price"
        };
        $data = [
            "price_amount" => (float) $pri->C_C509->D_5118,
            "unit_price_basis" => (float) $pri->C_C509->D_5284, #The number of item units to which the price applies to.
            "measurement_unit_code" => (string) $pri->C_C509->D_6411,
        ];
        return [$type => $data];
    }
}
