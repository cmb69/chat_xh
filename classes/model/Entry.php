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

final class Entry
{
    /** @var int */
    private $timestamp;

    /** @var string */
    private $username;

    /** @var string */
    private $message;

    public static function makeFromLine(string $line): self
    {
        [$timestamp, $username, $message] = explode("\t", $line, 3);
        return new self((int) $timestamp, $username, $message);
    }

    public function __construct(int $timestamp, string $username, string $message)
    {
        $this->timestamp = $timestamp;
        $this->username = $username;
        $this->message = $message;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getLine(): string
    {
        return $this->timestamp . "\t" . $this->username . "\t" . $this->message;
    }
}
