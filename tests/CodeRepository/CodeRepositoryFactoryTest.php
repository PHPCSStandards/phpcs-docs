<?php
declare(strict_types=1);

namespace App\Tests\CodeRepository;

use App\CodeRepository\CodeRepositoryFactory;
use App\CodeRepository\GitCodeRepository;
use App\CodeRepository\LocalCodeRepository;
use App\Configuration\Value\Source;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** @covers \App\CodeRepository\CodeRepositoryFactory */
class CodeRepositoryFactoryTest extends TestCase
{
    /** @test */
    public function fromType_WithInvalid_ThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        (new CodeRepositoryFactory)->fromType('INVALID');
    }

    /** @test */
    public function fromType_WithGit_ReturnGitImplementation()
    {
        self::assertInstanceOf(
            GitCodeRepository::class,
            (new CodeRepositoryFactory)->fromType(Source::TYPE_GIT)
        );
    }

    /** @test */
    public function fromType_WithLocal_ReturnLocalImplementation()
    {
        self::assertInstanceOf(
            LocalCodeRepository::class,
            (new CodeRepositoryFactory)->fromType(Source::TYPE_LOCAL)
        );
    }
}
