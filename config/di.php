<?php
declare(strict_types=1);

use App\CodeRepository\CodeRepository;
use App\CodeRepository\GithubCodeRepository;
use App\Generator\Generator as DocGenerator;
use App\Generator\MarkdownGenerator;
use App\SniffFinder\FilesystemSniffFinder;
use App\SniffFinder\SniffFinder;

return [
    CodeRepository::class => DI\autowire(GithubCodeRepository::class),
    DocGenerator::class => DI\autowire(MarkdownGenerator::class),
    SniffFinder::class => DI\autowire(FilesystemSniffFinder::class),
];
