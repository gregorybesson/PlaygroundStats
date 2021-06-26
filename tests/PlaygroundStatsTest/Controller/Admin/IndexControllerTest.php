<?php

namespace PlaygroundStatsTest\Controller\Admin;

use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use \PlaygroundGame\Entity\Game as GameEntity;

class IndexControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

    protected function setUp(): void
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../../TestConfig.php'
        );

        parent::setUp();
    }

    public function testIndexAction()
    {
        $this->assertTrue(true);
    }
}
