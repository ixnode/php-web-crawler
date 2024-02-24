<?php

/*
 * This file is part of the ixnode/php-web-crawler project.
 *
 * (c) Björn Hempel <https://www.hempel.li/>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

declare(strict_types=1);

require dirname(__DIR__).'/vendor/autoload.php';

use Ixnode\PhpWebCrawler\Converter\Sprintf;
use Ixnode\PhpWebCrawler\Output\Field;
use Ixnode\PhpWebCrawler\Output\Group;
use Ixnode\PhpWebCrawler\Source\Url;
use Ixnode\PhpWebCrawler\Source\XpathSections;
use Ixnode\PhpWebCrawler\Value\XpathTextNode;

$domain = 'https://en.wikipedia.org';
$url = $domain.'/w/index.php?profile=advanced&search=Pirates+of+the+Caribbean+movie&title=Special:Search&ns0=1';

$html = new Url(
    $url,
    new Field('title', new XpathTextNode('/html/body//*[@id="firstHeading"]')),
    new Group(
        'hits',
        new XpathSections(
            '/html/body//*[@id="mw-content-text"]//div[contains(@class, \'mw-search-results-container\')]/ul/li',
            new Field('title', new XpathTextNode('./table/tr/td[2]/div[1]/a')),
            new Field('link', new XpathTextNode(
                './table/tr/td[2]/div[1]/a/@href',
                new Sprintf($domain.'%s')
            ))
        )
    )
);

try {
    echo $html->parse()->getJsonStringFormatted() . PHP_EOL;
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage().PHP_EOL;
}
