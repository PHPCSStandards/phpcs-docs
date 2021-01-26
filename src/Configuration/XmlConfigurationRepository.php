<?php
declare(strict_types=1);

namespace App\Configuration;

use App\Configuration\Value\Configuration;
use App\Configuration\Value\Source;
use App\Configuration\Value\Standard;
use App\Value\Folder;
use DOMDocument;
use RuntimeException;
use SimpleXMLElement;

class XmlConfigurationRepository implements ConfigurationRepository
{
    private Folder $root;

    public function __construct(Folder $root)
    {
        $this->root = $root;
    }

    public function getConfig(): Configuration
    {
        $paths = [$this->root . 'generator.xml', $this->root . '/generator.xml.dist'];
        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $this->parse($path);
            }
        }

        throw new RuntimeException(
            sprintf('Could not find a configuration file in any of these paths: %s', implode(',', $paths))
        );
    }

    private function parse(string $path): Configuration
    {
        $dom = new DOMDocument;
        $dom->load($path);

        set_error_handler(function(int $number, string $error) use ($path) {
            throw new RuntimeException(
                sprintf("The configuration file %s is invalid.\n%s", $path, $error)
            );
        });
        $dom->schemaValidate(__DIR__ . '/generator.xsd');
        restore_error_handler();

        $xml = new SimpleXMLElement(file_get_contents($path));

        return new Configuration(
            (string)$xml['format'],
            $this->getSources($xml),
        );
    }

    /**
     * @return Source[]
     */
    private function getSources(SimpleXMLElement $xml): array
    {
        return array_map(function (SimpleXMLElement $source): Source {
            return new Source(
                (string)$source['path'],
                $this->getStandards($source)
            );
        }, $xml->xpath('source'));
    }

    /**
     * @return Standard[]
     */
    private function getStandards(SimpleXMLElement $xml): array
    {
        return array_map(function (SimpleXMLElement $standard): Standard {
            return new Standard(
                (string)$standard['path']
            );
        }, $xml->xpath('standard'));
    }
}
