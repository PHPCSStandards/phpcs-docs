<?php
declare(strict_types=1);

namespace App\Configuration;

use App\Configuration\Value\Configuration;

interface ConfigurationRepository
{
    public function getConfig(): Configuration;
}
