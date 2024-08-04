<?php

namespace bewil19\Site;

use League\Plates\Engine;
use League\Plates\Exception\TemplateNotFound;
use League\Plates\Extension\Asset;

class Site
{
    private static ?Site $instance = null;

    private string $rootDir;

    private string $templateDir;

    private string $pageDir;

    private string $subDir;

    private string $name;

    private string $siteUrl;

    private function __construct(string $rootDir)
    {
        $this->rootDir = $rootDir . DIRECTORY_SEPARATOR;
        $this->templateDir = $this->rootDir . 'template' . DIRECTORY_SEPARATOR;
        $this->pageDir = $this->rootDir . 'pages' . DIRECTORY_SEPARATOR;

        if (false == file_exists($this->pageDir)) {
            mkdir($this->pageDir);
        }

        $http = isset($_SERVER['HTTPS']) && 'off' != $_SERVER['HTTPS'] ? 'https' : 'http';
        $httpHost = $_SERVER['HTTP_HOST'] ?? '';
        $serverName = $_SERVER['SERVER_NAME'] ?? '';
        $this->siteUrl = $http . '://';
        if (!empty($httpHost)) {
            $this->siteUrl .= $httpHost;
        } elseif (!empty($serverName)) {
            $this->siteUrl .= $serverName;
        }

        // is in sub dir
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
        $root = str_replace('\\', '/', $this->rootDir);
        $subDir = str_replace($docRoot, '', $root);
        $this->subDir = $subDir;
    }

    public static function getInstance(string $rootDir): Site
    {
        if (!self::$instance instanceof Site) {
            self::$instance = new Site($rootDir);
        }

        return self::$instance;
    }

    public function getRootDir(): string
    {
        return $this->rootDir;
    }

    public function redirect(string $page, int $code = 301): never
    {
        http_response_code($code);
        header('Location: ' . $this->siteUrl . $this->subDir . $page);
        echo '';

        exit;
    }

    public function getPage(): void
    {
        if (!isset($_GET['page']) || empty($_GET['page']) || 'index' == $_GET['page']) {
            $this->redirect('home');
        }

        $this->name = $_GET['page'];
        $config = Config::getInstance();

        if (false === $config->checkConfig() && 'install' !== $this->name) {
            //$this->redirect('install', 302);
        }

        $template = new Engine($this->pageDir);
        $template->loadExtension(new Asset($this->templateDir . 'assets'));

        if (!file_exists($this->pageDir . $this->name . '.php')) {
            $template->setDirectory(__DIR__ . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR);
        } else {
            $template->setDirectory(__DIR__ . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR);
        }

        $siteName = $config->getSetting('siteName');
        if ('' === $siteName || '0' === $siteName) {
            $siteName = 'New Site';
        }

        $template->addData([
            'siteName' => $siteName,
            'canonicalLink' => $this->siteUrl . $this->subDir . $this->name,
        ]);

        try {
            echo $template->render($this->name);
        } catch (TemplateNotFound) {
            http_response_code(404);
            if (file_exists($this->pageDir . 'errorpage.php')) {
                $template->addData([
                    'errorHeader' => 'Page not found!',
                    'errorText' => '<p>This page has not been found!<br>Please try again later!</p>',
                ]);
                echo $template->render('errorpage');
            } else {
                echo '<h1><center>Error Page</center></h1>';
                echo '<center><p>This page has not been found!<br>Please try again later!</p></center>';
            }
        }
    }
}
