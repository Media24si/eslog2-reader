<?php

namespace Media24si\eSlog2Reader\Segments;

use SimpleXMLElement;

class ItemDescription
{
    public const TYPE_FREE_FORM = "F";
    public const TYPE_FREE_FORM_LONG_DESCRIPTION = "A";

    public function getItemDescription(SimpleXMLElement $imd): array
    {
        return match ((string) $imd->D_7077) {
            self::TYPE_FREE_FORM => ["item_name" => (string) $imd->C_C273->D_7008],
            self::TYPE_FREE_FORM_LONG_DESCRIPTION => ["item_description" => (string) $imd->C_C273->D_7008],
        };
    }
}
