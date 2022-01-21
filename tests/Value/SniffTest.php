<?phpdeclare(strict_types=1);namespace App\Tests\Value;use App\Value\Diff;use App\Value\Property;use App\Value\Sniff;use App\Value\Url;use App\Value\UrlList;use App\Value\Violation;use InvalidArgumentException;use PHPUnit\Framework\TestCase;/** @covers \App\Value\Sniff */class SniffTest extends TestCase{    /** @test */    public function constructor_WithBlankCode_ThrowException()    {        $this->expectException(InvalidArgumentException::class);        $this->createSniff()->withCode('');    }    /** @test */    public function getProperties()    {        $properties = [            new Property('name', 'int', 'description')        ];        self::assertEquals(            $properties,            $this->createSniff()->withProperties($properties)->getProperties()        );    }    /** @test */    public function getDocblock()    {        self::assertEquals(            'Docblock',            $this->createSniff()->withDocblock('Docblock')->getDocblock()        );    }    /** @test */    public function getDescription()    {        self::assertEquals(            'Description',            $this->createSniff()->withDescription('Description')->getDescription()        );    }    /** @test */    public function getViolations()    {        $violations = [            new Violation('code', '', [], new UrlList([]))        ];        self::assertEquals(            $violations,            $this->createSniff()->withViolations($violations)->getViolations()        );    }    /** @test */    public function getDiffs()    {        $diffs = [            new Diff('a();', 'b();')        ];        self::assertEquals(            $diffs,            $this->createSniff()->withDiffs($diffs)->getDiffs()        );    }    /** @test */    public function getUrls()    {        $urls = new UrlList([            new Url('https://link.com')        ]);        self::assertEquals(            $urls,            $this->createSniff()->withUrls($urls)->getUrls()        );    }    /** @test */    public function getCode()    {        self::assertEquals(            'Standard.Category.SniffName',            $this->createSniff()->withCode('Standard.Category.SniffName')->getCode()        );    }    /** @test */    public function getStandardName()    {        self::assertSame(            'Standard',            $this->createSniff()->withCode('Standard.Category.SniffName')->getStandardName()        );    }    /** @test */    public function getCategoryName()    {        self::assertSame(            'Category',            $this->createSniff()->withCode('Standard.Category.SniffName')->getCategoryName()        );    }    /** @test */    public function getSniffName()    {        self::assertSame(            'SniffName',            $this->createSniff()->withCode('Standard.Category.SniffName')->getSniffName()        );    }    private function createSniff(): Sniff    {        return new Sniff(            'Standard.Category.SniffName',            '',            [],            new UrlList([]),            '',            [],            []        );    }}