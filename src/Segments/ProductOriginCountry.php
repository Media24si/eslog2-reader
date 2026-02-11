<?php

namespace Media24si\eSlog2Reader\Segments;

use SimpleXMLElement;

class ProductOriginCountry
{
    public function getProductOriginCountry(SimpleXMLElement $ali): array
    {
        return ["origin_country_code" => (string) $ali->D_3239];
    }
}
