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
use Ixnode\PhpWebCrawler\Source\XpathSection;
use Ixnode\PhpWebCrawler\Value\LastUrl;
use Ixnode\PhpWebCrawler\Value\XpathTextNode;

$url = 'https://en.wikipedia.org/wiki/Pirates_of_the_Caribbean:_The_Curse_of_the_Black_Pearl';

$html = new Url(
    $url,
    new Field('title', new XpathTextNode('/html/body//*[@id="firstHeading"]')),
    new Field('last-url', new LastUrl()),
    new XpathSection(
        '/html/body//*[@id="mw-content-text"]/div[1]/table',
        new Group(
            'information',
            new Group(
                'person',
                new Field('directed_by', new XpathTextNode('./tbody/tr[th[contains(., \'Directed by\')]]/td')),
                new Field('produced_by', new XpathTextNode('./tbody/tr[th[contains(., \'Produced by\')]]/td')),
                new Field('based_on', new XpathTextNode('./tbody/tr[th[contains(., \'Based on\')]]/td'))
            )
        )
    )
);

try {
    echo $html->parse()->getJsonStringFormatted() . PHP_EOL;
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage().PHP_EOL;
}
