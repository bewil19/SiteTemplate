<?php

namespace bewil19\Site;

enum DatabaseType: string
{
    case dbType = 'dbType';

    case dbUsername = 'dbUsername';

    case dbPassword = 'dbPassword';

    case dbHost = 'dbHost';

    case dbPort = 'dbPort';

    case autoConnect = 'autoConnect';

    case dbName = 'dbName';

    case mysql = 'MySQL';

    case autoInt = 'AUTO_INCREMENT';

    case int = 'int';

    case notNull = 'NOT NULL';

    case varchar = 'varchar(%int%)';

    case primaryKey = 'PRIMARY KEY (`%name%`)';

    case tableType = 'type';

    case tableLength = 'length';

    case tableName = 'name';

    case default = 'default';

    case autoIntName = 'autoInt';
}
