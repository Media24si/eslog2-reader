<?php

namespace Media24si\eSlog2Reader\Segments;

class LineItem
{
    public function getLineItem($lin): array
    {
        return [
            'line_item_id' => (string) $lin->D_1082,
            "item_id" => (string) $lin->C_C212->D_7140,
            "item_type_id" => (string) $lin->C_C212->D_7143,
        ];
    }
}
