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

## Supporting Custom Tag Behaviour

Custom tags can be supported through customer converters.
Converters take a DOMNode as an input, and return a DeltaOp.
The default converters can be found in src/Converters

Custom converters can be implemented to handle additional behaviour, which are then implemented into an extension of the HtmlConverter.

### Example: Parsing \<style> tags as code blocks.

To enable parsing style tags, two converters are required. One for the \<style> tag itself,
and one for the #cdata-section which represents the internal data.

For this example, namespaces will be omitted.

```php
class StyleConverter extends CodeConverter {
	public function getSupportedTags(): array {
		return ['style'];
	}
}
```

```php
class CDataConverter extends TextConverter {
	public function getSupportedTags(): array {
		return ['#cdata-section'];
	}
}
```

```php
class StyleCodeBlockEnabledHtmlConverter extends HtmlConverter {
	public function __construct(){
		parent::__construct(); // We want our default converters.
		$this->converters[] = new StyleConverter();
		$this->converters[] = new CDataConverter();
	}
}
```

These classes combine in the same style as the basic usage example, to produce our usable output:
```php
$converter = new StyleCodeBlockEnabledHtmlConverter();
echo json_encode($converter->convert("<style>.warning {background-color: red;}</style>"));

// {"ops":[{"insert":".warning {background-color: red;}","attributes":{"code":true}},{"insert":"\n"}]}
```
