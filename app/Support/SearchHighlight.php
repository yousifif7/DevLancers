<?php

namespace App\Support;

class SearchHighlight
{
    public static function mark(?string $text, ?string $term): string
    {
        $escaped = e($text ?? '');

        if ($term === null || trim($term) === '' || $escaped === '') {
            return $escaped;
        }

        $pattern = '/' . preg_quote(trim($term), '/') . '/iu';

        return preg_replace($pattern, '<mark class="dl-search-mark">$0</mark>', $escaped) ?? $escaped;
    }
}
