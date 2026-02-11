<?php

namespace Media24si\eSlog2Reader\Segments;

use SimpleXMLElement;

class FinancialInfo
{
    public const BUYER_FINANCIAL_INSTITUTION = "BI";
    public const BUYER_BANK = "BB";
    public const PAYING_FINANCIAL_INSTITUITION = "PB";
    public const RECIVING_FINANCIAL_INSTITUTION = "RB";

    public function getFinancialInfo(SimpleXMLElement $fii): array|null
    {
        $type = (string) $fii->D_3035;

        $key = match ($type) {
            self::BUYER_FINANCIAL_INSTITUTION => 'buyer_financial_institution',
            self::BUYER_BANK => 'buyer_bank',
            self::PAYING_FINANCIAL_INSTITUITION => 'paying_financial_institution',
            self::RECIVING_FINANCIAL_INSTITUTION => 'receiving_financial_institution',
            default => null,
        };

        if ($key === null) {
            return null;
        }

        $info = self::getPartyFinancialInfo($fii);

        return [$key => $info];
    }
    private static function getPartyFinancialInfo(SimpleXMLElement $fii): array
    {
        return [
            "account_identifier" => (string) $fii->C_C078->D_3194,
            "account_holder_name" => (string) $fii->C_C078->D_3192,
            "institution_name_code" => (string) $fii->C_C088->D_3433,
        ];
    }
}
