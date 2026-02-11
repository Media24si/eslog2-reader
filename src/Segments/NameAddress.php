<?php

namespace Media24si\eSlog2_reader\Segments;

use Media24si\eSlog2_reader\XMLHelpers;
use SimpleXMLElement;

class NameAddress
{
    public const TYPE_BUYER_PARTY_FUNCTION_CODE_QUALIFIER = 'BY';
    public const TYPE_SELLER_PARTY_FUNCTION_CODE_QUALIFIER = 'SE';
    public const TYPE_PAYEE_PARTY_FUNCTION_CODE_QUALIFIER = 'PE';
    public const TYPE_DELIVERY_PARTY_FUNCTION_CODE_QUALIFIER = 'DP';
    public const TYPE_TAX_DECLARATION_PARTY_FUNCTION_CODE_QUALIFIER = 'LC';


    public function getDocumentNameAddress(SimpleXMLElement $nad): array|null
    {
        $type = (string) $nad->D_3035;

        $key = match ($type) {
            self::TYPE_BUYER_PARTY_FUNCTION_CODE_QUALIFIER => 'buyer',
            self::TYPE_SELLER_PARTY_FUNCTION_CODE_QUALIFIER => 'seller',
            self::TYPE_PAYEE_PARTY_FUNCTION_CODE_QUALIFIER => 'payee',
            self::TYPE_DELIVERY_PARTY_FUNCTION_CODE_QUALIFIER => 'delivery',
            self::TYPE_TAX_DECLARATION_PARTY_FUNCTION_CODE_QUALIFIER => 'tax_representive',
            default => null,
        };

        if ($key === null) {
            return null;
        }

        $info = match ($type) {
            self::TYPE_BUYER_PARTY_FUNCTION_CODE_QUALIFIER => self::parsePartyInfo($nad),
            self::TYPE_SELLER_PARTY_FUNCTION_CODE_QUALIFIER => self::parsePartyInfo($nad),
            self::TYPE_PAYEE_PARTY_FUNCTION_CODE_QUALIFIER => self::parsePayeeInfo($nad),
            self::TYPE_DELIVERY_PARTY_FUNCTION_CODE_QUALIFIER => self::parseDeliveryInfo($nad),
            self::TYPE_TAX_DECLARATION_PARTY_FUNCTION_CODE_QUALIFIER => self::parsePartyInfo($nad),
            default => null,
        };

        return [$key => $info];
    }

    private static function getAddressLines(SimpleXMLElement $nad): array
    {
        $addressLines = [];
        if (isset($nad->C_C059)) {
            foreach ($nad->C_C059->children() as $child) {
                $childName = $child->getName();
                if (strpos($childName, 'D_3042') === 0) {
                    $line = (string) $child;
                    if ($line !== '') {
                        $addressLines[] = $line;
                    }
                }
            }
        }
        return $addressLines;
    }

    private static function parsePartyInfo(SimpleXMLElement $nad): array
    {
        return [
            "identifier" => (string) ($nad->C_C082->D_3039),
            "identifier_scheme" => (string) ($nad->C_C082->D_1131),
            "name" => (string) ($nad->C_C080->D_3036),
            "trading_name" => (string) ($nad->C_C080->D_3036_2),
            "address_lines" => self::getAddressLines($nad),
            "city" => (string) ($nad->D_3164),
            "region" => (string) ($nad->C_C819->D_3228),
            "postal_code" => (string) ($nad->D_3251),
            "country_code" => (string) ($nad->D_3207),
        ];
    }

    private static function parsePayeeInfo(SimpleXMLElement $nad): array
    {
        return [
            "identifier" => (string) ($nad->C_C082->D_3039),
            "identifier_scheme" => (string) ($nad->C_C082->D_1131),
            "name" => (string) ($nad->C_C080->D_3036),
        ];
    }

    private static function parseDeliveryInfo(SimpleXMLElement $nad): array
    {
        return [
            "identifier" => (string) ($nad->C_C082->D_3039),
            "identifier_scheme" => (string) ($nad->C_C082->D_1131),
            "name" => (string) ($nad->C_C080->D_3036),
            "address_lines" => self::getAddressLines($nad),
            "city" => (string) ($nad->D_3164),
            "region" => (string) ($nad->C_C819->D_3228),
            "postal_code" => (string) ($nad->D_3251),
            "country_code" => (string) ($nad->D_3207),
        ];
    }
}
