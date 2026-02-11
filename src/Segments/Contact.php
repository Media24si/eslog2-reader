<?php

namespace Media24si\eSlog2_reader\Segments;

use SimpleXMLElement;

class Contact
{
    public const TYPE_INFORMATION_CONTACT = "IC";
    public const TYPE_PURCHASING_CONTACT = "PD";
    public const TYPE_SUPPLIER_CONTACT = "SU";

    public const COMM_EMAIL = "EM";
    public const COMM_TELEPHONE = "TE";
    public const COMM_FAX = "FX";

    public function getContactInfo(SimpleXMLElement $segment): array
    {
        $ctaSegment = $segment->S_CTA;
        $contactType = (string) ($ctaSegment->D_3139);
        $contactName = (string) ($ctaSegment->C_C056->D_3412);

        $communications = [];
        if (isset($segment->S_COM)) {
            foreach ($segment->S_COM as $com) {
                $commType = (string) ($com->C_C076->D_3155);
                $commValue = (string) ($com->C_C076->D_3148);

                $commKey = match ($commType) {
                    self::COMM_EMAIL => 'email',
                    self::COMM_TELEPHONE => 'telephone',
                    self::COMM_FAX => 'fax',
                    default => 'other',
                };

                if (!isset($communications[$commKey])) {
                    $communications[$commKey] = [];
                }
                $communications[$commKey][] = $commValue;
            }
        }

        $contactKey = match ($contactType) {
            self::TYPE_INFORMATION_CONTACT => 'information_contact',
            self::TYPE_PURCHASING_CONTACT => 'purchasing_contact',
            self::TYPE_SUPPLIER_CONTACT => 'supplier_contact',
            default => 'contact',
        };

        return [
            $contactKey => [
                'name' => $contactName,
                'communications' => $communications,
            ]
        ];
    }
}
