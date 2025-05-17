<?php

/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Chat_XH.
 *
 * Chat_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Chat_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Chat_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Chat;

use Plib\SystemChecker;
use Plib\View;

class InfoCommand
{
    /** @var SystemChecker */
    private $systemChecker;

    /** @var View */
    private $view;

    public function __construct(SystemChecker $systemChecker, View $view)
    {
        $this->systemChecker = $systemChecker;
        $this->view = $view;
    }

    public function render(): string
    {
        global $plugin_tx;

        $o = $this->view->render("about", [
            "version" => CHAT_VERSION,
        ]);
        $o .= '<h4>' . $plugin_tx['chat']['syscheck_title'] . '</h4>' . "\n"
            . $this->checkPHPVersion('7.1.0');
        foreach (array('pcre', 'session') as $ext) {
            $o .= $this->checkExtension($ext);
        }
        $o .= $this->checkXHVersion('1.7.0');
        foreach ($this->getWritableFolders() as $folder) {
            $o .= $this->checkWritability($folder);
        }
        return $o;
    }

    private function checkPHPVersion(string $version): string
    {
        $kind = $this->systemChecker->checkVersion(PHP_VERSION, $version) ? 'success' : 'fail';
        return $this->view->message($kind, "syscheck_phpversion", $version);
    }

    private function checkExtension(string $name): string
    {
        $kind = $this->systemChecker->checkExtension($name) ? 'success' : 'fail';
        return $this->view->message($kind, "syscheck_extension", $name);
    }

    private function checkXHVersion(string $version): string
    {
        $kind = $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH {$version}") ? 'success' : 'fail';
        return $this->view->message($kind, "syscheck_xhversion", $version);
    }

    private function checkWritability(string $filename): string
    {
        $kind = $this->systemChecker->checkWritability($filename) ? 'success' : 'warning';
        return $this->view->message($kind, "syscheck_writable", $filename);
    }

    private function getWritableFolders(): array
    {
        global $pth;

        $folders = array();
        foreach (array('config/', 'css/', 'languages/') as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'chat/' . $folder;
        }
        $folders[] = Room::dataFolder();
        return $folders;
    }
}
