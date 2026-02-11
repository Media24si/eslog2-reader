<?php

namespace Media24si\eSlog2_reader\Segments;

class MonetaryAmount
{
    public const TYPE_LINE_ITEM_AMOUNT = 203;
    public const TYPE_INVOICE_ITEM_AMOUNT = 38;
    public const TYPE_TAX_AMOUNT = 124;
    public const TYPE_TAXABLE_AMOUNT = 125;
    public const TYPE_ALLOWANCE_AMOUNT = 204;
    public const TYPE_CHARGE_ALLOWANCE_BASIS = 25;
    public const TYPE_UNIT_ALLOWANCE_AMOUNT = 509;
    public const TYPE_CHARGE_AMOUNT = 23;
    public const TYPE_ROUNDING_AMOUNT = 2;
    public const TYPE_AMOUNT_DUE_FOR_PAYMENT = 9;
    public const TYPE_TOTAL_LINE_ITEM_AMOUNT = 79;
    public const TYPE_TOTAL_ALLOWANCES_AMOUNT = 92;
    public const TYPE_TOTAL_CHARGES_AMOUNT = 99;
    public const TYPE_PAID_AMOUNT = 113;
    public const TYPE_TOTAL_AMOUNT_WITHOUT_VAT = 128;
    public const TYPE_TOTAL_AMOUNT_WITH_VAT = 131;
    public const TYPE_TOTAL_VAT_AMOUNT = 176;
    public const TYPE_ROUNDING_AMOUNT_ALT = 165;
    public const TYPE_DOCUMENT_LEVEL_ALLOWANCES = 260;
    public const TYPE_DOCUMENT_LEVEL_CHARGES = 259;
    public const TYPE_PREPAID_AMOUNT = 366;
    public const TYPE_INVOICE_TOTAL_WITHOUT_VAT_ALT = 389;
    public const TYPE_INVOICE_TOTAL_WITH_VAT_ALT = 388;

    public function getMonetaryAmount($moa)
    {
        return match ((int) $moa->C_C516->D_5025) {
            self::TYPE_LINE_ITEM_AMOUNT => ["line_item_amount" => (string) $moa->C_C516->D_5004],
            self::TYPE_INVOICE_ITEM_AMOUNT => ["invoice_item_amount" => (string) $moa->C_C516->D_5004],
            self::TYPE_TAX_AMOUNT => ["tax_amount" => (string) $moa->C_C516->D_5004],
            self::TYPE_TAXABLE_AMOUNT => ["taxable_amount" => (string) $moa->C_C516->D_5004],
            self::TYPE_ALLOWANCE_AMOUNT => ["allowance_amount" => (string) $moa->C_C516->D_5004],
            self::TYPE_CHARGE_ALLOWANCE_BASIS => ["charge_allowance_basis" => (string) $moa->C_C516->D_5004],
            self::TYPE_UNIT_ALLOWANCE_AMOUNT => ["unit_allowance_amount" => (string) $moa->C_C516->D_5004],
            self::TYPE_CHARGE_AMOUNT => ["charge_amount" => (string) $moa->C_C516->D_5004],
            self::TYPE_ROUNDING_AMOUNT => ["rounding_amount" => (string) $moa->C_C516->D_5004],
            self::TYPE_ROUNDING_AMOUNT_ALT => ["rounding_amount" => (string) $moa->C_C516->D_5004],
            self::TYPE_AMOUNT_DUE_FOR_PAYMENT => ["amount_due_for_payment" => (string) $moa->C_C516->D_5004],
            self::TYPE_TOTAL_LINE_ITEM_AMOUNT => ["total_line_item_amount" => (string) $moa->C_C516->D_5004],
            self::TYPE_TOTAL_ALLOWANCES_AMOUNT => ["total_allowances_amount" => (string) $moa->C_C516->D_5004],
            self::TYPE_TOTAL_CHARGES_AMOUNT => ["total_charges_amount" => (string) $moa->C_C516->D_5004],
            self::TYPE_TOTAL_AMOUNT_WITHOUT_VAT => ["total_amount_without_vat" => (string) $moa->C_C516->D_5004],
            self::TYPE_INVOICE_TOTAL_WITHOUT_VAT_ALT => ["total_amount_without_vat" => (string) $moa->C_C516->D_5004],
            self::TYPE_TOTAL_VAT_AMOUNT => ["total_vat_amount" => (string) $moa->C_C516->D_5004],
            self::TYPE_TOTAL_AMOUNT_WITH_VAT => ["total_amount_with_vat" => (string) $moa->C_C516->D_5004],
            self::TYPE_INVOICE_TOTAL_WITH_VAT_ALT => ["total_amount_with_vat" => (string) $moa->C_C516->D_5004],
            self::TYPE_PAID_AMOUNT => ["paid_amount" => (string) $moa->C_C516->D_5004],
            self::TYPE_DOCUMENT_LEVEL_ALLOWANCES => ["document_level_allowances" => (string) $moa->C_C516->D_5004],
            self::TYPE_DOCUMENT_LEVEL_CHARGES => ["document_level_charges" => (string) $moa->C_C516->D_5004],
            self::TYPE_PREPAID_AMOUNT => ["prepaid_amount" => (string) $moa->C_C516->D_5004],
            default => ["monetary_amount_" . (int) $moa->C_C516->D_5025 => (string) $moa->C_C516->D_5004],
        };
    }
}
