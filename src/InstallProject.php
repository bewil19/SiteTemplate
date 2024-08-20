<?php

namespace bewil19\Site;
use Composer\Script\Event;

class InstallProject{

    public static function postCreateProject(Event $event){
        echo __DIR__;
    }
}