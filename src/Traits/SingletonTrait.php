<?php

namespace bewil19\Site\Traits;

trait SingletonTrait
{
    private static $instance;

    public static function getInstance(...$args)
    {
        if (!isset(self::$instance)) {
            self::$instance = new static(...$args); // late static binding
        } elseif (!empty($args)) {
            throw new \LogicException('Singleton already instantiated. Cannot pass arguments again.');
        }

        return self::$instance;
    }

    // Prevent cloning and unserializing
    private function __clone() {}
    public function __wakeup() {}
}