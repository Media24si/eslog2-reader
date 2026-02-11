<?php

namespace Media24si\eSlog2_reader;

use Media24si\eSlog2_reader\FreeText;
use Media24si\eSlog2_reader\Segments\DateTimePeriod;

class ParseInvoiceXML
{
    private DateTimePeriod $dateTimePeriod;
    private FreeText $freeText;
    private ParseSegmentType $segmentParser;

    public function __construct()
    {
        $this->dateTimePeriod = new DateTimePeriod();
        $this->freeText = new FreeText();
        $this->segmentParser = new ParseSegmentType();
    }

    public const FUNCTION_CANCELLATION = 1;
    public const FUNCTION_REPLACE = 5;
    public const FUNCTION_DUPLICATE = 7;
    public const FUNCTION_ORIGINAL = 9;
    public const FUNCTION_COPY = 31;
    public const FUNCTION_ADDITIONAL_TRANSMISSION = 43;

    public const PAYMENT_REQUIRED = 0;
    public const PAYMENT_DIRECT_DEBIT = 1;
    public const PAYMENT_ALREADY_PAID = 2;
    public const PAYMENT_OTHER_NO_PAYMENT = 3;

    public const LOCATION_PAYMENT = 57;
    public const LOCATION_ISSUED = 91;
    public const LOCATION_SALE = 162;
    public string $xml_file;

    private function readXML($xml_file): array
    {
        if (!file_exists($xml_file)) {
            throw new \InvalidArgumentException("File not found: $xml_file");
        }

        if (!is_readable($xml_file)) {
            throw new \InvalidArgumentException("File is not readable: $xml_file");
        }

        libxml_use_internal_errors(true);
        $xmlFile = @simplexml_load_file($xml_file);

        if ($xmlFile === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            $errorMsg = $errors ? $errors[0]->message : 'Unknown XML parsing error';
            throw new \RuntimeException("Failed to parse XML file: " . trim($errorMsg));
        }

        if (!isset($xmlFile->M_INVOIC)) {
            throw new \RuntimeException("Invalid eSLOG invoice: M_INVOIC element not found");
        }

        $invoice = $xmlFile->M_INVOIC;
        $headers = $this->parseHeaders($invoice);
        $documentTypeAndId = $this->parseDocumentTypeAndId($invoice);
        $documentDateTimePeriod = $this->parseDocumentDateTimePeriod($invoice);
        $documentFreeText = $this->parseDocumentFreeText($invoice);
        $segments = $this->parseDocumentSegments($invoice);

        $invoiceData = array_merge($headers, $documentTypeAndId, $documentDateTimePeriod, $documentFreeText, $segments);
        return $invoiceData;
    }

    public function getAllData($file)
    {
        $data = $this->readXML($file);
        if ($data !== null) {
            return $data;
        }
    }
    public function getSpecificData($file) //Get specific data
    {
        $xml = $this->readXML($file);
        return [
            'document_type' => $xml['document_type'] ?? null, //tip dokumenta
            'document_number' => $xml['document_identifier'] ?? null, //št. dokumenta
            'document_date' => $xml['document_date'] ?? null, //datum dokumenta
            'payment_due_date' => $xml['payment_terms']['payment_due_date'] ?? null, //datum zapadlosti / rok plačila
            'delivery_date' => $xml['delivery_date'] ?? null, //datum dostave/opravljene storitve
            'tax_point_date' => $xml['tax_point_date'] ?? null, //davčni datum
            'total_amount_without_vat' => $xml['total_amount_without_vat'] ?? null, //NETO brez DDV,
            'total_amount_with_vat' => $xml['total_amount_with_vat'] ?? null, //Saldo
            'buyer_name' => $xml['buyer']['name'] ?? null, //Prejemnik, ime podjetja
            'payment_type' => substr($xml['payment_reference'], 0, 2) ?? null, //Tip reference
            'payment_model' => substr($xml['payment_reference'], 2, 2) ?? null, //Model reference
            'payment_reference_number' => substr($xml['payment_reference'], 4) ?? null, //Sklic prejemnika
            'reference_currency' => $xml['reference_currency'] ?? null, //Valuta 
            'vat_registration_number' => substr($xml['seller_references']['vat_registration_number'], 2) ?? null //Davčna številka.
        ];
    }

    private function parseHeaders($invoice): array
    {
        if (!isset($invoice->S_UNH) || !isset($invoice->S_UNH->C_S009)) {
            throw new \RuntimeException("Invalid invoice: Missing required S_UNH header");
        }
        $header = $invoice->S_UNH;
        $documentIdentifiers = $header->C_S009;
        return [
            "document_reference_number" => (string) $header->D_0062,
            "document_type" => (string) $documentIdentifiers->D_0065,
            "document_version_number" => (string) $documentIdentifiers->D_0052,
            "message_release_number" => (string) $documentIdentifiers->D_0054,
            "controlling_agency" => (string) $documentIdentifiers->D_0051,
        ];
    }
    private function parseDocumentTypeAndId($invoice): array
    {
        if (!isset($invoice->S_BGM) || !isset($invoice->S_BGM->C_C002) || !isset($invoice->S_BGM->C_C106)) {
            throw new \RuntimeException("Invalid invoice: Missing required S_BGM segment");
        }
        $bgm = $invoice->S_BGM;
        return [
            'document_type' => (string) DocumentType::getName((int) $bgm->C_C002->D_1001),
            'document_identifier' => (string) $bgm->C_C106->D_1004
        ];
    }
    private function parseDocumentDateTimePeriod($invoice): array
    {
        $dateTimePeriods = [];
        foreach ($invoice->S_DTM as $dtm) {
            $dateTimePeriods[$this->dateTimePeriod->getDocumentDateType($dtm)] = $this->dateTimePeriod->getDocumentDate($dtm);
        }

        return $dateTimePeriods;
    }
    private function parseDocumentFreeText($invoice): array
    {
        $freeText = $invoice->S_FTX;
        $freeTextInfo = [];

        foreach ($freeText as $ftx) {
            $freeTextSubjectCodeQualifier = $ftx->D_4451;
            $freeTextType = $this->freeText->getFreeTextType((string) $freeTextSubjectCodeQualifier);
            if (isset($ftx->C_C108)) {
                $lines = [];
                foreach ($ftx->C_C108->children() as $name => $value) {
                    if (str_starts_with($name, 'D_4440')) {
                        $text = (string) $value;
                        if (!empty($text)) {
                            $lines[] = $text;
                        }
                    }
                }
                $freeTextInfo[$freeTextType] = count($lines) === 1 ? $lines[0] : $lines;
            } elseif (isset($ftx->C_C107)) {
                $lines = [];
                foreach ($ftx->C_C107->children() as $name => $value) {
                    $text = (string) $value;
                    if (!empty($text)) {
                        $lines[] = $text;
                    }
                }
                $freeTextInfo[$freeTextType] = count($lines) === 1 ? $lines[0] : $lines;
            }
        }
        return $freeTextInfo;
    }
    private function parseDocumentSegments($invoice)
    {
        $segments = [];
        $lineItems = [];
        $taxBreakdowns = [];
        $allowances = [];
        $charges = [];

        foreach ($invoice->children() as $child) {
            $childName = $child->getName();
            if (strpos($childName, "G_SG") === 0) {
                $segmentData = $this->segmentParser->getSegmentTypeInfo($child);
                if (!empty($segmentData) && is_array($segmentData)) {
                    if ($childName === "G_SG26") {
                        $lineItems = $lineItems + $segmentData;
                    } elseif ($childName === "G_SG52" && isset($segmentData['tax_breakdown'])) {
                        $taxBreakdowns[] = $segmentData['tax_breakdown'];
                    } elseif ($childName === "G_SG16") {
                        if (isset($segmentData['allowance'])) {
                            $allowances[] = $segmentData['allowance'];
                        }
                        if (isset($segmentData['charge'])) {
                            $charges[] = $segmentData['charge'];
                        }
                    } else {
                        $segments = array_merge($segments, $segmentData);
                    }
                }
            }
        }

        if (!empty($lineItems)) {
            $segments['line_items'] = $lineItems;
        }
        if (!empty($taxBreakdowns)) {
            $segments['tax_breakdown'] = $taxBreakdowns;
        }
        if (!empty($allowances)) {
            $segments['allowances'] = $allowances;
        }
        if (!empty($charges)) {
            $segments['charges'] = $charges;
        }

        return $segments;
    }
}
