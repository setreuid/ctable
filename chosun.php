<?php
    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
    if( strpos( $_SERVER[REQUEST_URI], 'chosun.php' ) !== false && stripos( $ua,'android' ) !== false )
    {
        header('Location: https://play.google.com/store/apps/details?id=kr.elseif.ctable128022');
    }

    $ver = 83;
?>
<!doctype html>
<html ng-app="chosunApp">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, target-densityDpi=device-dpi, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <script language="javascript">
        window.account = {
            uid: "<?php echo $_REQUEST['uid']; ?>",
            upw: "<?php echo $_REQUEST['upw']; ?>",
            svVer: "<?php echo $ver; ?>"
        };
    </script>

    <link rel="stylesheet" href="css/app.css?v=<?php echo $ver; ?>">
    <link rel="stylesheet" href="css/timetable.css?v=<?php echo $ver; ?>">
    <link rel="stylesheet" href="css/settings.css?v=<?php echo $ver; ?>">
    <link rel="stylesheet" href="css/board.css?v=<?php echo $ver; ?>">
    <link rel="stylesheet" href="css/stock.css?v=<?php echo $ver; ?>">
    <link rel="stylesheet" href="css/game.css?v=<?php echo $ver; ?>">
    <link rel="stylesheet" href="libs/chart/chartist.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>
<body ng-class="{ 'process-loading' : config.isProcessLoading }">
    <div class="backdrop center ng-hide" ng-show="data.loading">
        <div class="loading"></div>
    </div>

    <div class="backdrop center ng-hide" ng-show="data.alert" ng-click="stopAlert()">
        <div class="error"><span>{{ data.alertMessage }}</span></div>
    </div>

    <div class="backdrop center confirm ng-hide" ng-show="data.confirm">
        <font>
            <div><span ng-bind-html="data.confirmMessage | toTrusted"></span></div>
            <div class="center"><button class="no" ng-click="stopConfirm(false)"><i class="material-icons">&#xE5CD;</i></button></div>
            <div class="center"><button class="ok" ng-click="stopConfirm(true)"><i class="material-icons">&#xE876;</i></button></div>
        </font>
    </div>

    <div class="pop-image center ng-hide" ng-click="data.imagePop = false" ng-show="data.imagePop">
        <img ng-src="http://api.udp.cc/libs/image.php?idx={{ data.imageUrl }}" width="100%">
    </div>

    <div class="page" ng-view></div>

    <chosun-footer class="ng-hide backdrop-filter" ng-show="paging.url !== 'splash'">

        <div class="row tabs">
            <div class="col-{{ menus.length }} center{{ paging.url === item.href ? ' active' : '' }}"
                 ng-click="gomenu( $index )"
                 ng-repeat="item in menus">
                    <i class="material-icons" ng-bind-html="item.icon"></i>
            </div>
        </div>

        <div class="holder col-{{ menus.length }}" style="-webkit-transform: translateX( {{ config.menuIndex * ( 100 / menus.length ) }}vw ); transform: translateX( {{ config.menuIndex * ( 100 / menus.length ) }}vw );"></div>

    </chosun-footer>

    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular-route.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular-touch.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular-animate.min.js"></script>
    <!--script src="https://player.twitch.tv/js/embed/v1.js"></script-->
    <script src="libs/chart/chartist.min.js"></script>
    <script src="js/public/elastic-textarea.js"></script>
    <script src="js/public/util.js?v=<?php echo $ver; ?>"></script>
    <script src="js/public/upload.js"></script>
    <script src="js/public/fastclick.js"></script>

    <script src="js/timetable/whole.js?v=<?php echo $ver; ?>"></script>
    <script src="js/timetable/whole2.js?v=<?php echo $ver; ?>"></script>
    <script src="js/timetable/list.js?v=<?php echo $ver; ?>"></script>
    <script src="js/timetable/settings.js?v=<?php echo $ver; ?>"></script>
    <script src="js/timetable/detail.js?v=<?php echo $ver; ?>"></script>

    <script src="js/board/calendar.js?v=<?php echo $ver; ?>"></script>
    <script src="js/board/board.js?v=<?php echo $ver; ?>"></script>

    <script src="js/stock/list.js?v=<?php echo $ver; ?>"></script>
    <script src="js/stock/buy.js?v=<?php echo $ver; ?>"></script>
    <script src="js/game/graph.js?v=<?php echo $ver; ?>"></script>

    <script src="js/app.js?v=<?php echo $ver; ?>"></script>
</body>
</html>
