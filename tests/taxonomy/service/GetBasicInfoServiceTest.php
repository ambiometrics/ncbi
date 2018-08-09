<?php
declare(strict_types=1);

namespace test\edwrodrig\ncbi\taxonomy\service;

use edwrodrig\ncbi\taxonomy\service\GetBasicInfoService;
use PHPUnit\Framework\TestCase;

class GetBasicInfoServiceTest extends TestCase
{
    public function testGetBasicInfo()
    {
        $service = new GetBasicInfoService([
            'taxids' => [1]
        ]);
        $service->setDataFolder(__DIR__ . '/../../files/database');
        $this->assertEquals(['name' => 'A', 'parent_id' => 1], $service->getBasicInfo(1));
        $this->assertEquals(['name' => 'B', 'parent_id' => 1], $service->getBasicInfo(2));
    }

    public function testProcess()
    {
        $service = new GetBasicInfoService([
            'taxids' => [1, 2]
        ]);
        $service->setDataFolder(__DIR__ . '/../../files/database');
        $this->assertEquals(['1' => ['name' => 'A', 'parent_id' => 1], 2 => ['name' => 'B', 'parent_id' => 1]], $service->process());
    }
}
