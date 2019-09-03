<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    define('DS', DIRECTORY_SEPARATOR);
    define('BASE_DIR', __DIR__ . DS);
    define('FILE_DIR', BASE_DIR . 'files' . DS);
    define('FILE_URL', '/files/');
    define('BASE_URL' , '');

    require('src/app.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PHP Editor</title>
    <?php style('css/style.css') ?>
    <?php style('css/codemirror.css') ?>
    <?php style('css/codemirror-theme.css') ?>
</head>
<body>
    <div>
        <table cellspacing="0" id="columns">
            <tbody>
                <tr>
                    <td id="sidebar" valign="top">
                        <div id="new-file-btn-wrapper">
                            <button id="new-file-btn" class="btn btn-block btn-primary">New file</button>
                        </div>
                        <ul id="file-list"></ul>
                    </td>
                    <td id="editor" valign="top">
                        <textarea id="codearea" cols="30" rows="10"></textarea>
                    </td>
                    <td id="output" valign="top">
                        <iframe id="output-iframe" src="" frameborder="0"></iframe>
                        <div id="info">
                            <small>PHP <?php echo PHP_VERSION ?></small>
                            <small>display_errors: <?php echo ini_get('display_errors') ?></small>
                            <small>display_startup_errors: <?php echo ini_get('display_startup_errors') ?></small>
                        </div>
                        <div id="iframe-overlay"></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php script('js/codemirror/codemirror.js') ?>
    <?php script('js/codemirror/matchbrackets.js') ?>
    <?php script('js/codemirror/htmlmixed.js') ?>
    <?php script('js/codemirror/xml.js') ?>
    <?php script('js/codemirror/javascript.js') ?>
    <?php script('js/codemirror/css.js') ?>
    <?php script('js/codemirror/clike.js') ?>
    <?php script('js/codemirror/php.js') ?>
    <?php script('js/column-resize.js') ?>
    <script>
        window.url = {
            updateFile: '<?php echo BASE_URL ?>/?action=update_file',
            getFile: '<?php echo BASE_URL ?>/?action=get_file&filename=',
            createFile: '<?php echo BASE_URL ?>/?action=create_file',
            getFiles: '<?php echo BASE_URL ?>/?action=get_files',
            deleteFile: '<?php echo BASE_URL ?>/?action=delete_file'
        }
    </script>
    <?php script('js/app.js') ?>
</body>
</html>