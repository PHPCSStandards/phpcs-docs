<?php
declare(strict_types=1);

use App\CodeRepository\CodeRepository;
use App\CodeRepository\GitCodeRepository;
use App\Configuration\ConfigurationRepository;
use App\Configuration\XmlConfigurationRepository;
use App\Generator\Generator as DocGenerator;
use App\Generator\MarkdownGenerator;
use App\SniffFinder\FilesystemSniffFinder;
use App\SniffFinder\SniffFinder;
use App\Value\Folder;

return [
    CodeRepository::class => DI\autowire(GitCodeRepository::class),
    DocGenerator::class => DI\autowire(MarkdownGenerator::class),
    SniffFinder::class => DI\autowire(FilesystemSniffFinder::class),
    ConfigurationRepository::class => DI\factory(function () {
        return new XmlConfigurationRepository(new Folder(__DIR__ . '/../'));
    })
];
