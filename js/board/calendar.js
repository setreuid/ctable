var calendar = angular.module('calendar', []);


/**
 * 시간표 요일 뷰
 *
 * @author 조태호
 */
calendar.controller( 'CalendarCtrl', function( $scope, $sce, $location,
    $http, $rootScope, $timeout, $anchorScroll )
{
    $scope.data =  {
        dayOfday: 0,
        useBack: false,
        caltable: [],
        isFirstLoaded: false
    };

    /**
     * 페이지 로드
     *
     * @author 조태호
     */
    $scope.$on('$viewContentLoaded', function()
    {
        if( $scope.data.caltable.length > 0 || $scope.data.isFirstLoaded )
        {
            return;
        }
        $scope.data.isFirstLoaded = true;

        doLoading();
        $rootScope.httpRequestToServer(
            $rootScope.config.externalUrls.calendar,
            $rootScope.config.custcd,
            '',
            {
                year: $rootScope.config.year
            },
            function( data ) {
                $timeout( function() {
                    $scope.data.caltable = data.data;

                    var today = new Date();
                    var year = today.getFullYear();
                    var month = today.getMonth() + 1;

                    month = month < 10 ? '0' + month : month;

                    $timeout( function() {
                        $location.hash( 'c' + year + '.' + month );
                        $anchorScroll();
                        stopLoading();
                    }, 600);
                });
            }
        );
    });
});
