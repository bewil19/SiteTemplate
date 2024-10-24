<?php

namespace bewil19\Site;

class Config
{
    private static ?Config $instance = null;

    /**
     * @var array<string>
     */
    private array $config = [
        DatabaseType::dbType => DatabaseType::mysql,
        DatabaseType::dbUsername => 'root',
        DatabaseType::dbPassword => '',
        DatabaseType::dbHost => 'localhost',
        DatabaseType::dbPort => '3306',
        DatabaseType::autoConnect => 'false',
        DatabaseType::dbName => 'databaseName',
    ];

    private string $installPassword = '$2y$10$OJ/67WlrXQS/GAZjhQgXm.CKxeBf6y5kJUI1LwwIYwrzNoF6559Te';

    private function __construct(private string $rootDir)
    {
        if ($this->checkConfig()) {
            $this->loadConfig();
        }
    }

    public static function getInstance(): Config
    {
        if (!self::$instance instanceof Config) {
            $rootDir = Site::getInstance('')->getRootDir();
            self::$instance = new Config($rootDir);
        }

        return self::$instance;
    }

    public function checkConfig(): bool
    {
        return file_exists($this->rootDir.'config.json');
    }

    /**
     * @return array<string> $post
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array<string> $post
     */
    public function install(array $post): string
    {
        if (false === password_verify($post['installPassword'], $this->installPassword)) {
            return 'Error: Invalid install password!';
        }
        if (isset($post[DatabaseType::autoConnect]) && 'on' === $post[DatabaseType::autoConnect]) {
            $post[DatabaseType::autoConnect] = 'true';
        } else {
            $post[DatabaseType::autoConnect] = 'false';
        }

        unset($post['submit'], $post['installPassword']);
        $database = Database::getInstance();
        if (false === $database->tryConfig($post)) {
            return 'Error: Invalid database settings!';
        }

        if (false === $this->saveConfig($post)) {
            return 'Error: Not able to save config!';
        }

        if (false === $database->databaseExist($post[DatabaseType::dbName])) {
            return "Error: Database don't exist, please make the database and try again!";
        }

        if (false === $database->tableExist('settings') && false === $database->tableCreate('settings', [
            [
                DatabaseType::tableName => 'id',
                DatabaseType::tableType => DatabaseType::int,
                DatabaseType::default => DatabaseType::notNull,
                DatabaseType::autoIntName => DatabaseType::autoInt,
            ],
            [
                DatabaseType::tableName => 'name',
                DatabaseType::tableType => DatabaseType::varchar,
                DatabaseType::tableLength => '255',
                DatabaseType::default => DatabaseType::notNull,
            ],
            [
                DatabaseType::tableName => 'value',
                DatabaseType::tableType => DatabaseType::varchar,
                DatabaseType::tableLength => '255',
                DatabaseType::default => DatabaseType::notNull,
            ],
            DatabaseType::primaryKey => 'id',
        ])) {
            return 'Error: Unable to make settings table!';
        }

        if (false === $database->tableExist('users') && false === $database->tableCreate('users', [
            [
                DatabaseType::tableName => 'id',
                DatabaseType::tableType => DatabaseType::int,
                DatabaseType::default => DatabaseType::notNull,
                DatabaseType::autoIntName => DatabaseType::autoInt,
            ],
            [
                DatabaseType::tableName => 'username',
                DatabaseType::tableType => DatabaseType::varchar,
                DatabaseType::tableLength => '255',
                DatabaseType::default => DatabaseType::notNull,
            ],
            [
                DatabaseType::tableName => 'email',
                DatabaseType::tableType => DatabaseType::varchar,
                DatabaseType::tableLength => '255',
                DatabaseType::default => DatabaseType::notNull,
            ],
            [
                DatabaseType::tableName => 'password',
                DatabaseType::tableType => DatabaseType::varchar,
                DatabaseType::tableLength => '255',
                DatabaseType::default => DatabaseType::notNull,
            ],
            [
                DatabaseType::tableName => 'hash',
                DatabaseType::tableType => DatabaseType::varchar,
                DatabaseType::tableLength => '255',
                DatabaseType::default => DatabaseType::notNull,
            ],
            DatabaseType::primaryKey => 'id',
        ])) {
            return 'Error: Unable to make users table!';
        }

        if (false === $database->tableExist('loginHistory') && false === $database->tableCreate('loginHistory', [
            [
                DatabaseType::tableName => 'id',
                DatabaseType::tableType => DatabaseType::int,
                DatabaseType::default => DatabaseType::notNull,
                DatabaseType::autoIntName => DatabaseType::autoInt,
            ],
            [
                DatabaseType::tableName => 'userID',
                DatabaseType::tableType => DatabaseType::varchar,
                DatabaseType::tableLength => '255',
                DatabaseType::default => DatabaseType::notNull,
            ],
            [
                DatabaseType::tableName => 'hash',
                DatabaseType::tableType => DatabaseType::varchar,
                DatabaseType::tableLength => '255',
                DatabaseType::default => DatabaseType::notNull,
            ],
            [
                DatabaseType::tableName => 'ip',
                DatabaseType::tableType => DatabaseType::varchar,
                DatabaseType::tableLength => '255',
                DatabaseType::default => DatabaseType::notNull,
            ],
            [
                DatabaseType::tableName => 'time',
                DatabaseType::tableType => DatabaseType::varchar,
                DatabaseType::tableLength => '255',
                DatabaseType::default => DatabaseType::notNull,
            ],
            [
                DatabaseType::tableName => 'success',
                DatabaseType::tableType => DatabaseType::int,
                DatabaseType::default => DatabaseType::notNull,
            ],
            DatabaseType::primaryKey => 'id',
        ])) {
            return 'Error: Unable to make loginHistory table!';
        }

        return 'Success: Config saved! Site ready to use!';
    }

    public function getSetting(string $settingName): string
    {
        $database = Database::getInstance();
        if (false === $database->getConnected($this->config)) {
            return '';
        }

        $sql = 'SELECT * FROM `settings` WHERE `name` = :settingName;';
        $sqlOptions = [':settingName' => $settingName];
        if (false === $database->query($sql, $sqlOptions)) {
            return '';
        }

        $result = $database->StatmentGet();
        if (0 === $result->rowCount()) {
            return '';
        }

        $result = $result->fetch(\PDO::FETCH_ASSOC);
        if (!is_array($result)) {
            return '';
        }

        return $result['value'];
    }

    private function loadConfig(): bool
    {
        $config = file_get_contents($this->rootDir.'config.json');
        if (is_bool($config)) {
            return $config;
        }

        $configArray = json_decode($config, true);
        if (!is_array($configArray)) {
            return false;
        }

        $this->config = array_merge($this->config, $configArray);

        return true;
    }

    /**
     * @param array<string> $config
     */
    private function saveConfig(array $config): bool
    {
        $save = file_put_contents($this->rootDir.'config.json', json_encode($config));

        return is_int($save);
    }
}
