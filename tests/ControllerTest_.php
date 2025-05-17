<?php

namespace Chat;

use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    /** @var Controller */
    protected $subject;

    /** @var object */
    protected $rspmiMock;

    public function setUp(): void
    {
        $this->defineConstant('XH_ADM', true);
        $this->subject = new Controller();
        $this->rspmiMock = new PHPUnit_Extensions_MockFunction(
            'XH_registerStandardPluginMenuItems',
            $this->subject
        );
    }

    public function testDispatchRegistersPluginMenuItems(): void
    {
        $this->rspmiMock->expects($this->once())->with(false);
        $this->subject->dispatch();
    }

    protected function defineConstant(string $name, string $value): void
    {
        if (!defined($name)) {
            define($name, $value);
        } else {
            runkit_constant_redefine($name, $value);
        }
    }
}
