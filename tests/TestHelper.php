<?php

namespace Bojaghi\Helper\Tests;

use Bojaghi\Helper\Helper;

class TestHelper extends \WP_UnitTestCase
{
    public function test_loadConfig(): void
    {
        $path   = __DIR__ . '/test-config.php';
        $sample = ['key1' => 'value1', 'key2' => 'value2'];

        $this->assertEquals($sample, Helper::loadConfig($sample));
        $this->assertEquals($sample, Helper::loadConfig($path));

        unlink($path);
    }
}
