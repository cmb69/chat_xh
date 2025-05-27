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

use Chat\Model\Room;
use Plib\DocumentStore;
use Plib\SystemChecker;
use Plib\View;

class InfoCommand
{
    /** @var string */
    private $pluginFolder;

    /** @var SystemChecker */
    private $systemChecker;

    /** @var DocumentStore */
    private $store;

    /** @var View */
    private $view;

    public function __construct(
        string $pluginFolder,
        SystemChecker $systemChecker,
        DocumentStore $store,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->systemChecker = $systemChecker;
        $this->store = $store;
        $this->view = $view;
    }

    public function render(): string
    {
        $checks = [
            $this->checkPHPVersion("7.1.0"),
            $this->checkXHVersion("1.7.0"),
        ];
        foreach ($this->getWritableFolders() as $folder) {
            $checks[]  = $this->checkWritability($folder);
        }
        return $this->view->render("about", [
            "version" => Dic::VERSION,
            "checks" => $checks,
        ]);
    }

    private function checkPHPVersion(string $version): string
    {
        $kind = $this->systemChecker->checkVersion(PHP_VERSION, $version) ? 'success' : 'fail';
        return $this->view->message($kind, "syscheck_phpversion", $version);
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

    /** @return list<string> */
    private function getWritableFolders(): array
    {
        $folders = [];
        foreach (["config/", "css/", "languages/"] as $folder) {
            $folders[] = $this->pluginFolder . $folder;
        }
        $folders[] = $this->store->folder();
        return $folders;
    }
}
