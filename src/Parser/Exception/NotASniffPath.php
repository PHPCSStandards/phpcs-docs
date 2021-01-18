<?php
declare(strict_types=1);

namespace App\Parser\Exception;

use DomainException;

class NotASniffPath extends DomainException
{
    public static function fromPath(string $path): self
    {
        return new self(
            <<<MSG
            The file path provided does not follow the convention for a sniff class.
            Must contain {Standard}/Sniffs/{Category}/{SniffName}.php
            Received: $path
            MSG
        );
    }
}
