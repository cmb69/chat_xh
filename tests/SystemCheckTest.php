<?php

namespace Chat;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;

class SystemCheckTest extends TestCase
{
    public function setUp(): void
    {
        global $pth, $plugin_tx;
        $pth = ["folder" => ["content" => "../../content/", "plugins" => "../"]];
        $plugin_tx = XH_includeVar("./languages/en.php", "plugin_tx");
    }

    private function sut(): SystemCheck
    {
        return new SystemCheck();
    }

    public function testRendersSystemCheck(): void
    {
        $response = $this->sut()->render();
        Approvals::verifyHtml($response);
    }
}
