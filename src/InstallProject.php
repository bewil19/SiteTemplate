<?php

namespace bewil19\Site;

class InstallProject
{
    public static function postPackageInstall()
    {
        $sourceFolder = dirname(__DIR__);
        $projectFolder = str_replace('vendor'.DIRECTORY_SEPARATOR.'bewil19'.DIRECTORY_SEPARATOR.'sitetemplate', '', $sourceFolder);

        if (str_replace($projectFolder, '', $sourceFolder) === 'vendor'.DIRECTORY_SEPARATOR.'bewil19'.DIRECTORY_SEPARATOR.'sitetemplate') {
            $files = self::getDirContents($sourceFolder.DIRECTORY_SEPARATOR.'example');
            self::copyFiles($files, $projectFolder, $sourceFolder.DIRECTORY_SEPARATOR.'example'.DIRECTORY_SEPARATOR);

            $files = [
                $sourceFolder.DIRECTORY_SEPARATOR.'.gitignore',
                $sourceFolder.DIRECTORY_SEPARATOR.'.php-cs-fixer.dist.php',
                $sourceFolder.DIRECTORY_SEPARATOR.'phpstan.neon.dist',
                $sourceFolder.DIRECTORY_SEPARATOR.'.github'.DIRECTORY_SEPARATOR.'dependabot.yml',
            ];
            self::copyFiles($files, $projectFolder, $sourceFolder.DIRECTORY_SEPARATOR);

            echo 'Project Created or Updated from Example!';
        } else {
            echo 'Project can not be created from here!';
        }
    }

    private static function copyFiles(array $files, string $projectFolder, string $search): void
    {
        foreach ($files as $file) {
            $fileName = str_replace($search, '', $file);
            if (false === file_exists($projectFolder.$fileName)) {
                $fileContents = file_get_contents($file);
                self::fileForceContents($projectFolder.$fileName, $fileContents);
            }
        }
    }

    public static function getDirContents($dir, &$results = [])
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if (!is_dir($path)) {
                $results[] = $path;
            } elseif ('.' != $value && '..' != $value) {
                self::getDirContents($path, $results);
                // $results[] = $path;
            }
        }

        return $results;
    }

    public static function fileForceContents($fullPath, $contents, $flags = 0)
    {
        $parts = explode(DIRECTORY_SEPARATOR, $fullPath);
        array_pop($parts);
        $dir = implode(DIRECTORY_SEPARATOR, $parts);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($fullPath, $contents, $flags);
    }
}
