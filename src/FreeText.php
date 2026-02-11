<?php

namespace Media24si\eSlog2_reader;

class FreeText
{
    public const TEXT_PAYMENT_TERMS = "AAB";
    public const TEXT_SELLER_LEGAL_INFO = "REG";
    public const TEXT_INVOICE_NOTE = "GEN";
    public const TEXT_SPECIFICATION_ID = "DOC";
    public const TEXT_PAYMENT_MEANS = "AAT";
    public const TEXT_VAT_EXEMPTION_REASON = "AGM";
    public const TEXT_REMITTANCE_UNSTRUCTURED = "PMD";
    public const TEXT_FISCAL_DATA = "TXD";
    public const TEXT_PAYMENT_METHOD_EXT = "PAI";
    public const TEXT_PAYMENT_PURPOSE = "ALQ";
    public const TEXT_ITEM_DESCRIPTION = "AAA";
    public const TEXT_ADDITIONAL_PRODUCT_INFORMATION = "ACB";
    public const TEXT_ADDITIONAL_PRODUCT_ATTRIBUTE_INFORMATION = "ACF";


    public function getFreeTextType(string $freeTextSubjectCodeQualifier): string
    {
        return match ($freeTextSubjectCodeQualifier) {
            self::TEXT_PAYMENT_TERMS => "payment_terms_text",
            self::TEXT_SELLER_LEGAL_INFO => "seller_legal_info",
            self::TEXT_INVOICE_NOTE => "invoice_note",
            self::TEXT_SPECIFICATION_ID => "specification_id",
            self::TEXT_PAYMENT_MEANS => "payment_means_text",
            self::TEXT_VAT_EXEMPTION_REASON => "vat_exemption_reason",
            self::TEXT_REMITTANCE_UNSTRUCTURED => "remittance_unstructured",
            self::TEXT_FISCAL_DATA => "fiscal_data",
            self::TEXT_PAYMENT_METHOD_EXT => "payment_method_ext",
            self::TEXT_PAYMENT_PURPOSE => "payment_purpose",
            self::TEXT_ITEM_DESCRIPTION => "item_description",
            default => "unknown type ($freeTextSubjectCodeQualifier)"
        };
    }
    public function getAdditionalProductInformation($ftx)
    {
        $code = (string) $ftx->D_4451;

        if ($code === self::TEXT_ADDITIONAL_PRODUCT_INFORMATION || $code === self::TEXT_ADDITIONAL_PRODUCT_ATTRIBUTE_INFORMATION) {
            $lines = [];

            if (isset($ftx->C_C108)) {
                foreach ($ftx->C_C108->children() as $name => $value) {
                    if (str_starts_with($name, 'D_4440')) {
                        $text = (string) $value;
                        if (!empty($text)) {
                            $lines[] = $text;
                        }
                    }
                }
            }

            $key = $code === self::TEXT_ADDITIONAL_PRODUCT_INFORMATION
                ? "additional_information"
                : "additional_product_attribute_information";

            return [$key => $lines];
        }

        return [];
    }
}
