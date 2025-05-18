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

use Plib\Document;
use Plib\DocumentStore;

final class Room implements Document
{
    /** @var string */
    private $name;

    /** @var list<Entry> */
    private $entries = [];

    public static function fromString(string $contents, string $key): self
    {
        $that = new self(basename($key, ".csv"));
        $lines = preg_split('/\r?\n/', $contents);
        if (!is_array($lines)) {
            return $that;
        }
        foreach ($lines as $line) {
            if (!empty($line)) {
                $that->entries[] = Entry::makeFromLine($line);
            }
        }
        return $that;
    }

    public static function retrieve(string $name, DocumentStore $store): self
    {
        $that = $store->retrieve("$name.csv", self::class);
        assert($that instanceof self);
        return $that;
    }

    public static function update(string $name, DocumentStore $store): self
    {
        $that = $store->update("$name.csv", self::class);
        assert($that instanceof self);
        return $that;
    }

    public static function isValidName(string $name): bool
    {
        return (bool) preg_match('/^[a-z0-9-]*$/u', $name);
    }

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }

    /** @return list<Entry> */
    public function entries(): array
    {
        return $this->entries;
    }

    public function purgeIfExpired(int $expiration): void
    {
        $expired = true;
        foreach ($this->entries as $entry) {
            if ($entry->getTimestamp() >= $expiration) {
                $expired = false;
                break;
            }
        }
        if ($expired) {
            $this->entries = [];
        }
    }

    public function postMessage(int $timestamp, string $username, string $text): Entry
    {
        $entry = new Entry($timestamp, $username, $text);
        $this->entries[] = $entry;
        return $entry;
    }

    public function toString(): string
    {
        $lines = [];
        foreach ($this->entries as $entry) {
            $lines[] = $entry->getLine();
        }
        return implode("\n", $lines);
    }
}
