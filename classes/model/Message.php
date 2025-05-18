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

namespace Chat\Model;

final class Message
{
    /** @var int */
    private $timestamp;

    /** @var string */
    private $username;

    /** @var string */
    private $text;

    public static function fromString(string $line): ?self
    {
        $fields = explode("\t", $line, 3);
        if (count($fields) < 3) {
            return null;
        }
        return new self((int) $fields[0], $fields[1], $fields[2]);
    }

    public static function fromPost(int $timestamp, string $username, string $text): ?self
    {
        if (utf8_strlen($text) > 160) {
            return null;
        }
        return new self($timestamp, $username, $text);
    }

    public function __construct(int $timestamp, string $username, string $text)
    {
        $this->timestamp = $timestamp;
        $this->username = $username;
        $this->text = $text;
    }

    public function timestamp(): int
    {
        return $this->timestamp;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function text(): string
    {
        return $this->text;
    }

    public function toString(): string
    {
        return $this->timestamp . "\t" . $this->username . "\t" . $this->text;
    }
}
