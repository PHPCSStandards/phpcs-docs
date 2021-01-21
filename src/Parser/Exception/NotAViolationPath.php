<?php
declare(strict_types=1);

namespace App\Parser\Exception;

use DomainException;
use Throwable;

class NotAViolationPath extends DomainException
{
    private function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromPath(string $path): self
    {
        return new self(
            <<<MSG
            The file path provided does not follow the convention for violation documentation.
            Must contain {Standard}/Docs/{Category}/{SniffName}Standard/{ErrorCode}.xml
            Received: $path
            MSG
        );
    }
}
