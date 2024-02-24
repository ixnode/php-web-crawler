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

use Ixnode\PhpWebCrawler\Output\Field;
use Ixnode\PhpWebCrawler\Output\Group;
use Ixnode\PhpWebCrawler\Source\Url;
use Ixnode\PhpWebCrawler\Value\XpathTextNode;

$url = 'https://en.wikipedia.org/wiki/Pirates_of_the_Caribbean:_The_Curse_of_the_Black_Pearl';

$html = new Url(
    $url,
    new Group(
        'group',
        new Field('title', new XpathTextNode('//*[@id="firstHeading"]/i')),
        new Field('directed_by', new XpathTextNode('//*[@id="mw-content-text"]/div/table[1]//tr[3]/td/a'))
    )
);

try {
    echo $html->parse()->getJsonStringFormatted() . PHP_EOL;
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage().PHP_EOL;
}
