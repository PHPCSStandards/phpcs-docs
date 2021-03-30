<?php
declare(strict_types=1);

namespace App\Tests\App\Generator\Formatter;

use App\Generator\Formatter\MarkdownFormatter;
use App\Value\Diff;
use App\Value\Property;
use App\Value\Sniff;
use App\Value\Url;
use App\Value\UrlList;
use App\Value\Violation;
use PHPUnit\Framework\TestCase;

/** @covers \App\Generator\Formatter\MarkdownFormatter */
final class MarkdownFormatterTest extends TestCase
{
    private MarkdownFormatter $formatter;

    private function createSniff(): Sniff
    {
        return new Sniff(
            'Standard.Category.My',
            '',
            [],
            new UrlList([]),
            '',
            [],
            []
        );
    }

    /** @test */
    public function formatDescription(): void
    {
        self::assertEquals(
            <<<MD
            Description
            MD,
            $this->formatter->formatDescription(
                $this->createSniff()->withDescription('Description')
            )
        );
    }

    /** @test */
    public function formatDescription_WithBlankString_ReturnBlankString(): void
    {
        self::assertEquals(
            '',
            $this->formatter->formatDescription(
                $this->createSniff()->withDescription('')
            )
        );
    }

    /** @test */
    public function formatDocblock(): void
    {
        self::assertEquals(
            <<<MD
            ## Docblock
            
            Docblock
            MD,
            $this->formatter->formatDocblock(
                $this->createSniff()->withDocblock('Docblock')
            )
        );
    }

    /** @test */
    public function formatDocblock_WithBlankString_ReturnBlankString(): void
    {
        self::assertEquals(
            '',
            $this->formatter->formatDocblock(
                $this->createSniff()->withDocblock('')
            )
        );
    }

    /** @test */
    public function formatComparisons(): void
    {
        self::assertEquals(
            <<<MD
            ## Comparisons
            
            ```diff
            -a();
            +b();
            ```
            
            ```diff
            -a();
            +b();
            ```
            MD,
            $this->formatter->formatComparisons(
                $this->createSniff()->withDiffs([
                    new Diff('a();', 'b();'),
                    new Diff('a();', 'b();')
                ])->getDiffs()
            )
        );
    }

    /** @test */
    public function formatComparisons_WithEmptyList_ReturnBlankString(): void
    {
        self::assertEquals(
            '',
            $this->formatter->formatComparisons(
                $this->createSniff()->withDiffs([])->getDiffs()
            )
        );
    }

    /** @test */
    public function formatPublicProperties(): void
    {
        self::assertEquals(
            <<<MD
            ## Public Properties
            
            - `\$a` : string DescriptionA
            - `\$b` : int DescriptionB
            MD,
            $this->formatter->formatPublicProperties(
                $this->createSniff()->withProperties([
                    new Property('a', 'string', 'DescriptionA'),
                    new Property('b', 'int', 'DescriptionB')
                ])->getProperties()
            )
        );
    }

    /** @test */
    public function formatPublicProperties_WithEmptyList_ReturnBlankString(): void
    {
        self::assertEquals(
            '',
            $this->formatter->formatPublicProperties(
                $this->createSniff()->withProperties([])->getProperties()
            )
        );
    }

    /** @test */
    public function formatSeeAlso(): void
    {
        self::assertEquals(
            <<<MD
            ## See Also
            
            - [http://link1.com](http://link1.com)
            - [http://link2.com](http://link2.com)
            MD,
            $this->formatter->formatSeeAlso(
                $this->createSniff()->withUrls(new UrlList([
                    new Url('http://link1.com'),
                    new Url('http://link2.com')
                ]))->getUrls()
            )
        );
    }

    /** @test */
    public function formatSeeAlso_WithEmptyList_ReturnBlankString(): void
    {
        self::assertEquals(
            '',
            $this->formatter->formatSeeAlso(
                $this->createSniff()->withUrls(new UrlList([]))->getUrls()
            )
        );
    }

    /** @test */
    public function formatViolations(): void
    {
        self::assertEquals(
            <<<MD
            ## Troubleshooting
            
            ```
            <details>
            <summary>Standard.Category.My.ErrorCode</summary>
            Description
            
            ## Comparisons
            
            ```diff
            -a();
            +b();
            ```
            
            ## See Also
            
            - [http://link1.com](http://link1.com)
            </details>
            ```
            MD,
            $this->formatter->formatViolations(
                $this->createSniff()->withViolations([
                    new Violation(
                        'Standard.Category.My.ErrorCode',
                        'Description',
                        [
                            new Diff('a();', 'b();')
                        ],
                        new UrlList([
                            new Url('http://link1.com')
                        ])
                    )
                ])->getViolations()
            )
        );
    }

    /** @test */
    public function formatViolations_WithEmptyList_ReturnBlankString(): void
    {
        self::assertEquals(
            '',
            $this->formatter->formatViolations(
                $this->createSniff()->withViolations([])->getViolations()
            )
        );
    }

    protected function setUp(): void
    {
        $this->formatter = new MarkdownFormatter();
    }
}
