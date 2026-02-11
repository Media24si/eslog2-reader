<?php

namespace Media24si\eSlog2Reader\Segments;

use SimpleXMLElement;

class Currency
{
    public const REFERENCE_CURRENCY = 2;
    public const INFORMATION_CURRENCY = 6;

    public function getCurrency(SimpleXMLElement $cux): array
    {
        return match ((int) $cux->C_C504->D_6347) {
            self::INFORMATION_CURRENCY => ["information_currency" => (string) $cux->C_C504->D_6345],
            self::REFERENCE_CURRENCY => ["reference_currency" => (string)$cux->C_C504->D_6345],
        };
    }
}
