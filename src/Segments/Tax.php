<?php

namespace Media24si\eSlog2_reader\Segments;

use SimpleXMLElement;

class Tax
{
    public const TAX_TYPE_VAT = "VAT";
    public const TAX_TYPE_OTHER = "OTH";
    public const CATEGORY_STANDARD_RATE = "S";
    public const CATEGORY_ZERO_RATED_GOODS = "Z";
    public const CATEGORY_EXEMPT_FROM_TAX = "E";
    public const CATEGORY_VAT_REVERSE_CHARGE = "AE";
    public const CATEGORY_VAT_EXEMPT_FOR_EAA_INTRACOMMUNITY = "K";
    public const CATEGORY_FREE_EXPORT_ITEM = "G";
    public const CATEGORY_SERVICES_OUTSIDE_TAX_SCOPE = "O";
    public const CATEGORY_CANARY_ISLANDS_GENERAL_INDIRECT_TAX = "O";
    public const CATEGORY_TAX_FOR_PRODUCTION_SERVICES_AND_IMPORTATION_IN_CEUTA_AND_MELIA = "M";

    public function getTaxInfo(SimpleXMLElement $tax): array
    {
        $taxType = match ((string) $tax->C_C241->D_5153) {
            self::TAX_TYPE_VAT => "value_added_tax",
            self::TAX_TYPE_OTHER => "other_tax"
        };
        $taxCategory = match ((string) $tax->D_5305) {
            self::CATEGORY_STANDARD_RATE => "standard_rate",
            self::CATEGORY_ZERO_RATED_GOODS => "zero_rated_goods",
            self::CATEGORY_EXEMPT_FROM_TAX => "exempt_from_tax",
            self::CATEGORY_VAT_REVERSE_CHARGE => "vat_reverse_charge",
            self::CATEGORY_VAT_EXEMPT_FOR_EAA_INTRACOMMUNITY => "vat_exempt_for_eaa_intracommunity_supply_of_goods_and_services",
            self::CATEGORY_FREE_EXPORT_ITEM => "free_export_item",
            self::CATEGORY_SERVICES_OUTSIDE_TAX_SCOPE => "services_outside_scope_of_tax",
            self::CATEGORY_CANARY_ISLANDS_GENERAL_INDIRECT_TAX => "canary_islands_general_indirect_tax",
            self::CATEGORY_TAX_FOR_PRODUCTION_SERVICES_AND_IMPORTATION_IN_CEUTA_AND_MELIA => "tax_for_production_services_and_importation_in_ceuta_and_melila",
            default => "no_tax_category"
        };
        return [$taxType => [$taxCategory => ["tax_rate" => (float) $tax->C_C243->D_5278]]];
    }
}
