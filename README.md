# eSlog 2.0 Reader

A PHP library for reading and parsing eSlog 2.0 XML invoice files.

## Installation

Install via Composer:

```bash
composer require media24si/eslog2_reader
```

## Requirements

- PHP >= 8.1
- ext-simplexml


## Usage

```php
use Media24si\eSlog2_reader\ParseInvoiceXML;

// Parse an eSlog 2.0 invoice XML file
$parser = new ParseInvoiceXML();
$invoice = $parser->getAllData('path/to/invoice.xml'); //To get all invoice data 
$invoice = $parser->getSpecificData('path/to/invoice.xml'); //Get specific data at your request (must set it up)

// Access invoice data
// ...
```

## Features

- Parse eSlog 2.0 XML invoice format
- Extract line items, tax information, and financial details
- Support for various segment types (dates, amounts, taxes, etc.)

## Testing

Run the test suite:

```bash
composer test
```

Or run PHPUnit directly:

```bash
./vendor/bin/phpunit
```

## License

MIT License. See [LICENSE](LICENSE) for more information.

## Author

Mark Hafner
