<?php

namespace Media24si\eSlog2Reader\Segments;

use SimpleXMLElement;

class Allowance
{
    public const TYPE_ALLOWANCE = "A";
    public const TYPE_CHARGE = "C";

    public function getAllowanceData(SimpleXMLElement $alc): array
    {
        if ((string) $alc->D_5463 === self::TYPE_ALLOWANCE) {
            $data = [
                "allowance_id" => (string) $alc->C_C552->D_1230,
                "allowance_id_code" => (string) $alc->C_C552->D_5189,
            ];
            return $data;
        } elseif ((string) $alc->D_5463 === self::TYPE_CHARGE) {
            $data = [
                "charge_id" => (string) $alc->C_C552->D_1230,
                "charge_id_code" => (string) $alc->C_C552->D_5189,
                "special_service_description_code" => (string) $alc->C_C214->D_7161
            ];
            return $data;
        }
        return [];
    }
}
