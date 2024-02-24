# PHP Web Crawler

[![Release](https://img.shields.io/github/v/release/ixnode/php-web-crawler)](https://github.com/ixnode/php-web-crawler/releases)
[![](https://img.shields.io/github/release-date/ixnode/php-web-crawler)](https://github.com/ixnode/php-web-crawler/releases)
![](https://img.shields.io/github/repo-size/ixnode/php-web-crawler.svg)
[![PHP](https://img.shields.io/badge/PHP-^8.2-777bb3.svg?logo=php&logoColor=white&labelColor=555555&style=flat)](https://www.php.net/supported-versions.php)
[![PHPStan](https://img.shields.io/badge/PHPStan-Level%20Max-777bb3.svg?style=flat)](https://phpstan.org/user-guide/rule-levels)
[![PHPUnit](https://img.shields.io/badge/PHPUnit-Unit%20Tests-6b9bd2.svg?style=flat)](https://phpunit.de)
[![PHPCS](https://img.shields.io/badge/PHPCS-PSR12-416d4e.svg?style=flat)](https://www.php-fig.org/psr/psr-12/)
[![PHPMD](https://img.shields.io/badge/PHPMD-ALL-364a83.svg?style=flat)](https://github.com/phpmd/phpmd)
[![Rector - Instant Upgrades and Automated Refactoring](https://img.shields.io/badge/Rector-PHP%208.2-73a165.svg?style=flat)](https://github.com/rectorphp/rector)
[![LICENSE](https://img.shields.io/github/license/ixnode/php-api-version-bundle)](https://github.com/ixnode/php-api-version-bundle/blob/master/LICENSE)

> This PHP class allows you to crawl recursively a given html page (or a given html file) and collect some data from it.
> Simply define the url (or a html file) and a set of xpath expressions which should map with the output data object.
> The final representation will be a php array which can be easily converted into the json format for further
> processing.

## 1. Installation

```shell
composer require ixnode/php-web-crawler
```

```shell
vendor/bin/php-web-crawler -V
```

```shell
php-web-crawler 0.1.0 (02-24-2024 14:46:26) - Björn Hempel <bjoern@hempel.li>
```

## 2. Usage

### 2.1) Simple Raw HTML

```php
use Ixnode\PhpWebCrawler\Output\Field;
use Ixnode\PhpWebCrawler\Source\Raw;
use Ixnode\PhpWebCrawler\Value\Text;
use Ixnode\PhpWebCrawler\Value\XpathTextNode;

$rawHtml = <<<HTML
<html>
    <head>
        <title>Test Page</title>
    </head>
    <body>
        <h1>Test Title</h1>
        <p>Test Paragraph</p>
    </body>
</html>
HTML;

$html = new Raw(
    $rawHtml,
    new Field('version', new Text('1.0.0')),
    new Field('title', new XpathTextNode('//h1')),
    new Field('paragraph', new XpathTextNode('//p'))
);

$html->parse()->getJsonStringFormatted();
// See below
```

```json
{
    "version": "1.0.0",
    "title": "Test Title",
    "paragraph": "Test Paragraph"
}
```

### 2.2) More examples

* [examples/converter.php](examples/converter.php)
* [examples/group.php](examples/group.php)
* [examples/section.php](examples/section.php)
* [examples/sections-recursive-url.php](examples/sections-recursive-url.php)
* [examples/sections.php](examples/sections.php)
* [examples/simple-wiki-page.php](examples/simple-wiki-page.php)

## 3. Development

```bash
git clone git@github.com:ixnode/php-web-crawler.git && cd php-web-crawler
```

```bash
composer install
```

```bash
composer test
```

## 4. License

This library is licensed under the MIT License - see the [LICENSE.md](/LICENSE.md) file for details.
