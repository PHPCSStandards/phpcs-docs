<?php
declare(strict_types=1);

namespace App\Tests\Configuration;

use App\Configuration\Value\Configuration;
use App\Configuration\Value\Source;
use App\Configuration\Value\Standard;
use App\Configuration\XmlConfigurationRepository;
use App\Value\Folder;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;

/** @covers \App\Configuration\XmlConfigurationRepository */
class XmlConfigurationRepositoryTest extends TestCase
{
    const XML_FILE_PATH = 'var/tests/generator.xml';
    const XML_DIST_FILE_PATH = 'var/tests/generator.xml.dist';
    private XmlConfigurationRepository $repo;

    /** @test */
    public function getConfig_WithMissingFile_ThrowException()
    {
        $this->expectException(RuntimeException::class);
        $this->repo->getConfig();
    }

    /** @test */
    public function getConfig_WithInvalidSchema_ThrowException()
    {
        $this->expectException(RuntimeException::class);

        $xmlContent =
        '<?xml version="1.0" encoding="UTF-8"?>
        <generator>
        </generator>';

        (new Filesystem)->dumpFile(self::XML_FILE_PATH, $xmlContent);

        $this->repo->getConfig();
    }

    /** @test */
    public function getConfig_WithBothFiles_PickNonDist()
    {
        $xmlContent =
        '<?xml version="1.0" encoding="UTF-8"?>
        <generator format="markdown">
            <source path="../">
                <standard path="Xml" />
            </source>
        </generator>';

        (new Filesystem)->dumpFile(self::XML_FILE_PATH, $xmlContent);

        $xmlDistContent =
        '<generator format="markdown">
            <source path="../">
                <standard path="Dist" />
            </source>
        </generator>';

        (new Filesystem)->dumpFile(self::XML_DIST_FILE_PATH, $xmlDistContent);

        $config = $this->repo->getConfig();
        self::assertEquals(
            'Xml',
            $config->getSources()[0]->getStandards()[0]->getPath()
        );
    }

    /** @test */
    public function getConfig_WithValidFile_ReturnConfiguration()
    {
        $xmlContent =
        '<?xml version="1.0" encoding="UTF-8"?>
        <generator format="markdown">
            <source path="path/to/code">
                <standard path="Standard" />
            </source>
        </generator>';

        (new Filesystem)->dumpFile(self::XML_FILE_PATH, $xmlContent);

        self::assertEquals(
            new Configuration(
                'markdown',
                [
                    new Source('path/to/code', [
                        new Standard('Standard')
                    ]),
                ]
            ),
            $this->repo->getConfig()
        );
    }

    protected function setUp(): void
    {
        $fs = new Filesystem;
        $fs->remove(self::XML_DIST_FILE_PATH);
        $fs->remove(self::XML_FILE_PATH);
        $this->repo = new XmlConfigurationRepository(new Folder('var/tests/'));
    }
}
