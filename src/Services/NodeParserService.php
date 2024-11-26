<?php

namespace YusufTogtay\Generator\Services;

class NodeParserService
{
    /**
     * Verilen node'lardan başlık ve key-value ilişkilerini objelere dönüştürür.
     *
     * @param array $nodeValues
     * @return array
     */
    public static function parseNodes(array $nodeValues): array
    {
        $parsedNodes = [];

        foreach ($nodeValues as $value) {
            $decodedValue = html_entity_decode($value);

            preg_match('/<b>(.*?)<\/b>/', $decodedValue, $titleMatch);
            $title = $titleMatch[1] ?? null;

            preg_match_all('/(\w+): ([^<]+)/', $decodedValue, $keyValueMatches, PREG_SET_ORDER);

            $keyValues = [];
            foreach ($keyValueMatches as $match) {
                $keyValues[$match[1]] = $match[2];
            }

            $parsedNodes[] = [
                'title' => $title,
                'attributes' => $keyValues,
            ];
        }

        return $parsedNodes;
    }
}
