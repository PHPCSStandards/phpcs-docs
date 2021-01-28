<?php
declare(strict_types=1);

use App\Configuration\ConfigurationRepository;
use App\Configuration\XmlConfigurationRepository;
use App\Generator\Generator as DocGenerator;
use App\Generator\MarkdownGenerator;
use App\SniffFinder\FilesystemSniffFinder;
use App\SniffFinder\SniffFinder;
use App\Value\Folder;

return [
    DocGenerator::class => DI\autowire(MarkdownGenerator::class),
    SniffFinder::class => DI\autowire(FilesystemSniffFinder::class),
    ConfigurationRepository::class => DI\factory(function () {
        return new XmlConfigurationRepository(new Folder(__DIR__ . '/../'));
    })
];
