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

class Room
{
    /** @var string */
    private $name;

    /** @var int */
    private $purgeInterval;

    public static function dataFolder(): string
    {
        global $pth;

        $filename = $pth['folder']['content'] . 'chat/';
        if (!file_exists($filename)) {
            if (mkdir($filename, 0777, true)) {
                chmod($filename, 0777);
            }
        }
        return $filename;
    }

    public static function isValidName(string $name): bool
    {
        return (bool) preg_match('/^[a-z0-9-]*$/u', $name);
    }

    public function __construct(string $name, int $purgeInterval)
    {
        $this->name = $name;
        $this->purgeInterval = $purgeInterval;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFilename(): string
    {
        return self::dataFolder() . $this->name . '.csv';
    }

    public function isWritable(): bool
    {
        $filename = $this->getFilename();
        return is_writable($filename) ||
            !file_exists($filename) && is_writable(dirname($filename));
    }

    public function isExpired(): bool
    {
        $filename = $this->getFilename();
        return file_exists($filename)
            && $this->purgeInterval
            && time() > filemtime($filename) + $this->purgeInterval;
    }

    public function purge(): void
    {
        unlink($this->getFilename());
    }

    /** @return array<Entry> */
    public function findEntries(): array
    {
        $filename = $this->getFilename();
        $entries = array();
        if (is_readable($filename)
            && ($lines = file($filename)) !== false
        ) {
            foreach ($lines as $line) {
                if (!empty($line)) {
                    $entries[] = Entry::makeFromLine(rtrim($line));
                }
            }
        }
        return $entries;
    }

    public function appendEntry(Entry $entry): bool
    {
        $filename = $this->getFilename();
        return (bool) file_put_contents($filename, $entry->getLine() . PHP_EOL, FILE_APPEND);
    }
}
