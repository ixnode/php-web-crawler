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

use Ixnode\PhpWebCrawler\Converter\Collection\Chunk;
use Ixnode\PhpWebCrawler\Converter\Scalar\Number;
use Ixnode\PhpWebCrawler\Output\Field;
use Ixnode\PhpWebCrawler\Source\Raw;
use Ixnode\PhpWebCrawler\Value\Text;
use Ixnode\PhpWebCrawler\Value\XpathTextNode;

$rawHtml = <<<HTML
<html>
    <head>
        <title>Test Runways</title>
    </head>
    <body>
        <h1>Test Runways</h1>
        
        <p>18L/36R</p>
        <p>8,677</p>
        <p>Asphalt/Concrete</p>
        
        <p>18C/36C</p>
        <p>10,000</p>
        <p>Concrete</p>
        
        <p>18R/36L</p>
        <p>9,000</p>
        <p>Concrete</p>
    </body>
</html>
HTML;

$html = new Raw(
    $rawHtml,
    new Field('version', new Text('1.0.0')),
    new Field('title', new XpathTextNode('//h1')),
    new Field('runways', new XpathTextNode('//p', new Chunk(
        chunkSize: 3,
        separator: ', ',
        arrayKeys: ['direction', 'length', 'surface'],
        scalarConverters: [null, new Number([',', '.'], ''), null])
    ))
);

try {
    echo $html->parse()->getJsonStringFormatted().PHP_EOL;
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage().PHP_EOL;
}
