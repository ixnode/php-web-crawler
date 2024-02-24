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
use Ixnode\PhpWebCrawler\Source\File;
use Ixnode\PhpWebCrawler\Value\Text;
use Ixnode\PhpWebCrawler\Value\XpathTextNode;

$file = dirname(__FILE__).'/html/basic.html';

$html = new File(
    $file,
    new Field('version', new Text('1.0.0')),
    new Field('title', new XpathTextNode('//h1')),
    new Field('paragraph', new XpathTextNode('//p'))
);

try {
    echo $html->parse()->getJsonStringFormatted().PHP_EOL;
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage().PHP_EOL;
}
