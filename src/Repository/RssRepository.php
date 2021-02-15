<?php
declare(strict_types=1);

/**
 * This file is part of the JobRouter Widget RSS Base library.
 *
 * Copyright (c) 2021 Chris Müller
 *
 * For the full copyright and license information, please view the
 * LICENSE.txt file that was distributed with this source code.
 * @see https://github.com/brotkrueml/jobrouter-widget-rss-base
 */

namespace Brotkrueml\JobRouterWidgetRssBase\Repository;

final class RssRepository
{
    /** @var int */
    private $itemsLimit;

    public function __construct(int $itemsLimit = 5)
    {
        $this->itemsLimit = $itemsLimit;
    }

    public function find(string $url): array
    {
        $rssContent = \file_get_contents($url);
        if ($rssContent === false) {
            throw new \RuntimeException('RSS URL could not be fetched', 1606043525);
        }

        $rssFeed = \simplexml_load_string($rssContent);

        return $this->generateRssItems($rssFeed);
    }

    private function generateRssItems(\SimpleXMLElement $rssFeed): array
    {
        $items = [];
        foreach ($rssFeed->channel->item as $item) {
            $items[] = [
                'title' => \trim((string)$item->title),
                'link' => (string)$item->link,
                'pubDate' => \trim((string)$item->pubDate),
                'description' => \trim(\str_replace('&nbsp;', '', \strip_tags((string)$item->description))),
                'originalDescription' => (string)$item->description,
            ];
        }
        \usort($items, static function (array $item1, array $item2): int {
            return new \DateTimeImmutable($item2['pubDate']) <=> new \DateTimeImmutable($item1['pubDate']);
        });
        $items = \array_slice($items, 0, $this->itemsLimit);

        foreach ($items as &$item) {
            $item['image'] = $this->getImage($item['originalDescription']);
            unset($item['originalDescription']);
        }

        return $items;
    }

    private function getImage($description): ?array
    {
        if (!\preg_match('/<img src="(.*?)".*alt="(.*?)"/s', $description, $matches)) {
            return null;
        }

        return [
            'src' => $matches[1],
            'alt' => $matches[2],
        ];
    }
}
