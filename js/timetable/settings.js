var settings = angular.module('settings', []);


/**
 * 시간표 전체 뷰
 *
 * @author 조태호
 */
settings.controller( 'SettingsCtrl', function( $scope, $sce, $location,
    $http, $rootScope, $timeout, $templateCache, $route )
{
    $scope.data = {
        class: '',
        class10: false,
        class11: false,
        class20: false,
        class21: false
    };

    $scope.saveConfigFor = function( idx )
    {
        switch( idx )
        {
            case 0:
                $rootScope.config.useThr = !$rootScope.config.useThr;
            break;

            case 1:
                $rootScope.config.useSat = !$rootScope.config.useSat;
            break;

            case 2:
                $rootScope.config.useMon = !$rootScope.config.useMon;
            break;

            case 3:
                $rootScope.config.useTue = !$rootScope.config.useTue;
            break;

            case 4:
                $rootScope.config.useWed = !$rootScope.config.useWed;
            break;

            case 5:
                $rootScope.config.useThu = !$rootScope.config.useThu;
            break;

            case 6:
                $rootScope.config.useFri = !$rootScope.config.useFri;
            break;

            default:

        }

        $timeout( function() {
            window.localStorage['usethr'] = $rootScope.config.useThr;
            window.localStorage['usesat'] = $rootScope.config.useSat;
            window.localStorage['usemon'] = $rootScope.config.useMon;
            window.localStorage['usetue'] = $rootScope.config.useTue;
            window.localStorage['usewed'] = $rootScope.config.useWed;
            window.localStorage['usethu'] = $rootScope.config.useThu;
            window.localStorage['usefri'] = $rootScope.config.useFri;
        }, 300 );
    }

    $scope.updateSwitches = function( data )
    {
        switch( data )
        {
            case '10':

                $scope.data = {
                    class10: true,
                    class11: false,
                    class20: false,
                    class21: false
                };

            break;

            case '11':

                $scope.data = {
                    class10: false,
                    class11: true,
                    class20: false,
                    class21: false
                };

            break;

            case '20':

                $scope.data = {
                    class10: false,
                    class11: false,
                    class20: true,
                    class21: false
                };

            break;

            case '21':

                $scope.data = {
                    class10: false,
                    class11: false,
                    class20: false,
                    class21: true
                };

            break;
        }
    }

    $scope.setClass = function( data )
    {
        $timeout( function()
        {
            $scope.updateSwitches( data );

            window.localStorage['class'] = data;
            $scope.data.class = $rootScope.config.class = data;
            $rootScope.initTimeTable( data );
        });
    }

    $scope.logoutCurrentAccount = function()
    {
        $timeout( function()
        {
            $rootScope.config.account.uid = '';
            $rootScope.config.account.upw = '';

            window.localStorage['uid'] = '';
            window.localStorage['upw'] = '';

            $rootScope.gohref( 'splash' );
        });
    }

    $scope.updateSystem = function()
    {
        $templateCache.removeAll();
        window.location.reload( true );
    }

    $scope.toggleConfigArea = function()
    {
        $timeout(function() {
            $rootScope.config.viewArea = !$rootScope.config.viewArea;
            window.localStorage['viewarea'] = $rootScope.config.viewArea;
        });
    }

    $scope.toggleVisibleTime = function()
    {
        $timeout(function() {
            $rootScope.config.showVisibleTime = !$rootScope.config.showVisibleTime;
            window.localStorage['showvisibletime'] = $rootScope.config.showVisibleTime;
        });
    }

    $scope.toggleOneSee = function()
    {
        $timeout(function() {
            $rootScope.config.showOneSee = !$rootScope.config.showOneSee;
            window.localStorage['showonesee'] = $rootScope.config.showOneSee;
        });
    }

    $scope.toggleLongRoom = function()
    {
        $timeout(function() {
            $rootScope.config.showLongRoom = !$rootScope.config.showLongRoom;
            window.localStorage['showlongroom'] = $rootScope.config.showLongRoom;
        });
    }

    $scope.toggleUseNew01 = function()
    {
        $timeout(function() {
            $rootScope.config.usenew01 = !$rootScope.config.usenew01;
            window.localStorage['usenew01'] = $rootScope.config.usenew01;
        });
    }

    $scope.reloadTimeTable = function()
    {
        refreshTimeTable( function( data ) {
            if( data.result === 'success' )
            {
                api( 'TIMETABLE', 'SAVE', {
                    perid: $rootScope.getCurrentAccount().uid,
                    custcd: $rootScope.config.custcd,
                    year: $rootScope.config.year,
                    class: $rootScope.config.class,
                    data: JSON.stringify( data.data )
                });

                $rootScope.calcTimeTable( data.data, function() {
                    $rootScope.gomenu(1);
                });
            } else
                Alert( '패스워드를 혹시 바꾸셨나요? 로그아웃후 재시도 해주세요' );
        });
    }

    /**
     * 페이지 로드
     *
     * @author 조태호
     */
    $scope.$on('$viewContentLoaded', function()
    {
        $timeout( function()
        {
            $scope.data.class = $rootScope.config.class;
            $scope.updateSwitches( $scope.data.class );
        });
    });
});
