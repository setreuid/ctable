var daytable = angular.module('daytable', []);


/**
 * 시간표 요일 뷰
 *
 * @author 조태호
 */
daytable.controller( 'DayTableCtrl', function( $scope, $sce, $location,
    $http, $rootScope, $timeout )
{
    $scope.data =  {
        title: '',
        dayOfday: 0,
        useBack: false,
        daynames: [ '월', '화', '수', '목', '금', '토', '일' ],
        daytable: [],
        /*
            - 0교시 : 08:00~8:50
            - 1교시 : 09:00~9:50
            - 2교시 : 10:00~10:50
            - 3교시 : 11:00~11:50
            - 4교시 : 12:00~12:50
            - 5교시 : 13:00~13:50
            - 6교시 : 14:00~14:50
            - 7교시 : 15:00~15:50
            - 8교시 : 16:00~16:50
            - 9교시 : 17:00~17:50
            - 10교시 : 18:00~18:50
            - 11교시 : 18:55~19:45
            - 12교시 : 19:50~20:40
            - 13교시 : 20:45~21:35
            - 14교시 : 21:40~22:30
         */
        hourtable: [
            '08:00 ~ 8:50',
            '09:00 ~ 9:50',
            '10:00 ~ 10:50',
            '11:00 ~ 11:50',
            '12:00 ~ 12:50',
            '13:00 ~ 13:50',
            '14:00 ~ 14:50',
            '15:00 ~ 15:50',
            '16:00 ~ 16:50',
            '17:00 ~ 17:50',
            '18:00 ~ 18:50',
            '18:55 ~ 19:45',
            '19:50 ~ 20:40',
            '20:45 ~ 21:35',
            '21:40 ~ 22:30'
        ],
        hourtable2: [
            '08:00 ~ 8:50',
            '09:00 ~ 10:15',
            '10:30 ~ 11:45',
            '12:00 ~ 13:15',
            '13:30 ~ 14:45',
            '15:00 ~ 16:15',
            '16:30 ~ 17:45',
            '18:00 ~ 19:15',
            '19:30 ~ 20:45',
            '21:00 ~ 22:15',
            '22:30 ~ 23:45',
            '18:55 ~ 19:45'
        ]
    };

    $scope.filterCuriNum = function(v)
    {
        return v.curi_num !== '';
    }

    $scope.gotoBoard = function( item )
    {
        $rootScope.gohref( '/board/' + item.emp_nm + '' + item.curi_num.substring(0, item.curi_num.indexOf('-')) + '/' + item.shortname + ' 게시판' );
    }

    $scope.showDetail = function( item )
    {
        if( item.curi_num !== '' )
            $rootScope.gohref( "/detail/" + item.shortname + "/" + item.curi_num );
    }

    $scope.viewDay = function( index )
    {
        $timeout( function() {
            // TODO: 오늘에 해당하는 일자의 시간표가 나와야 함
            $scope.data.dayOfday = index;
            $scope.data.daytable = $rootScope.getDayTable( index ).data;
            $scope.data.title = $rootScope.getDayTable( index ).name;

            document.querySelector('chosun-contents').scrollTop = 0;
        });
    }

    $scope.swipeDay = function( isLeft )
    {
        if( isLeft )
        {
            $scope.data.dayOfday--;
            if( $scope.data.dayOfday < 0 ) {
                $scope.data.dayOfday = 6;
            }
        }
        else
        {
            $scope.data.dayOfday++;
            if( $scope.data.dayOfday > 6 ) {
                $scope.data.dayOfday = 0;
            }
        }

        $timeout( function() {
            // TODO: 오늘에 해당하는 일자의 시간표가 나와야 함
            $scope.data.daytable = $rootScope.getDayTable( $scope.data.dayOfday ).data;
            $scope.data.title = $rootScope.getDayTable( $scope.data.dayOfday ).name;

            document.querySelector('chosun-contents').scrollTop = 0;
        });
    }

    /**
     * 페이지 로드
     *
     * @author 조태호
     */
    $scope.$on('$viewContentLoaded', function()
    {
        var today = new Date();
        var dayOfday = today.getDay() - 1;

        if( dayOfday < 0 ) dayOfday = 6;

        $timeout( function() {
            // TODO: 오늘에 해당하는 일자의 시간표가 나와야 함
            $scope.data.dayOfday = dayOfday;
            $scope.data.daytable = $rootScope.getDayTable( dayOfday ).data;
            $scope.data.title = $rootScope.getDayTable( dayOfday ).name;

            document.querySelector('chosun-contents').scrollTop = 0;
        });
    });
});
