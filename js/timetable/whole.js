var whole = angular.module('whole', []);


/**
 * 시간표 전체 뷰
 *
 * @author 조태호
 */
whole.controller( 'TimeTableWholeCtrl', function( $scope, $sce, $location,
    $http, $rootScope, $timeout )
{
    $scope.data =  {
        dayCount: 7,
        dayNames: [ '월', '화', '수', '목', '금', '토', '일' ],
        visibleTimes: [],
        timetable: [],
        styleHeight: '',
        minused: false
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
        });
    });
});
