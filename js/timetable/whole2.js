var whole2 = angular.module('whole2', []);


/**
 * 시간표 전체 뷰
 *
 * @author 조태호
 */
whole2.controller( 'TimeTableWhole2Ctrl', function( $scope, $sce, $location,
    $http, $rootScope, $timeout )
{
    $scope.data =  {
        dayCount: 7,
        dayNames: [ '월', '화', '수', '목', '금', '토', '일' ],
        visibleTimes: [],
        timetable: [],
        styleHeight: '',
        minused: false,
        hourtable: [
            '08:00',
            '09:00',
            '10:00',
            '11:00',
            '12:00',
            '13:00',
            '14:00',
            '15:00',
            '16:00',
            '17:00',
            '18:00',
            '18:55',
            '19:50',
            '20:45',
            '21:40'
        ],
        hourtable2: [
            '08:00',
            '09:00',
            '10:30',
            '12:00',
            '13:30',
            '15:00',
            '16:30',
            '18:00',
            '19:30',
            '21:00',
            '22:30',
            '18:55'
        ],
        _hourtable: [
            '8:50',
            '9:50',
            '10:50',
            '11:50',
            '12:50',
            '13:50',
            '14:50',
            '15:50',
            '16:50',
            '17:50',
            '18:50',
            '19:45',
            '20:40',
            '21:35',
            '22:30'
        ],
        _hourtable2: [
            '8:50',
            '10:15',
            '11:45',
            '13:15',
            '14:45',
            '16:15',
            '17:45',
            '19:15',
            '20:45',
            '22:15',
            '23:45',
            '19:45'
        ]
    };

    $scope.showDetail = function( item )
    {
        if( item.curi_num !== '' )
            $rootScope.gohref( "/detail/" + item.shortname + "/" + item.curi_num );
    }

    $scope.getClassTime = function( time, index )
    {
        // console.log( time + ' - ' + index );
        return time - 8 <= 0 || index > 5 ? time + 4 : time - 8;
    }

    $scope.round = function( val )
    {
        return Math.round( val );
    }

    /**
     * 페이지 로드
     *
     * @author 조태호
     */
    $scope.$on('$viewContentLoaded', function()
    {
        $scope.data.dayCount = 7;

        $timeout( function()
        {
            if( !$rootScope.config.useThr )
            {
                $scope.data.dayCount--;
            } //  토요일을 보여줍니까?

            if( !$rootScope.config.useSat )
            {
                $scope.data.dayCount--;
            } //  일요일을 보여줍니까?

            if( !$rootScope.config.useMon )
            {
                $scope.data.dayCount--;
            } //  월요일을 보여줍니까?

            if( !$rootScope.config.useTue )
            {
                $scope.data.dayCount--;
            } //  화요일을 보여줍니까?

            if( !$rootScope.config.useWed )
            {
                $scope.data.dayCount--;
            } //  수요일을 보여줍니까?

            if( !$rootScope.config.useThu )
            {
                $scope.data.dayCount--;
            } //  목요일을 보여줍니까?

            if( !$rootScope.config.useFri )
            {
                $scope.data.dayCount--;
            } //  금요일을 보여줍니까?

            $scope.data.timetable = $rootScope.getTimeTable();
            $scope.data.visibleTimes = $rootScope.data.visibleTimes;

            $scope.data.styleHeight = 100/$scope.data.timetable.length + 'vh - ' + ( navigator.userAgent.indexOf('iPhone') > -1 ? 162 : 142 ) / $scope.data.timetable.length + 'px';
            // $scope.data.styleHeight = 100/$scope.data.timetable.length + 'vh - ' + (143 / $scope.data.timetable.length) + 'px';
            $rootScope.config.heightC = $rootScope.config.showOneSee ? ( window.innerHeight - 143 ) / $scope.data.timetable.length : 72;
        });
    });
});
