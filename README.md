# html-to-quill

html-to-quill is a PHP library for converting an HTML string to Quill deltas.

## Installation

html-to-quill can be installed through Composer.
```bash
$ composer require everyday/html-to-quill
```

## Basic Usage

The `HtmlConverter` class provides a simple interface for converting from HTML to Quill Deltas:

```php
use Everyday\HtmlToQuill\HtmlConverter;

$converter = new HtmlConverter();
echo json_encode($converter->convert("<h1>Hello World!</h1>"));

// {"ops":[{"insert":"Hello World!"},{"insert":"\n","attributes":{"header":1}}]}
```
