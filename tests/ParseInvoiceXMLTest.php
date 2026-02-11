<?php

namespace Media24si\eSlog2_reader\Tests;

use Media24si\eSlog2_reader\ParseInvoiceXML;
use PHPUnit\Framework\TestCase;

class ParseInvoiceXMLTest extends TestCase
{
    private ParseInvoiceXML $parser;
    private string $sampleXmlPath;

    protected function setUp(): void
    {
        $this->parser = new ParseInvoiceXML();
        $this->sampleXmlPath = __DIR__ . '/fixtures/sample_eSLOG20_INVOIC_v200_with_BT.xml';
    }

    public function test_can_instantiate_parser(): void
    {
        $this->assertInstanceOf(ParseInvoiceXML::class, $this->parser);
    }

    public function test_throws_exception_for_non_existent_file(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File not found');

        $this->parser->getAllData('non_existent_file.xml');
    }

    public function test_can_parse_sample_invoice(): void
    {
        if (!file_exists($this->sampleXmlPath)) {
            $this->markTestSkipped('Sample XML file not found at: ' . $this->sampleXmlPath);
        }

        $result = $this->parser->getAllData($this->sampleXmlPath);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    public function test_parsed_invoice_has_required_fields(): void
    {
        if (!file_exists($this->sampleXmlPath)) {
            $this->markTestSkipped('Sample XML file not found at: ' . $this->sampleXmlPath);
        }

        $result = $this->parser->getAllData($this->sampleXmlPath);

        $this->assertArrayHasKey('document_type', $result);
        $this->assertArrayHasKey('document_reference_number', $result);
        $this->assertArrayHasKey('document_identifier', $result);
    }

    public function test_can_get_specific_data(): void //This could possibly fail if you setup to retrive different data
    {
        if (!file_exists($this->sampleXmlPath)) {
            $this->markTestSkipped('Sample XML file not found at: ' . $this->sampleXmlPath);
        }

        $result = $this->parser->getSpecificData($this->sampleXmlPath);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('document_type', $result);
        $this->assertArrayHasKey('document_number', $result);
        $this->assertArrayHasKey('document_date', $result);
        $this->assertArrayHasKey('payment_due_date', $result);
        $this->assertArrayHasKey('delivery_date', $result);
        $this->assertArrayHasKey('tax_point_date', $result);
        $this->assertArrayHasKey('total_amount_without_vat', $result);
        $this->assertArrayHasKey('total_amount_with_vat', $result);
        $this->assertArrayHasKey('buyer_name', $result);
        $this->assertArrayHasKey('payment_type', $result);
        $this->assertArrayHasKey('payment_model', $result);
        $this->assertArrayHasKey('payment_reference_number', $result);
        $this->assertArrayHasKey('reference_currency', $result);
    }

    public function test_document_type_constants_are_defined(): void
    {
        $this->assertEquals(1, ParseInvoiceXML::FUNCTION_CANCELLATION);
        $this->assertEquals(5, ParseInvoiceXML::FUNCTION_REPLACE);
        $this->assertEquals(7, ParseInvoiceXML::FUNCTION_DUPLICATE);
        $this->assertEquals(9, ParseInvoiceXML::FUNCTION_ORIGINAL);
        $this->assertEquals(31, ParseInvoiceXML::FUNCTION_COPY);
        $this->assertEquals(43, ParseInvoiceXML::FUNCTION_ADDITIONAL_TRANSMISSION);
    }

    public function test_payment_constants_are_defined(): void
    {
        $this->assertEquals(0, ParseInvoiceXML::PAYMENT_REQUIRED);
        $this->assertEquals(1, ParseInvoiceXML::PAYMENT_DIRECT_DEBIT);
        $this->assertEquals(2, ParseInvoiceXML::PAYMENT_ALREADY_PAID);
        $this->assertEquals(3, ParseInvoiceXML::PAYMENT_OTHER_NO_PAYMENT);
    }

    public function test_location_constants_are_defined(): void
    {
        $this->assertEquals(57, ParseInvoiceXML::LOCATION_PAYMENT);
        $this->assertEquals(91, ParseInvoiceXML::LOCATION_ISSUED);
        $this->assertEquals(162, ParseInvoiceXML::LOCATION_SALE);
    }
}
