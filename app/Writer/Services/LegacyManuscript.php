<?php

namespace App\Writer\Services;

use DOMDocument;
use DOMElement;
use DOMNode;
use League\CommonMark\CommonMarkConverter;

/** Convert legacy Markdown/HTML into the editor's supported block format. */
class LegacyManuscript
{
    public static function convert(string $source, bool $markdown = true): array
    {
        $html = $markdown ? (string) (new CommonMarkConverter(['html_input' => 'allow', 'allow_unsafe_links' => false]))->convert($source) : $source;
        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        try {
            $dom->loadHTML('<?xml encoding="UTF-8"><html><body>'.$html.'</body></html>', LIBXML_NONET);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
        $blocks = [];
        $pending = [];
        $text = fn (string $value, array $marks = []) => $value === '' ? [] : [array_filter(['type' => 'text', 'text' => $value, 'marks' => $marks], fn ($v) => $v !== [])];
        $inline = function (DOMNode $node, array $marks = []) use (&$inline, $text): array {
            if ($node->nodeType === XML_TEXT_NODE) {
                return $text($node->nodeValue, $marks);
            }
            if (! $node instanceof DOMElement) {
                return [];
            }
            $tag = strtolower($node->tagName);
            if (in_array($tag, ['script', 'style', 'iframe', 'object'])) {
                return [];
            }
            if ($tag === 'br') {
                return [['type' => 'hard_break']];
            }
            if ($tag === 'img') {
                return $text('['.($node->getAttribute('alt') ?: 'Image').'] ('.$node->getAttribute('src').')', $marks);
            }
            $mark = ['b' => 'strong', 'strong' => 'strong', 'i' => 'em', 'em' => 'em', 'code' => 'code'][$tag] ?? null;
            if ($mark && ! in_array(['type' => $mark], $marks)) {
                $marks[] = ['type' => $mark];
            }
            $out = [];
            foreach ($node->childNodes as $child) {
                $out = [...$out, ...$inline($child, $marks)];
            }
            if ($tag === 'a' && $node->getAttribute('href') !== $node->textContent) {
                $out = [...$out, ...$text(' ('.$node->getAttribute('href').')', $marks)];
            }

            return $out;
        };
        $flush = function () use (&$pending, &$blocks) {
            if ($pending) {
                $blocks[] = ['type' => 'paragraph', 'content' => $pending];
                $pending = [];
            }
        };
        $walk = function (DOMNode $node) use (&$walk, &$blocks, &$pending, $inline, $flush, $text) {
            $tag = $node instanceof DOMElement ? strtolower($node->tagName) : '';
            if (in_array($tag, ['script', 'style', 'iframe', 'object'])) {
                return;
            }
            if (in_array($tag, ['div', 'section', 'article', 'blockquote', 'ul', 'ol', 'table', 'tbody', 'thead', 'tfoot'])) {
                $flush();
                foreach ($node->childNodes as $child) {
                    $walk($child);
                } $flush();

                return;
            }
            if (in_array($tag, ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'li', 'pre', 'tr', 'hr'])) {
                $flush();
                if ($tag === 'hr') {
                    $blocks[] = ['type' => 'horizontal_rule'];

                    return;
                }
                $content = [];
                if ($tag === 'li') {
                    $content = $text('• ');
                }
                foreach ($node->childNodes as $child) {
                    if ($child instanceof DOMElement && in_array(strtolower($child->tagName), ['ul', 'ol'])) {
                        continue;
                    } $content = [...$content, ...$inline($child)];
                    if ($tag === 'tr') {
                        $content = [...$content, ...$text(' | ')];
                    }
                }
                $heading = preg_match('/^h[1-6]$/', $tag);
                $block = ['type' => $heading ? 'heading' : 'paragraph', 'content' => $content];
                if ($heading) {
                    $block['attrs'] = ['level' => $tag === 'h1' ? 1 : 2];
                }
                $blocks[] = $block;
                if ($tag === 'li') {
                    foreach ($node->childNodes as $child) {
                        if ($child instanceof DOMElement && in_array(strtolower($child->tagName), ['ul', 'ol'])) {
                            $walk($child);
                        }
                    }
                }

                return;
            }
            if ($node->nodeType === XML_TEXT_NODE && trim($node->nodeValue) === '') {
                return;
            }
            $pending = [...$pending, ...$inline($node)];
        };
        foreach ($dom->getElementsByTagName('body')->item(0)->childNodes as $node) {
            $walk($node);
        }
        $flush();
        $document = ['type' => 'doc', 'content' => $blocks ?: [['type' => 'paragraph', 'content' => []]]];
        Manuscript::validate($document);

        return $document;
    }
}
