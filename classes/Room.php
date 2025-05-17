<?php

/**
 * The chat rooms.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Chat
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Chat_XH
 */

namespace Chat;

/**
 * The chat rooms.
 *
 * @category CMSimple_XH
 * @package  Chat
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Chat_XH
 */
class Room
{
    /** @var string */
    protected $name;

    /** @var int */
    protected $purgeInterval;

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
