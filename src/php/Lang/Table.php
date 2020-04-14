<?php

namespace Phel\Lang;

use ArrayAccess;
use Countable;
use Iterator;
use Phel\Printer;

class Table extends Phel implements ArrayAccess, Countable, Iterator {

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $keys = [];

    public static function empty() {
        return new Table();
    }

    public static function fromKVs(...$kvs) {
        $result = new Table();
        for ($i = 0; $i < count($kvs); $i += 2) {
            $result[$kvs[$i]] = $kvs[$i+1];
        }
        return $result;
    }

    public static function fromKVArray(array $kvs) {
        return self::fromKVs(...$kvs);
    }

    public function offsetSet($offset, $value) {
        $hash = $this->offsetHash($offset);

        if ($value === null) {
            unset($this->keys[$hash]);
            unset($this->data[$hash]);
        } else {
            $this->keys[$hash] = $offset;
            $this->data[$hash] = $value;
        }
    }

    public function offsetExists($offset) {
        $hash = $this->offsetHash($offset);

        return isset($this->data[$hash]);
    }

    public function offsetUnset($offset) {
        $hash = $this->offsetHash($offset);

        unset($this->keys[$hash]);
        unset($this->data[$hash]);
    }

    public function offsetGet($offset) {
        $hash = $this->offsetHash($offset);

        return isset($this->data[$hash]) ? $this->data[$hash] : null;
    }

    public function count(): int {
        return count($this->data);
    }

    public function current() {
        return current($this->data);
    }

    public function key() {
        $key = key($this->data);

        if ($key !== null) {
            return $this->keys[$key];
        } else {
            return null;
        }
    }

    public function next() {
        return next($this->data);
    }

    public function rewind() {
        reset($this->data);
    }

    public function valid() {
        return key($this->data) !== null;
    }

    public function hash() {
        return spl_object_hash($this);
    }

    public function equals($other): bool {
        return $this === $other;
    }

    private function offsetHash($offset) {
        if ($offset instanceof Phel) {
            return $offset->hash();
        } else if ($offset instanceof object) {
            return spl_object_hash($offset);
        } else {
            return $offset;
        }
    }

    public function isTruthy(): bool {
        return count($this->keys) > 0;
    }

    public function __toString()
    {
        $printer = new Printer();
        return $printer->print($this, true);
    }
}