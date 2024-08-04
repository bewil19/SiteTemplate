<?php

namespace bewil19\Site;

enum DatabaseType
{
    public const dbType = 'dbType';

    public const dbUsername = 'dbUsername';

    public const dbPassword = 'dbPassword';

    public const dbHost = 'dbHost';

    public const dbPort = 'dbPort';

    public const autoConnect = 'autoConnect';

    public const dbName = 'dbName';

    public const mysql = 'MySQL';

    public const autoInt = 'AUTO_INCREMENT';

    public const int = 'int';

    public const notNull = 'NOT NULL';

    public const varchar = 'varchar(%int%)';

    public const primaryKey = 'PRIMARY KEY (`%name%`)';

    public const tableType = 'type';

    public const tableLength = 'length';

    public const tableName = 'name';

    public const default = 'default';

    public const autoIntName = 'autoInt';
}
