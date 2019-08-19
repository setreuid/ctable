<?php
    $src = 'index.php?uid=' . $_REQUEST['uid'] . '&upw=' . $_REQUEST['upw'];
?>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <style>
        html, body {
            padding: 0;
            margin: 0;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
            
        }
    </style>
</head>
<body>
    <iframe src="<?=$src?>"></iframe>
</body>
</html>
