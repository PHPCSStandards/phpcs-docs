<?php
declare(strict_types=1);

namespace App\CodeRepository;

use App\Configuration\Value\Source;
use InvalidArgumentException;

class CodeRepositoryFactory
{
    public function fromType(string $type): CodeRepository
    {
        switch ($type) {
            case Source::TYPE_GIT:
                return new GitCodeRepository();
            case Source::TYPE_LOCAL:
                return new LocalCodeRepository();
            default:
                throw new InvalidArgumentException('Invalid type: ' . $type);
        }
    }
}
