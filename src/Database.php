<?php

namespace bewil19\Site;

class Database
{
    private static ?Database $instance = null;

    private string $error;

    private bool $connected = false;

    private string $databaseType;

    private string $databaseName = '';

    private \PDO $database;

    private \PDOStatement $statment;

    public function __construct()
    {
        unset($this->database, $this->statment);
    }

    public function __destruct()
    {
        unset($this->database);
    }

    public static function getInstance(): Database
    {
        if (!self::$instance instanceof Database) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * @param array<string> $post
     */
    public function tryConfig(array $post): bool
    {
        unset($this->database);

        return $this->connect($post);
    }

    public function errorGet(): string
    {
        return $this->error;
    }

    /**
     * @param array<string> $config
     */
    public function getConnected(array $config = []): bool
    {
        if ([] !== $config && false === $this->connected) {
            $this->connected = $this->connect($config);
        }

        return $this->connected;
    }

    /**
     * @param null|array<string> $sqlOptions
     */
    public function query(string $sql, $sqlOptions = null, bool $firstTime = true): bool
    {
        $this->errorEmpty();

        try {
            $this->statment = $this->database->prepare($sql);

            return $this->statment->execute($sqlOptions);
        } catch (\PDOException $pdoException) {
            $this->error = $pdoException->getMessage();
        }

        return false;
    }

    public function StatmentGet(): \PDOStatement
    {
        return $this->statment;
    }

    public function StatmentDestory(): void
    {
        unset($this->statment);
    }

    public function databaseGet(): string
    {
        return $this->databaseName;
    }

    public function databaseSet(string $dbName): bool
    {
        $this->errorEmpty();
        $dbName = strtolower($dbName);

        switch ($this->databaseType) {
            case DatabaseType::mysql:
                $sql = sprintf('USE `%s`;', $dbName);
                $query = $this->query($sql);
                if ($query) {
                    $this->databaseName = $dbName;

                    return true;
                }

                $this->error = 'Unable to set database';

                break;

            default:
                $this->error = 'Unkown database type';

                break;
        }

        return false;
    }

    public function databaseExist(string $dbName): bool
    {
        $this->errorEmpty();
        $dbName = strtolower($dbName);

        switch ($this->databaseType) {
            case DatabaseType::mysql:
                $sql = 'SHOW DATABASES';
                if (false === $this->query($sql)) {
                    $this->error = 'Unable to check if database exist';
                } else {
                    $fetchAll = $this->statment->fetchAll(\PDO::FETCH_COLUMN);
                    foreach ($fetchAll as $DBname) {
                        if ($DBname === $dbName) {
                            return true;
                        }
                    }
                }

                break;

            default:
                $this->error = 'Unkown database type';

                break;
        }

        return false;
    }

    public function databaseCreate(string $dbName): bool
    {
        $this->errorEmpty();
        $dbName = strtolower($dbName);

        switch ($this->databaseType) {
            case DatabaseType::mysql:
                $sql = sprintf('CREATE DATABASE `%s`', $dbName);
                if (false === $this->query($sql)) {
                    $this->error = 'Unable to create database';
                } else {
                    return true;
                }

                break;

            default:
                $this->error = 'Unkown database type';

                break;
        }

        return false;
    }

    public function databaseDelete(string $dbName): bool
    {
        $this->errorEmpty();
        $dbName = strtolower($dbName);

        switch ($this->databaseType) {
            case DatabaseType::mysql:
                $sql = sprintf('DROP DATABASE `%s`;', $dbName);
                $query = $this->query($sql);
                if (false === $query) {
                    $this->error = 'Unable to delete database';
                } else {
                    return true;
                }

                break;

            default:
                $this->error = 'Unkown database type';

                break;
        }

        return false;
    }

    public function tableExist(string $tableName): bool
    {
        $this->errorEmpty();
        $tableName = strtolower($tableName);

        switch ($this->databaseType) {
            case DatabaseType::mysql:
                $sql = 'SHOW TABLES;';
                $query = $this->query($sql);
                if (!$query) {
                    $this->error = 'Unable to check if table exist';
                } else {
                    $fetchAll = $this->statment->fetchAll(\PDO::FETCH_COLUMN);
                    foreach ($fetchAll as $TableName) {
                        if ($TableName === $tableName) {
                            return true;
                        }
                    }
                }

                break;

            default:
                $this->error = 'Unkown database type';

                break;
        }

        return false;
    }

    /**
     * @param array<int|string, array<string, string>|string> $tableOptions
     */
    public function tableCreate(string $tableName, array $tableOptions): bool
    {
        $this->errorEmpty();
        $tableName = strtolower($tableName);

        switch ($this->databaseType) {
            case DatabaseType::mysql:
                $sql = sprintf('CREATE TABLE `%s` (', $tableName);
                foreach ($tableOptions as $key => $val) {
                    if (is_array($val)) {
                        $sql .= sprintf('`%s` ', $val[DatabaseType::tableName]);

                        switch ($val[DatabaseType::tableType]) {
                            case DatabaseType::int:
                                $sql .= DatabaseType::int.' ';

                                break;

                            case DatabaseType::varchar:
                                $sql .= str_replace('%int%', $val[DatabaseType::tableLength], $val[DatabaseType::tableType]).' ';

                                break;
                        }

                        $sql .= $val[DatabaseType::default];
                        if (isset($val[DatabaseType::autoIntName])) {
                            $sql .= ' '.$val[DatabaseType::autoIntName].', ';
                        } else {
                            $sql .= ', ';
                        }
                    } else {
                        $sql .= str_replace('%name%', $val, (string) $key);
                    }
                }

                $sql .= ')';

                $query = $this->query($sql);
                if (!$query) {
                    $this->error = 'Unable to create table';
                } else {
                    return true;
                }

                break;

            default:
                $this->error = 'Unkown database type';

                break;
        }

        return false;
    }

    public function tableDelete(string $tableName): bool
    {
        $this->errorEmpty();
        $tableName = strtolower($tableName);

        switch ($this->databaseType) {
            case DatabaseType::mysql:
                $sql = sprintf('DROP TABLE `%s`;', $tableName);
                $query = $this->query($sql);
                if (false === $query) {
                    $this->error = 'Unable to delete table';
                } else {
                    return true;
                }

                break;

            default:
                $this->error = 'Unkown database type';

                break;
        }

        return false;
    }

    private function errorEmpty(): string
    {
        return $this->error = '';
    }

    /**
     * @param array<string> $config
     */
    private function connect($config): bool
    {
        $this->errorEmpty();
        $connect = false;

        switch ($config[DatabaseType::dbType]) {
            case DatabaseType::mysql:
                $connect = $this->connectMysql($config);

                break;

            default:
                $this->error = 'Unkown database type';

                break;
        }

        if ($connect) {
            $this->database->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->database->setAttribute(\PDO::ATTR_AUTOCOMMIT, true);
            $this->databaseType = $config[DatabaseType::dbType];
            $this->databaseSet($config[DatabaseType::dbName]);
        }

        return $connect;
    }

    /**
     * @param array<string> $config
     */
    private function connectMysql(array $config): bool
    {
        $this->errorEmpty();

        try {
            $this->database = new \PDO('mysql:host'.$config[DatabaseType::dbHost].';port='.$config[DatabaseType::dbPort], $config[DatabaseType::dbUsername], $config[DatabaseType::dbPassword]);
        } catch (\PDOException $pdoException) {
            $this->error = $pdoException->getMessage();

            return false;
        }

        return true;
    }
}
