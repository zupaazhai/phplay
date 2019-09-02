<?php

class App
{
    protected $allowedFiles;
    
    public function __construct()
    {
        $this->allowedFiles = array('php');
    }

    public function getFileList()
    {
        $phpFiles = array();
        $files = scandir(FILE_DIR);

        foreach ($files as $file) {
            if (in_array($file, array('index.php', '.', '..'))) {
                continue;
            }

            if (is_dir(FILE_DIR . $file)) {
                continue;
            }

            $fileinfo = pathinfo($file);

            if (empty($fileinfo['extension'])) {
                continue;
            }

            $ext = $fileinfo['extension'];
            
            if (!in_array($ext, $this->allowedFiles)) {
                continue;
            }
            
            $phpFiles[] = array(
                'name' => $file,
                'path' => FILE_DIR . $file,
                'url' => FILE_URL . $file
            );
        }

        return $phpFiles;
    }

    public function getFile($fileName)
    {
        $filePath = FILE_DIR . $fileName;

        if (!file_exists($filePath)) {
            return false; 
        }

        return file_get_contents($filePath);
    }

    public function saveFile($fileName, $content = '')
    {
        $filePath = FILE_DIR . $fileName;

        if (!file_exists($filePath)) {
            return false; 
        }

        return file_put_contents($filePath, $content);
    }

    public function createFile($fileName, $content = '')
    {
        $filePath = FILE_DIR . $fileName;

        if (file_exists($filePath)) {
            return false; 
        }

        return file_put_contents($filePath, $content);
    }

    public function deleteFile($fileName)
    {
        $filePath = FILE_DIR . $fileName;

        if (!file_exists($filePath)) {
            return false; 
        }

        return unlink($filePath);
    }

    public function req()
    {
        $req = file_get_contents('php://input');
        return json_decode($req, true);
    }

    public function json($code, $data, $msg = '')
    {
        header('Content-Type: application/json');
        http_response_code($code);

        echo json_encode(array(
            'msg' => $msg,
            'data' => $data
        ));
        die;
    }
}

class Action
{
    protected $actions = array(
        'get_files' => 'getFiles',
        'get_file' => 'getFile',
        'create_file' => 'createFile',
        'update_file' => 'updateFile',
        'delete_file' => 'deleteFile'
    );

    protected $app;

    public function __construct()
    {
        $this->app = new App;
    }

    public function run()
    {
        if (!isset($_GET['action'])) {
            return;
        }

        $action = $_GET['action'];
        
        if (!array_key_exists($action, $this->actions)) {
            return;
        }

        $this->{$this->actions[$action]}();
        die;
    }

    public function getFiles()
    {
        $files = $this->app->getFileList();

        $this->app->json(200, $files, 'get_file_list');
    }

    public function getFile()
    {
        $fileName = $_GET['filename'];
        $content = $this->app->getFile($fileName);

        $this->app->json(200, array(
            'content' => $content
        ), 'get_file');
    }

    public function createFile()
    {
        $req = $this->app->req();
        $res = $this->app->createFile($req['name'], $req['content']);

        if (!$res) {
            return $this->app->json(500, array(), 'file_exists');
            die;
        }

        return $this->app->json(201, $req, 'created');
    }

    public function updateFile()
    {
        $req = $this->app->req();
        $res = $this->app->saveFile($req['name'], $req['content']);

        if (!$res) {
            return $this->app->json(500, array(), 'save_fail');
            die;
        }

        return $this->app->json(200, $req, 'updated');
    }

    public function deleteFile()
    {
        $req = $this->app->req();

        $this->app->deleteFile($req['name']);
    }
}

$action = new Action();
$action->run();