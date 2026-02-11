<?php

namespace Media24si\eSlog2Reader\Segments;

class ReferenceDocument
{
    public const TYPE_PROFORMA_INVOICE = 'AAB';
    public const TYPE_DELIVERY_ORDER_NUMBER = 'AAJ';
    public const TYPE_DELIVERY_FORM = 'AAK';
    public const TYPE_BENEFICIARYS_REFERENCE = 'AFO';
    public const TYPE_PROJECT_NUMBER = 'AEP';
    public const TYPE_CONSODILDATED_INVOICE = 'AIZ';
    public const TYPE_MESSAGE_BATCH_NUMBER = 'ALL';
    public const TYPE_RECEIVING_ADVICE_NUMBER = 'ALO';
    public const TYPE_EXTERNAL_OBJECT_REFERENCE = 'ATS';
    public const TYPE_COMMERCIAL_ACCOUNT_SUMMARY = 'APQ';
    public const TYPE_CREDIT_NOTE = 'CD';
    public const TYPE_CUSTOMER_REFERENCE_NUMBER = 'CR';
    public const TYPE_CONTRACT = 'CT';
    public const TYPE_DEBIT_NOTE = 'DL';
    public const TYPE_DELIVERY_NOTE = 'DQ';
    public const TYPE_IMPORT_LICENCE_NUMBER = 'IP';
    public const TYPE_INVOICE = 'IV';
    public const TYPE_GOVERNMENT_CONTRACT_NUMBER = 'GC';
    public const TYPE_ORDER_NUMBER = 'ON';
    public const TYPE_PREVIOUS_INVOICE_NUMBER = 'OI';
    public const TYPE_PRICE_LIST_NUMBER = 'PL';
    public const TYPE_PURCHASE_ORDER_RESPONSE_NUMBER = 'POR';
    public const TYPE_PAYMENT_REFERENCE = 'PQ';
    public const TYPE_EXPORT_REFERENCE_NUMBER = 'RF';
    public const TYPE_SPECIFICATION_NUMBER = 'SZ';
    public const TYPE_ORDER_NUMBER_SUPPLIER = 'VN';
    public const TYPE_VAT_REGISTRATION_NUMBER = 'VA';
    public const TYPE_COST_ACCOUNT = "AOU";
    public const TYPE_LEGAL_ENTITY_IDENTIFIER = "0199";
    public const TYPE_TAX_REGISTRATION_NUMBER = "AHP";
    public const TYPE_MANDATE_REFERENCE = "AVS";
    public const TYPE_BANK_COMMON_TRANSACTION_REFERENCE_NUMBER = "AII";
    public const TYPE_METERING_POINT = "AVE";
    public const TYPE_ACCOUNTING_ENTRY = "AWQ";

    public function getReferenceDocumentInfo(array $rff): array
    {
        $qualifiers = [];
        foreach ($rff as $reference) {
            $referenceQualifier = match ((string) $reference->C_C506->D_1153) {
                self::TYPE_PROFORMA_INVOICE => "proforma_Invoice",
                self::TYPE_DELIVERY_ORDER_NUMBER => "delivery_order_number",
                self::TYPE_DELIVERY_FORM => "delivery_form",
                self::TYPE_BENEFICIARYS_REFERENCE => "beneficiary_reference",
                self::TYPE_PROJECT_NUMBER => "project_number",
                self::TYPE_CONSODILDATED_INVOICE => "consolidated_invoice",
                self::TYPE_MESSAGE_BATCH_NUMBER => "message_batch_mumber",
                self::TYPE_RECEIVING_ADVICE_NUMBER => "receiving_advice_number",
                self::TYPE_EXTERNAL_OBJECT_REFERENCE => "external_object_reference",
                self::TYPE_COMMERCIAL_ACCOUNT_SUMMARY => "commercial_account_summary",
                self::TYPE_CREDIT_NOTE => "credit_note",
                self::TYPE_CUSTOMER_REFERENCE_NUMBER => "customer_reference_number",
                self::TYPE_CONTRACT => "contract",
                self::TYPE_DEBIT_NOTE => "debit_note",
                self::TYPE_DELIVERY_NOTE => "delivery_note",
                self::TYPE_IMPORT_LICENCE_NUMBER => "import_licence_number",
                self::TYPE_INVOICE => "invoice",
                self::TYPE_GOVERNMENT_CONTRACT_NUMBER => "government_contract_number",
                self::TYPE_ORDER_NUMBER => "order_number",
                self::TYPE_PREVIOUS_INVOICE_NUMBER => "previous_invoice_number",
                self::TYPE_PRICE_LIST_NUMBER => "price_list_number",
                self::TYPE_PURCHASE_ORDER_RESPONSE_NUMBER => "purchase_order_response_number",
                self::TYPE_PAYMENT_REFERENCE => "payment_reference",
                self::TYPE_EXPORT_REFERENCE_NUMBER => "export_reference_number",
                self::TYPE_SPECIFICATION_NUMBER => "specification_number",
                self::TYPE_ORDER_NUMBER_SUPPLIER => "order_number_supplier",
                self::TYPE_VAT_REGISTRATION_NUMBER => "vat_registration_number",
                self::TYPE_COST_ACCOUNT => "cost_account",
                self::TYPE_LEGAL_ENTITY_IDENTIFIER => "legal_entity_identifier",
                self::TYPE_TAX_REGISTRATION_NUMBER => "tax_registration_number",
                self::TYPE_MANDATE_REFERENCE => "mandate_reference",
                self::TYPE_BANK_COMMON_TRANSACTION_REFERENCE_NUMBER => "bank_common_transaction_reference_number",
                self::TYPE_METERING_POINT => "metering_point",
                self::TYPE_ACCOUNTING_ENTRY => "accounting_entry",
                default => "Unknown Reference Type (" . (string) $reference->C_C506->D_1153 . ")",
            };
            $referenceNumber = (string) ($reference->C_C506->D_1154 ?? $reference->C_C506->D_1156);
            $qualifiers[$referenceQualifier] = $referenceNumber;
        }
        return $qualifiers;
    }
}
