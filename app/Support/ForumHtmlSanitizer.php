<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class ForumHtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'blockquote',
        'pre', 'code', 'ol', 'ul', 'li', 'a', 'h2', 'h3', 'span',
    ];

    public function clean(string $html): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8" ?><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $root = $document->getElementsByTagName('div')->item(0);
        if (! $root) {
            return '';
        }

        $this->sanitizeChildren($root);

        $result = '';
        foreach ($root->childNodes as $child) {
            $result .= $document->saveHTML($child);
        }

        return trim($result);
    }

    private function sanitizeChildren(DOMNode $parent): void
    {
        foreach (iterator_to_array($parent->childNodes) as $node) {
            if ($node->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            /** @var DOMElement $node */
            if (! in_array(strtolower($node->tagName), self::ALLOWED_TAGS, true)) {
                while ($node->firstChild) {
                    $parent->insertBefore($node->firstChild, $node);
                }
                $parent->removeChild($node);
                continue;
            }

            foreach (iterator_to_array($node->attributes ?? []) as $attribute) {
                $name = strtolower($attribute->name);
                $allowed = $node->tagName === 'a' && in_array($name, ['href', 'target', 'rel'], true);
                if (! $allowed) {
                    $node->removeAttribute($attribute->name);
                }
            }

            if ($node->tagName === 'a') {
                $href = trim($node->getAttribute('href'));
                if (! preg_match('/^(https?:\/\/|mailto:|\/|#)/i', $href)) {
                    $node->removeAttribute('href');
                }
                $node->setAttribute('rel', 'nofollow noopener noreferrer');
                $node->setAttribute('target', '_blank');
            }

            $this->sanitizeChildren($node);
        }
    }
}
