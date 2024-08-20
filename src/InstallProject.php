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

            var_dump(self::getDirContents($sourceFolder . DIRECTORY_SEPARATOR . "example"));
        } else {
            echo "No";
        }
    }

    public static function getDirContents($dir, &$results = array()) {
        $files = scandir($dir);
    
        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value != "." && $value != "..") {
                self::getDirContents($path, $results);
                //$results[] = $path;
            }
        }
    
        return $results;
    }

    public static function fileForceContents($fullPath, $contents, $flags =0){
        $parts = explode( '/', $fullPath );
        array_pop( $parts );
        $dir = implode( '/', $parts );
        
        if( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        
        file_put_contents( $fullPath, $contents, $flags );
    }
}