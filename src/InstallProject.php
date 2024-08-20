<?php

namespace bewil19\Site;

class InstallProject{

    public static function postCreateProject(){
        $sourceFolder = dirname(__DIR__);
        $projectFolder = str_replace("vendor" . DIRECTORY_SEPARATOR . "bewil19" . DIRECTORY_SEPARATOR . "sitetemplate", "", $sourceFolder);
        
        if(str_replace($projectFolder, "", $sourceFolder) === "vendor" . DIRECTORY_SEPARATOR . "bewil19" . DIRECTORY_SEPARATOR . "sitetemplate"){
            // do new project code
            var_dump($sourceFolder);
            var_dump($projectFolder);
        } else {
            echo "No";
        }
    }
}