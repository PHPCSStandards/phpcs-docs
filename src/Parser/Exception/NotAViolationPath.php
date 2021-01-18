<?php
declare(strict_types=1);

namespace App\Parser\Exception;

use DomainException;

class NotAViolationPath extends DomainException
{
    public static function fromPath(string $path): self
    {
        return new self(
            <<<MSG
            The file path provided does not follow the convention for violation documentation.
            Must contain {Standard}/Docs/{Category}/{SniffName}/{ErrorCode}.xml
            Received: $path
            MSG
        );
    }
}
