<?php

namespace Tests;

use YusufTogtay\Generator\InfyOmGeneratorServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            InfyOmGeneratorServiceProvider::class,
        ];
    }
}
