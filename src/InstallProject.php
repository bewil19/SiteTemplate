<?php

namespace bewil19\Site;
use Composer\Script\Event;

class InstallProject{

    public static function postCreateProject(Event $event){
        $projectFolder = str_replace("vendor" . DIRECTORY_SEPARATOR . "bewil19" . DIRECTORY_SEPARATOR . "sitetemplate" . DIRECTORY_SEPARATOR . "src", "", __DIR__);
        $sourceFolder = __DIR__;
        if(str_replace($projectFolder, "", $sourceFolder) === "vendor" . DIRECTORY_SEPARATOR . "bewil19" . DIRECTORY_SEPARATOR . "sitetemplate" . DIRECTORY_SEPARATOR . "src"){
            // do new project code
            echo "Yes";
        } else {
            echo "No";
        }
    }
}