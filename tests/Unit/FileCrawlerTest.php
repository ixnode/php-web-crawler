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

namespace Ixnode\PhpWebCrawler\Tests\Unit;

use Ixnode\PhpContainer\Json;
use Ixnode\PhpException\ArrayType\ArrayKeyNotFoundException;
use Ixnode\PhpException\Case\CaseInvalidException;
use Ixnode\PhpException\File\FileNotFoundException;
use Ixnode\PhpException\File\FileNotReadableException;
use Ixnode\PhpException\Function\FunctionJsonEncodeException;
use Ixnode\PhpException\Type\TypeInvalidException;
use Ixnode\PhpNamingConventions\Exception\FunctionReplaceException;
use Ixnode\PhpWebCrawler\Converter\DateParser;
use Ixnode\PhpWebCrawler\Converter\PregReplace;
use Ixnode\PhpWebCrawler\Converter\Sprintf;
use Ixnode\PhpWebCrawler\Converter\Trim;
use Ixnode\PhpWebCrawler\Output\Field;
use Ixnode\PhpWebCrawler\Output\Group;
use Ixnode\PhpWebCrawler\Source\File;
use Ixnode\PhpWebCrawler\Source\XpathSections;
use Ixnode\PhpWebCrawler\Value\Text;
use Ixnode\PhpWebCrawler\Value\XpathInnerHtml;
use Ixnode\PhpWebCrawler\Value\XpathOuterHtml;
use Ixnode\PhpWebCrawler\Value\XpathTextNode;
use Ixnode\PhpWebCrawler\Version;
use JsonException;
use PHPUnit\Framework\TestCase;

/**
 * Class FileCrawlerTest
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2024-02-24)
 * @since 0.1.0 (2024-02-24) First version.
 * @link File
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
final class FileCrawlerTest extends TestCase
{
    private const EXPECTED_2 = 2;
    private const EXPECTED_3 = 3;

    /**
     * Parses the given file with given field configuration.
     *
     * @param string $file
     * @param Field[]|Group[] $fields
     * @return Json
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws FunctionJsonEncodeException
     * @throws TypeInvalidException
     * @throws FunctionReplaceException
     * @throws JsonException
     */
    private function parseHtml(string $file, array $fields = []): Json
    {
        $parameter = array_merge(
            [
                $file,
                new Field('version', new Text(Version::VERSION)),
            ],
            $fields
        );

        return (new File(...$parameter))->parse();
    }

    /**
     * Returns the full file path from the given file name.
     *
     * @param string $name
     * @return string
     */
    private function getFile(string $name): string
    {
        return sprintf('%s/%s/%s', __DIR__, '/../../examples/html/', $name);
    }

    /**
     * Test "simple".
     *
     * @test
     * @testdox Test "simple".
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws FunctionJsonEncodeException
     * @throws FunctionReplaceException
     * @throws JsonException
     * @throws TypeInvalidException
     */
    public function simple(): void
    {
        /* Arrange */
        $file = $this->getFile('basic.html');
        $fields = [
            new Field('title', new XpathTextNode('//h1')),
            new Field('paragraph', new XpathTextNode('//p'))
        ];

        /* Act */
        $data = $this->parseHtml($file, $fields)->getArray();

        /* Assert */
        $this->assertIsArray($data);
        $this->assertArrayHasKey('version', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('paragraph', $data);
        $this->assertCount(self::EXPECTED_3, $data);
        $this->assertEquals(Version::VERSION, $data['version']);
        $this->assertEquals('Test Title', $data['title']);
        $this->assertEquals('Test Paragraph', $data['paragraph']);
    }

    /**
     * Test "wiki page".
     *
     * @test
     * @testdox Test "wiki page".
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws FunctionJsonEncodeException
     * @throws FunctionReplaceException
     * @throws JsonException
     * @throws TypeInvalidException
     */
    public function wikiPage(): void
    {
        /* Arrange */
        $file = $this->getFile('wiki-page.html');
        $fields = [
            new Field('title', new XpathTextNode('//*[@id="firstHeading"]/i')),
            new Field('directed_by', new XpathTextNode('//*[@id="mw-content-text"]/div/table[1]//tr[3]/td/a'))
        ];

        /* Act */
        $data = $this->parseHtml($file, $fields)->getArray();

        /* Assert */
        $this->assertIsArray($data);
        $this->assertArrayHasKey('version', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('directed_by', $data);
        $this->assertCount(self::EXPECTED_3, $data);
        $this->assertEquals(Version::VERSION, $data['version']);
        $this->assertEquals('Pirates of the Caribbean: The Curse of the Black Pearl', $data['title']);
        $this->assertEquals('Gore Verbinski', $data['directed_by']);
    }

    /**
     * Test "list".
     *
     * @test
     * @testdox Test "list".
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws FunctionJsonEncodeException
     * @throws FunctionReplaceException
     * @throws JsonException
     * @throws TypeInvalidException
     * @throws ArrayKeyNotFoundException
     * @throws CaseInvalidException
     */
    public function list(): void
    {
        /* Arrange */
        $domain = 'https://en.wikipedia.org';
        $file = $this->getFile('search.html');
        $fields = [
            new Field('title', new XpathTextNode('//*[@id="firstHeading"]')),
            new Group(
                'hits',
                new XpathSections(
                    '//*[@id="mw-content-text"]/div/ul/li',
                    new Field('title', new XpathTextNode('./div[1]/a')),
                    new Field('link', new XpathTextNode('./div[1]/a/@href', new Sprintf($domain.'%s')))
                )
            )
        ];
        $expectedFirstHit = [
            'title' => 'Pirates of the Caribbean (film series)',
            'link' => 'https://en.wikipedia.org/wiki/Pirates_of_the_Caribbean_(film_series)',
        ];

        /* Act */
        $parsed = $this->parseHtml($file, $fields);
        $data = $parsed->getArray();
        $firstHit = $parsed->getKeyArray(['hits', 0]);

        /* Assert */
        $this->assertIsArray($data);
        $this->assertArrayHasKey('version', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('hits', $data);
        $this->assertCount(self::EXPECTED_3, $data);
        $this->assertEquals(Version::VERSION, $data['version']);
        $this->assertEquals('Search results', $data['title']);
        $this->assertEquals($expectedFirstHit, $firstHit);
    }

    /**
     * Test "inner html".
     *
     * @test
     * @testdox Test "inner html".
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws FunctionJsonEncodeException
     * @throws FunctionReplaceException
     * @throws JsonException
     * @throws TypeInvalidException
     * @throws ArrayKeyNotFoundException
     * @throws CaseInvalidException
     */
    public function innerHtml(): void
    {
        /* Arrange */
        $file = $this->getFile('search.html');
        $fields = [
            new Field('innerhtml', new XpathInnerHtml('//*[@id="p-namespaces"]'))
        ];
        $expectedInnerHtml = '<h3 id="p-namespaces-label">Namespaces</h3><ul><li id="ca-nstab-special" class="selected"><span><a href="/w/index.php?title=Special:Search&amp;profile=advanced&amp;search=Pirates+of+the+Caribbean+movie&amp;searchToken=dcujdmhbs1frrdepz40b2ieg0" title="This is a special page which you cannot edit">Special page</a></span></li></ul>';

        /* Act */
        $parsed = $this->parseHtml($file, $fields);
        $data = $parsed->getArray();
        $innerHtml = $parsed->getKeyString(['innerhtml']);

        /* Assert */
        $this->assertIsArray($data);
        $this->assertArrayHasKey('version', $data);
        $this->assertCount(self::EXPECTED_2, $data);
        $this->assertEquals(Version::VERSION, $data['version']);
        $this->assertEquals($expectedInnerHtml, $innerHtml);
    }

    /**
     * Test "outer html".
     *
     * @test
     * @testdox Test "inner html".
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws FunctionJsonEncodeException
     * @throws FunctionReplaceException
     * @throws JsonException
     * @throws TypeInvalidException
     * @throws ArrayKeyNotFoundException
     * @throws CaseInvalidException
     */
    public function outerHtml(): void
    {
        /* Arrange */
        $file = $this->getFile('search.html');
        $fields = [
            new Field('outerhtml', new XpathOuterHtml('//*[@id="p-namespaces"]'))
        ];
        $expectedOuterHtml = '<div id="p-namespaces" role="navigation" class="vectorTabs" aria-labelledby="p-namespaces-label"><h3 id="p-namespaces-label">Namespaces</h3><ul><li id="ca-nstab-special" class="selected"><span><a href="/w/index.php?title=Special:Search&amp;profile=advanced&amp;search=Pirates+of+the+Caribbean+movie&amp;searchToken=dcujdmhbs1frrdepz40b2ieg0" title="This is a special page which you cannot edit">Special page</a></span></li></ul></div>';

        /* Act */
        $parsed = $this->parseHtml($file, $fields);
        $data = $parsed->getArray();
        $outerHtml = $parsed->getKeyString(['outerhtml']);

        /* Assert */
        $this->assertIsArray($data);
        $this->assertArrayHasKey('version', $data);
        $this->assertCount(self::EXPECTED_2, $data);
        $this->assertEquals(Version::VERSION, $data['version']);
        $this->assertEquals($expectedOuterHtml, $outerHtml);
    }

    /**
     * Test "converter".
     *
     * @test
     * @testdox Test "converter".
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     * @throws FunctionJsonEncodeException
     * @throws FunctionReplaceException
     * @throws JsonException
     * @throws TypeInvalidException
     */
    public function converter(): void
    {
        /* Arrange */
        $file = $this->getFile('converter.html');
        $fields = [
            new Field(
                'title',
                new XpathTextNode(
                    '//*[@id="title-overview-widget"]/div[2]/div[2]/div/div[2]/div[2]/h1',
                    new Trim()
                )
            ),
            new Field(
                'date',
                new XpathTextNode(
                    '//*[@id="title-overview-widget"]/div[2]/div[2]/div/div[2]/div[2]/div[2]/a[4]',
                    new Trim(),
                    new PregReplace('~ \([^\(]+\)~', ''),
                    new DateParser('d M Y H:i:s', '%s 12:00:00')
                )
            )
        ];

        /* Act */
        $parsed = $this->parseHtml($file, $fields);
        $data = $parsed->getArray();

        /* Assert */
        $this->assertIsArray($data);
        $this->assertArrayHasKey('version', $data);
        $this->assertCount(self::EXPECTED_3, $data);
        $this->assertEquals(Version::VERSION, $data['version']);
        $this->assertEquals('Pirates of the Caribbean: Salazars Rache (2017)', $data['title']);
        $this->assertEquals(1_495_713_600, $data['date']);
    }
}
