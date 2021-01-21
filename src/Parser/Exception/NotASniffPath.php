<?php
declare(strict_types=1);

namespace App\Parser\Exception;

use DomainException;
use Throwable;

class NotASniffPath extends DomainException
{
    private function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromPath(string $path): self
    {
        return new self(
            <<<MSG
            The file path provided does not follow the convention for a sniff class.
            Must contain {Standard}/Sniffs/{Category}/{SniffName}Sniff.php
            Received: $path
            MSG
        );
    }
}
