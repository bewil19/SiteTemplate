<?php

namespace bewil19\Site;

class User{

    const GREGORIAN_OFFSET = 0x01b21dd213814000;

    private static $instance = null;

    public static function getInstance(): User
    {
        if (!self::$instance instanceof User) {
            self::$instance = new User();
        }

        return self::$instance;
    }

    public static function randomString($type = "alnum", $len = 10){
        switch($type){
            case "basic":
                return mt_rand();
            case "alnum":
            case "numeric":
            case "nozero":
            case "alpha":
                switch ($type){
                    case 'alpha':
                        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'alnum':
                        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'numeric':
                        $pool = '0123456789';
                        break;
                    case 'nozero':
                        $pool = '123456789';
                        break;
                }
                return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
            case "md5":
                return md5(uniqid(mt_rand()));
            case 'sha1':
                return sha1(uniqid(mt_rand(), TRUE));
        }
    }

    private $uuid;
    private $lastTime = null;

    private function __construct(){
        $this->uuid = $this->newUuid(self::randomString("nozero", 15));
    }

    private function getClock(){
        $microtime = microtime();
        list($usec, $sec) = explode(" ", $microtime);
        //list($zero, $usec) = explode(" ", $usec);
        $usec = str_pad($usec, 9, 0, STR_PAD_RIGHT);
        $usec = substr($usec, 0, 9);
        return $sec*1000000000 + $usec;
    }

    private function getTime($offset = 0){
        $time = $this->getClock() / 100;
        if($time > $this->lastTime or is_null($this->lastTime)){
            $this->lastTime = $time;
        } else {
            $time = ++$this->lastTime;
        }

        $this->lastTime = $time;

        return $time + $offset;
    }

    public function newUuid($mac): string{
        $time = $this->getTime(self::GREGORIAN_OFFSET);
        $clockSeq = $time & 0x3fff;
        $timeLow = $time & 0xffffffff;
        $timeMid = ($time >> 32) & 0xffff;
        $timeHiVersion = ($time >> 48) & 0xfff;
        $clockSeqLow = $clockSeq & 0xff;
        $clockSeqHiVariant = ($clockSeq >> 8) & 0x3f;

        $upper = ($timeLow << 32) | ($timeMid << 16) | $timeHiVersion;
        $upper &= ~0x7000;
        $upper |= 1 << 12;

        $lower = (($clockSeqHiVariant << 8) | $clockSeqLow) << 48;
        $lower |= $mac;
        $lower &= ~(0xc000 << 48);
        $lower |= (0x8000 << 48);

        $uuid = strrev(str_pad(substr(strrev(dechex($upper)), 0, 16), 16, '0', STR_PAD_RIGHT))
            . strrev(substr(strrev(dechex($lower)), 0, 16));
        $join = '-';
        return join($join, [
            substr($uuid, 0, 8),
            substr($uuid, 8, 4),
            substr($uuid, 12, 4),
            substr($uuid, 16, 4),
            substr($uuid, 20)
        ]);
    }

    public function getUuid(){
        return $this->uuid;
    }
}