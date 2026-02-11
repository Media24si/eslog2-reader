<?php

namespace Media24si\eSlog2_reader;

use Media24si\eSlog2_reader\Segments\AdditionalProductID;
use Media24si\eSlog2_reader\Segments\Allowance;
use Media24si\eSlog2_reader\Segments\Contact;
use Media24si\eSlog2_reader\Segments\DateTimePeriod;
use Media24si\eSlog2_reader\Segments\FinancialInfo;
use Media24si\eSlog2_reader\Segments\NameAddress;
use Media24si\eSlog2_reader\Segments\PercentageDetails;
use Media24si\eSlog2_reader\Segments\ReferenceDocument;
use Media24si\eSlog2_reader\Segments\Currency;
use Media24si\eSlog2_reader\Segments\ItemDescription;
use Media24si\eSlog2_reader\Segments\LineItem;
use Media24si\eSlog2_reader\Segments\MonetaryAmount;
use Media24si\eSlog2_reader\Segments\PriceDetails;
use Media24si\eSlog2_reader\Segments\ProductOriginCountry;
use Media24si\eSlog2_reader\Segments\Quantity;
use Media24si\eSlog2_reader\Segments\Tax;

class ParseSegmentType
{
    private AdditionalProductID $additionalProductID;
    private Allowance $allowance;
    private Contact $contact;
    private Currency $currency;
    private DateTimePeriod $dateTimePeriod;
    private FinancialInfo $financialInfo;
    private ItemDescription $itemDescription;
    private LineItem $lineItem;
    private MonetaryAmount $monetaryAmount;
    private NameAddress $nameAddress;
    private PriceDetails $priceDetails;
    private ProductOriginCountry $productOriginCountry;
    private Quantity $quantity;
    private ReferenceDocument $referenceDocument;
    private Tax $tax;
    private FreeText $freeText;
    private PercentageDetails $percentageDetails;

    public function __construct()
    {
        $this->additionalProductID = new AdditionalProductID();
        $this->allowance = new Allowance();
        $this->contact = new Contact();
        $this->currency = new Currency();
        $this->dateTimePeriod = new DateTimePeriod();
        $this->financialInfo = new FinancialInfo();
        $this->itemDescription = new ItemDescription();
        $this->lineItem = new LineItem();
        $this->monetaryAmount = new MonetaryAmount();
        $this->nameAddress = new NameAddress();
        $this->priceDetails = new PriceDetails();
        $this->productOriginCountry = new ProductOriginCountry();
        $this->quantity = new Quantity();
        $this->referenceDocument = new ReferenceDocument();
        $this->tax = new Tax();
        $this->freeText = new FreeText();
        $this->percentageDetails = new PercentageDetails();
    }

    public const SEG_DATETIME = "DTM";
    public const SEG_FREETEXT = "FTX";
    public const SEG_REFERENCE = "RFF";
    public const SEG_NAME_ADDRESS = "NAD";
    public const SEG_FINANCIAL_INFO = "FII";
    public const SEG_CONTACT = "CTA";
    public const SEG_COMMUNICATION = "COM";
    public const SEG_LINE_ITEM = "LIN";
    public const SEG_ITEM_DESC = "IMD";
    public const SEG_QUANTITY = "QTY";
    public const SEG_PRICE_DETAILS = "PRI";
    public const SEG_MONETARY = "MOA";
    public const SEG_TAX = "TAX";
    public const SEG_CURRENCY = "CUX";
    public const SEG_PAYMENT_TERMS_BASIS = "PAT";
    public const SEG_PAYMENT_INSTRUCTIONS = "PAI";
    public const SEG_ALLOWANCE = "ALC";
    public const SEG_ADDITIONAL_PRODUCT_ID = "PIA";
    public const SEG_PRODUCT_ORIGIN_COUNTRY = "ALI";
    public const SEG_PERCENTAGE_DETAILS = "PCD";
    public const SEG_SECTION_CONTROL = "UNS";
    public const SEG_TRAILER = "UNT";
    public const SEG_CONTROL_TOTAL = "CNT";

    public function getSegmentTypeInfo($segment, $parentContext = null): array
    {
        $result = [];
        $context = $parentContext;

        $segmentName = $segment->getName();

        if ($segmentName === "G_SG26") {
            return $this->processLineItemGroup($segment, $context);
        } elseif ($segmentName === "G_SG16") {
            return $this->processAllowanceOrChargeData($segment, $context);
        } elseif ($segmentName === "G_SG8") {
            return $this->processPaymentTerms($segment);
        } elseif ($segmentName === "G_SG52") {
            return $this->processTaxBreakdown($segment);
        }

        foreach ($segment->children() as $child) {
            $segmentType = substr($child->getName(), 2);

            if (str_starts_with($segmentType, "SG")) {
                if ($segmentType === "SG5") {
                    $contactData = $this->contact->getContactInfo($child);
                    if ($contactData !== null && is_array($contactData)) {
                        if ($context) {
                            foreach ($contactData as $key => $value) {
                                $contextKey = $context . '_' . $key;
                                $result[$contextKey] = $value;
                            }
                        } else {
                            foreach ($contactData as $key => $value) {
                                $result[$key] = $value;
                            }
                        }
                    }
                    continue;
                }
                $nestedData = $this->getSegmentTypeInfo($child, $context);
                if (!empty($nestedData) && is_array($nestedData)) {
                    foreach ($nestedData as $key => $value) {
                        if ($key === 'allowance' || $key === 'charge') {
                            if (!isset($result[$key])) {
                                $result[$key] = [];
                            }
                            $result[$key][] = $value;
                        } elseif (isset($result[$key]) && is_array($result[$key]) && is_array($value)) {
                            $result[$key] = array_merge($result[$key], $value);
                        } else {
                            $result[$key] = $value;
                        }
                    }
                }
                continue;
            }

            if ($segmentType === self::SEG_NAME_ADDRESS) {
                $type = (string) $child->D_3035;
                $context = match ($type) {
                    'BY' => 'buyer',
                    'SE' => 'seller',
                    'PE' => 'payee',
                    'DP' => 'delivery',
                    'LC' => 'tax_declaration',
                    default => null,
                };
            }

            $data = $this->getSegmentData($segmentType, $child, $context);

            if ($data !== null && is_array($data)) {
                if ($segmentType === self::SEG_FINANCIAL_INFO) {
                    $result = array_merge($result, $data);
                } else {
                    $result = array_merge($result, $data);
                }
            }
        }

        return $result;
    }

    private function processLineItemGroup($segment, $context = null): array
    {
        $lineItemData = [];
        $allowanceOrChargeData = [];
        $lineItemId = null;
        $lineAdditionalTxt = [];

        foreach ($segment->children() as $child) {
            $childName = $child->getName();

            if ($childName === "G_SG39") {
                $allowanceOrChargeData[] = $this->processAllowanceOrChargeData($child);
                continue;
            }

            if (str_starts_with($childName, "G_SG")) {
                $nestedData = $this->getSegmentTypeInfo($child, $context);

                if (!empty($nestedData) && is_array($nestedData)) {
                    foreach ($nestedData as $key => $value) {
                        if (isset($lineItemData[$key]) && is_array($lineItemData[$key]) && is_array($value)) {
                            $lineItemData[$key] = array_merge($lineItemData[$key], $value);
                        } else {
                            $lineItemData[$key] = $value;
                        }
                    }
                }
            } else {
                $segmentType = substr($childName, 2);
                $data = $this->getSegmentData($segmentType, $child, $context);

                if ($data !== null && is_array($data)) {
                    if ($segmentType === self::SEG_LINE_ITEM && isset($data['line_item_id'])) {
                        $lineItemId = $data['line_item_id'];
                    } elseif ($segmentType === self::SEG_FREETEXT && isset($data['additional_product_attribute_information'])) {
                        $lineAdditionalTxt[] = $data['additional_product_attribute_information'];
                    }
                    $lineItemData = array_merge($lineItemData, $data, $lineAdditionalTxt);
                }
            }
        }

        if (!empty($allowanceOrChargeData)) {
            $allowances = [];
            $charges = [];

            foreach ($allowanceOrChargeData as $item) {
                if (isset($item['allowance'])) {
                    $allowances[] = $item['allowance'];
                } elseif (isset($item['charge'])) {
                    $charges[] = $item['charge'];
                }
            }

            if (!empty($allowances)) {
                $lineItemData['allowance'] = $allowances;
            }

            if (!empty($charges)) {
                $lineItemData['charge'] = $charges;
            }
        }
        if ($lineItemId !== null) {
            return [$lineItemId => $lineItemData];
        }

        return $lineItemData;
    }
    private function processAllowanceOrChargeData($segment, $context = null): array
    {
        $allData = [];
        $type = null;

        foreach ($segment->children() as $child) {
            $childName = $child->getName();

            if (str_starts_with($childName, "G_SG")) {
                $nestedData = $this->getSegmentTypeInfo($child, $context);

                if (!empty($nestedData) && is_array($nestedData)) {
                    foreach ($nestedData as $key => $value) {
                        if (isset($allData[$key]) && is_array($allData[$key]) && is_array($value)) {
                            $allData[$key] = array_merge($allData[$key], $value);
                        } else {
                            $allData[$key] = $value;
                        }
                    }
                }
            } else {
                $segmentType = substr($childName, 2);

                if ($segmentType === self::SEG_ALLOWANCE) {
                    $alcType = (string) $child->D_5463;
                    $type = ($alcType === 'A') ? 'allowance' : 'charge';
                }

                $segmentData = $this->getSegmentData($segmentType, $child, $context);

                if ($segmentData !== null && is_array($segmentData)) {
                    $allData = array_merge($allData, $segmentData);
                }
            }
        }
        if ($type !== null) {
            return [$type => $allData];
        }
        return $allData;
    }
    private function processPaymentTerms($segment): array
    {
        $data = [];
        foreach ($segment->children() as $segmentChild) {
            $segmentType = substr($segmentChild->getName(), 2);
            if ($segmentType === "PAT") {
                $paymentTermsQualifierCode = $segmentChild->D_4279 == 1 ? "basic" : (string) $segmentChild->D_4279;
                $data['payment_terms_qualifier_type_code'] = $paymentTermsQualifierCode;
            } elseif ($segmentType === "PAI") {
                $paymentMeansCode = (string) $segmentChild->C_C534->D_4461;
                $data['payment_instructions'] = $paymentMeansCode;
            } elseif ($segmentType === "DTM") {
                $dtmData = $this->getSegmentData($segmentType, $segmentChild);
                $data[key($dtmData)] = current($dtmData);
            }
        }
        return ["payment_terms" => $data];
    }
    private function processTaxBreakdown($segment): array
    {
        $taxData = [];
        foreach ($segment->children() as $segmentChild) {
            $segmentType = substr($segmentChild->getName(), 2);
            $data = $this->getSegmentData($segmentType, $segmentChild);
            if ($data !== null && is_array($data)) {
                $taxData = array_merge($taxData, $data);
            }
        }
        return ['tax_breakdown' => $taxData];
    }

    private function getSegmentData($segmentType, $child, $context = null)
    {
        $data = match ($segmentType) {
            self::SEG_DATETIME => [$this->dateTimePeriod->getDocumentDateType($child) => $this->dateTimePeriod->getDocumentDate($child)],
            self::SEG_FREETEXT => $this->freeText->getAdditionalProductInformation($child),
            self::SEG_REFERENCE => $this->referenceDocument->getReferenceDocumentInfo([$child]),
            self::SEG_NAME_ADDRESS => $this->nameAddress->getDocumentNameAddress($child),
            self::SEG_FINANCIAL_INFO => $this->financialInfo->getFinancialInfo($child),
            self::SEG_CONTACT => $this->contact->getContactInfo($child),
            self::SEG_CURRENCY => $this->currency->getCurrency($child),
            self::SEG_ALLOWANCE => $this->allowance->getAllowanceData($child),
            self::SEG_LINE_ITEM => $this->lineItem->getLineItem($child),
            self::SEG_ADDITIONAL_PRODUCT_ID => $this->additionalProductID->getAdditionalProductID($child),
            self::SEG_ITEM_DESC => $this->itemDescription->getItemDescription($child),
            self::SEG_QUANTITY => $this->quantity->getQuantity($child),
            self::SEG_PRODUCT_ORIGIN_COUNTRY => $this->productOriginCountry->getProductOriginCountry($child),
            self::SEG_PRICE_DETAILS => $this->priceDetails->getPriceDetails($child),
            self::SEG_MONETARY => $this->monetaryAmount->getMonetaryAmount($child),
            self::SEG_TAX => $this->tax->getTaxInfo($child),
            self::SEG_PERCENTAGE_DETAILS => $this->percentageDetails->getPercentageDetails($child),
            default => null
        };

        if ($context && $segmentType === self::SEG_REFERENCE && $data !== null) {
            return [$context . '_references' => $data];
        }

        return $data;
    }
}
