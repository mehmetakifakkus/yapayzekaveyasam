<?php

/**
 * Parse markdown text to HTML
 *
 * @param string $text The markdown text to parse
 * @return string The parsed HTML
 */
function parse_markdown(?string $text): string
{
    if (empty($text)) {
        return '';
    }

    $parsedown = new \Parsedown();
    $parsedown->setSafeMode(true); // XSS protection

    return $parsedown->text($text);
}
