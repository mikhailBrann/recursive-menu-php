<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/classes/DB.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/data/db_info.php');

    //инициализируемся
    $newConnection = new DB(DB_NAME, DB_USER, DB_PASS);
    foreach (DB_CONTENT as $item) {
        if(empty($item['parent'])) {
            $newConnection->addMenuItem($item['name']);
        } else {
            $newConnection->addMenuItem($item['name'], $item['parent']);
        }
    }

    $menu = '<ul class="menu fisrs-level">' . $newConnection->createMenu() . '</ul>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test</title>
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
    <header>
        <div class="container">
            <nav><?=$menu?></nav>
        </div>
    </header>
</body>

<script src="/js/main.js"></script>
</html>

