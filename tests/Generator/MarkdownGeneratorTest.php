<?php
declare(strict_types=1);

namespace App\Tests\Generator;

use App\Generator\MarkdownGenerator;
use App\Value\Diff;
use App\Value\Url;
use App\Value\UserDoc;
use PHPUnit\Framework\TestCase;

/** @covers \App\Generator\MarkdownGenerator */
class MarkdownGeneratorTest extends TestCase
{
    private MarkdownGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new MarkdownGenerator();
    }

    /** @test */
    public function createUserDoc()
    {
        $doc = new UserDoc(
            'Rule.Code',
            'Description',
            [
                new Diff('a();', 'b();'),
                new Diff('a();', 'b();')
            ],
            [
                new Url('http://link1.com'),
                new Url('http://link2.com')
            ]
        );

        self::assertEquals(
            <<<MD
            # Rule.Code
            
            Description
            
            ## Comparisons
            
            ```diff
            -a();
            +b();
            ```
            
            ```diff
            -a();
            +b();
            ```
            
            ## See Also
            
            - [http://link1.com](http://link1.com)
            - [http://link2.com](http://link2.com)
            
            MD,
            $this->generator->createUserDoc($doc)
        );
    }
}
