<?php

namespace bewil19\Site;

class User
{
    public const GREGORIAN_OFFSET = 0x01B21DD213814000;

    private static $instance;

    private $uuid;
    private $lastTime;

    private function __construct()
    {
        $this->uuid = $this->newUuid(self::randomString('nozero', 15));
    }

    public static function getInstance(): User
    {
        if (!self::$instance instanceof User) {
            self::$instance = new User();
        }

        return self::$instance;
    }

    public static function randomString($type = 'alnum', $len = 10)
    {
        switch ($type) {
            case 'basic':
                return mt_rand();

            case 'alnum':
            case 'numeric':
            case 'nozero':
            case 'alpha':
                switch ($type) {
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

            case 'md5':
                return md5(uniqid(mt_rand()));

            case 'sha1':
                return sha1(uniqid(mt_rand(), true));
        }
    }

    public function newUuid($mac): string
    {
        $time = $this->getTime(self::GREGORIAN_OFFSET);
        $clockSeq = $time & 0x3FFF;
        $timeLow = $time & 0xFFFFFFFF;
        $timeMid = ($time >> 32) & 0xFFFF;
        $timeHiVersion = ($time >> 48) & 0xFFF;
        $clockSeqLow = $clockSeq & 0xFF;
        $clockSeqHiVariant = ($clockSeq >> 8) & 0x3F;

        $upper = ($timeLow << 32) | ($timeMid << 16) | $timeHiVersion;
        $upper &= ~0x7000;
        $upper |= 1 << 12;

        $lower = (($clockSeqHiVariant << 8) | $clockSeqLow) << 48;
        $lower |= $mac;
        $lower &= ~(0xC000 << 48);
        $lower |= (0x8000 << 48);

        $uuid = strrev(str_pad(substr(strrev(dechex($upper)), 0, 16), 16, '0', STR_PAD_RIGHT))
            .strrev(substr(strrev(dechex($lower)), 0, 16));
        $join = '-';

        return join($join, [
            substr($uuid, 0, 8),
            substr($uuid, 8, 4),
            substr($uuid, 12, 4),
            substr($uuid, 16, 4),
            substr($uuid, 20),
        ]);
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    private function getClock()
    {
        $microtime = microtime();
        list($usec, $sec) = explode(' ', $microtime);
        $usec = str_pad($usec, 9, 0, STR_PAD_RIGHT);
        $usec = substr($usec, 0, 9);

        return $sec * 1000000000 + $usec;
    }

    private function getTime($offset = 0)
    {
        $time = $this->getClock() / 100;
        if ($time > $this->lastTime or is_null($this->lastTime)) {
            $this->lastTime = $time;
        } else {
            $time = ++$this->lastTime;
        }

        $this->lastTime = $time;

        return $time + $offset;
    }

    public function registerAjax($post){
        if(isset($post["agree"])){
            $agree = true;
        } else {
            $agree = false;
        }

        return $this->register($post["username"], $post["password"], $post["confirmpassword"], $post["email"], $agree);
    }

    private function isValidEmail($email){
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function getUser($username = null, $email = null){
        if(is_null($username) == false){
            $user = $username;
            $sql = "SELECT * FROM `users` WHERE `username` = :user;";
        } elseif(is_null($email) == false){
            $user = $email;
            $sql = "SELECT * FROM `users` WHERE `email` = :user;";
        }

        $sqlOptions = [':user' => $user];

        $database = Database::getInstance();

        if($database->query($sql, $sqlOptions) === false){
            return false;
        }

        $result = $database->StatmentGet();
        if($result->rowCount() == 0){
            return false;
        }

        return $result->fetch(\PDO::FETCH_ASSOC);
    }

    public function register($username, $password, $confirmpassword, $email, $agree = true){
        $errors = array();

        if(empty($username) || empty($password) || empty($confirmpassword) || empty($email)){
            $errors[] = "Make sure you fill all boxed in!";
        } else {
            if($this->isValidEmail($email) === false){
                $errors[] = "Invalid email address!";
            }
            if($password <> $confirmpassword){
                $errors[] = "Passwords do not match!";
            }
            if($agree === false){
                $errors[] = "Must agree to terms and conditions!";
            }

        }

        if(count($errors) > 0){
            return $errors;
        }

        $errors = array();

        $user = $this->getUser(username: $username);
        $user2 = $this->getUser(email: $email);

        if(is_array($user)){
            $errors[] = "Username already in use!";
        }
        if(is_array($user2)){
            $errors[] = "Email address already in use!";
        }

        if(count($errors) > 0){
            return $errors;
        }

        $errors = array();

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $verifyHash = $this->randomString("md5");

        $database = Database::getInstance();
        $sql = "INSERT INTO `users` (`username`, `email`, `password`, `hash`) VALUES (:username, :email, :passwordHash, :verifyhash);";
        $sqlOptions = [
            ':username' => $username,
            ':email' => $email,
            ':passwordHash' => $passwordHash,
            ':verifyhash' => $verifyHash
        ];

        $database = Database::getInstance();
        if($database->query($sql, $sqlOptions) === false){
            return array("Unable to create account, contact site admin!");
        }

        return array("Created account, you can now login!");
    } 
}
