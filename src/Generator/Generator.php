<?php
declare(strict_types=1);

namespace App\Generator;

use App\Value\UserDoc;

interface Generator
{
    public function createUserDoc(UserDoc $doc): string;
}
