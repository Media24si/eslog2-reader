<?php

namespace Media24si\eSlog2_reader\Segments;

use SimpleXMLElement;

class DateTimePeriod
{
    public const TYPE_DOCUMENT_DATE = 137;
    public const TYPE_TAX_POINT_DATE = 131;
    public const TYPE_INVOICE_DATE = 3;
    public const TYPE_DELIVERY_DATE_ACTUAL = 35;
    public const TYPE_PAID_TO_DATE = 432;
    public const TYPE_INVOICING_PERIOD_START = 167;
    public const TYPE_INVOICING_PERIOD_END = 168;
    public const TYPE_PAYMENT_DUE_DATE = 13;
    public const TYPE_PREVIOUS_INVOICE_DATE = 384;

    public function getDocumentDateType(SimpleXMLElement $dtm): string
    {
        return match ((int) $dtm->C_C507->D_2005) {
            self::TYPE_TAX_POINT_DATE => "tax_point_date",
            self::TYPE_DOCUMENT_DATE => "document_date",
            self::TYPE_INVOICE_DATE => "invoice_date",
            self::TYPE_DELIVERY_DATE_ACTUAL => "delivery_date",
            self::TYPE_PAID_TO_DATE => "paid_date",
            self::TYPE_INVOICING_PERIOD_START => "invoicing_period_start",
            self::TYPE_INVOICING_PERIOD_END => "invoicing_period_end",
            self::TYPE_PAYMENT_DUE_DATE => "payment_due_date",
            self::TYPE_PREVIOUS_INVOICE_DATE => "previous_invoice",
            default => "unknown_date_type (" . (int) $dtm->C_C507->D_2005 . ")",
        };
    }
    public function getDocumentDate(SimpleXMLElement $dtm): string
    {
        return (string) $dtm->C_C507->D_2380;
    }
}
