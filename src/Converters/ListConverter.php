<?php

namespace Everyday\HtmlToQuill\Converters;

use DOMNode;
use Everyday\HtmlToQuill\HtmlConverterInterface;
use Everyday\QuillDelta\DeltaOp;

class ListConverter implements NodeConverterInterface
{
    public function convert(DOMNode $node, HtmlConverterInterface $htmlConverter): array
    {
        $attribute = "\t";
        $ops = $htmlConverter->convertChildren($node);
        if ($node->nodeName == "li") {
            if ($node->parentNode->nodeName == "ul") {
                $attribute = "bullet";
            }
            if ($node->parentNode->nodeName == "ol") {
                $attribute = "ordered";
            }
        }

        $modifier = DeltaOp::text("\n");
        $ops[] = $modifier->setAttribute("list", $attribute);

        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMNode) {
                if ($child->nodeName == "li") {
                    $ops[] = $modifier->setAttribute("list", $attribute);
                }
            }
        }
        if ($modifier->isBlockModifier()) {
            $ops[] = $modifier;
        }
        
        return $ops;
    }

    /**
     * @return array<string>
     */
    public function getSupportedTags(): array
    {
        return ['ul', 'ol', 'li'];
    }
}
