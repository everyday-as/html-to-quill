<?php

namespace Everyday\HtmlToQuill;

use DOMNode;
use Everyday\HtmlToQuill\Converters\NodeConverterInterface;
use Everyday\QuillDelta\Delta;
use Everyday\QuillDelta\DeltaOp;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\DomCrawler\Crawler;

class HtmlConverter implements HtmlConverterInterface
{
    /**
     * @var array<NodeConverterInterface>
     */
    protected array $converters;

    private array $tagConverterCache = [];

    #[Pure]
    public function __construct()
    {
        $this->converters = [
            new Converters\BreakConverter(),
            new Converters\CodeConverter(),
            new Converters\QuoteConverter(),
            new Converters\EmphasisConverter(),
            new Converters\HeaderConverter(),
            new Converters\ImageConverter(),
            new Converters\LinkConverter(),
            new Converters\ParagraphConverter(),
            new Converters\TextConverter(),
            new Converters\ListConverter(),
        ];
    }

    /**
     * Convert
     *
     * @param string $html
     *
     * @return Delta The Quill version of the html
     * @see HtmlConverter::convert
     */
    public function __invoke(string $html): Delta
    {
        return $this->convert($html);
    }

    /**
     * {@inheritdoc}
     */
    public function convert(string $html): Delta
    {
        if (empty(trim($html))) {
            return new Delta([]);
        }

        /** @var DeltaOp[] $ops */
        $ops = [];
        $crawler = new Crawler($html);

        if ($crawler->count() === 0) {
            $ops[] = DeltaOp::text($html);

            $delta = new Delta($ops);

            $delta->compact();

            return $delta;
        }

        foreach ($crawler->children() as $child) {
            $child->normalize();

            $ops = array_merge($ops, $this->convertChildren($child));
        }

        if (empty($ops)) {
            $ops[] = DeltaOp::text("\n");
        }

        if (!$ops[0]->isEmbed() && !$ops[0]->isBlockModifier()) {
            $ops[0]->setInsert(ltrim($ops[0]->getInsert()));
        }

        $delta = new Delta($ops);

        $delta->compact();

        return $delta;
    }

    /**
     * Convert to Quill
     *
     * @return DeltaOp[] The converted HTML as Quill DeltaOps
     */
    public function convertChildren(DOMNode $node): array
    {
        $ops = [];

        if (!$node->hasChildNodes()) {
            return $ops;
        }

        foreach ($node->childNodes as $childNode) {
            $converter = $this->getConverter($childNode->nodeName);

            if (null === $converter) {
                $ops = array_merge($ops, $this->convertChildren($childNode));

                continue;
            }

            $childNodeOps = $converter->convert($childNode, $this) ?? [];

            $ops = array_merge($ops, is_array($childNodeOps) ? $childNodeOps : [$childNodeOps]);
        }

        return $ops;
    }

    protected function getConverter(string $nodeName): ?NodeConverterInterface
    {
        if (isset($this->tagConverterCache[$nodeName])) {
            return $this->tagConverterCache[$nodeName];
        }

        foreach ($this->converters as $converter) {
            if (in_array($nodeName, $converter->getSupportedTags())) {
                return $this->tagConverterCache[$nodeName] =& $converter;
            }
        }

        return null;
    }
}
