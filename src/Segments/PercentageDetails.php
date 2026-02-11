<?php

namespace Media24si\eSlog2Reader\Segments;

use SimpleXMLElement;

class PercentageDetails
{
    public const TYPE_ALLOWANCE = 1;
    public const TYPE_CHARGE = 2;

    public function getPercentageDetails(SimpleXMLElement $pcd): array
    {
        return match ((int) $pcd->C_C501->D_5245) {
            self::TYPE_ALLOWANCE => ["allowance_percentage" => (float) $pcd->C_C501->D_5482],
            self::TYPE_CHARGE => ["charge_percentage" => (float) $pcd->C_C501->D_5482],
        };
    }
}
