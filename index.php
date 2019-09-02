<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    define('DS', DIRECTORY_SEPARATOR);
    define('BASE_DIR', __DIR__ . DS);
    define('FILE_DIR', BASE_DIR . 'files' . DS);
    define('FILE_URL', '/files/');

    require BASE_DIR . 'src' . DS . 'app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PHP Editor</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/codemirror.css">
    <link rel="stylesheet" href="css/codemirror-theme.css">
</head>
<body>
    <div id="content">
        <div id="sidebar">
            <div id="new-file-btn-wrapper">
                <button id="new-file-btn" class="btn btn-block btn-primary">New file</button>
            </div>
            <ul id="file-list"></ul>
        </div>
        <div id="editor">
            <textarea id="codearea" cols="30" rows="10"></textarea>
        </div>

        <div id="output">
            <iframe id="output-iframe" src="" frameborder="0"></iframe>
            <div id="info">
                <small>PHP <?php echo PHP_VERSION ?></small>
                <small>display_errors: <?php echo ini_get('display_errors') ?></small>
                <small>display_startup_errors: <?php echo ini_get('display_startup_errors') ?></small>
            </div>
        </div>
    </div>
    <script src="js/codemirror/codemirror.js"></script>
    <script src="js/codemirror/matchbrackets.js"></script>
    <script src="js/codemirror/htmlmixed.js"></script>
    <script src="js/codemirror/xml.js"></script>
    <script src="js/codemirror/javascript.js"></script>
    <script src="js/codemirror/css.js"></script>
    <script src="js/codemirror/clike.js"></script>
    <script src="js/codemirror/php.js"></script>
    <script src="js/app.js"></script>
    <script></script>
</body>
</html>