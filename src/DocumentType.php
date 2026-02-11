<?php

namespace Media24si\eSlog2_reader;

class DocumentType
{
    public const TYPE_MEASURED_SERVICES = 82;
    public const TYPE_FINANCIAL_CREDIT_NOTE = 83;
    public const TYPE_FINANCIAL_DEBIT_NOTE = 84;
    public const TYPE_INVOICING_DATA_SHEET = 130;
    public const TYPE_PROFORMA_INVOICE = 325;
    public const TYPE_INVOICE = 380;
    public const TYPE_CREDIT_NOTE = 381;
    public const TYPE_COMMISION_NOTE = 382;
    public const TYPE_DEBIT_NOTE = 383;
    public const TYPE_CORRECTED_INVOICE = 384;
    public const TYPE_CONSOLIDATED_INVOICE = 385;
    public const TYPE_PREPAYMENT_INVOICE = 386;
    public const TYPE_SELF_BILLED_INVOICE = 389;
    public const TYPE_DELCREDRE_INVOICE = 390;
    public const TYPE_FACTORED_INVOICE = 393;

    public static function getName(int $type_id): string
    {
        return match ($type_id) {
            self::TYPE_MEASURED_SERVICES => 'measured_services',
            self::TYPE_FINANCIAL_CREDIT_NOTE => 'financial_credit_note',
            self::TYPE_FINANCIAL_DEBIT_NOTE => 'financial_debit_note',
            self::TYPE_INVOICING_DATA_SHEET => 'invoicing_data_sheet',
            self::TYPE_PROFORMA_INVOICE => 'proforma_invoice',
            self::TYPE_INVOICE => 'invoice',
            self::TYPE_CREDIT_NOTE => 'credit_note',
            self::TYPE_COMMISION_NOTE => 'commission_note',
            self::TYPE_DEBIT_NOTE => 'debit_note',
            self::TYPE_CORRECTED_INVOICE => 'corrected_invoice',
            self::TYPE_CONSOLIDATED_INVOICE => 'consolidated_invoice',
            self::TYPE_PREPAYMENT_INVOICE => 'prepayment_invoice',
            self::TYPE_SELF_BILLED_INVOICE => 'self_billed_invoice',
            self::TYPE_DELCREDRE_INVOICE => 'delcredere_invoice',
            self::TYPE_FACTORED_INVOICE => 'factored_invoice',
        };
    }
}
