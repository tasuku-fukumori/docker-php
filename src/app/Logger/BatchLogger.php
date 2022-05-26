<?php

namespace App\Logger;

use Log;

class BatchLogger
{
    public function __construct()
    {
        Log::setDefaultDriver('console');   
    }

    public function info($info)
    {
        Log::info($info);
    }

    public function error($info)
    {
        Log::error($info);
    }
}
