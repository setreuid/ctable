var detail = angular.module('detail', []);


/**
 * 시간표 전체 뷰
 *
 * @author 조태호
 */
detail.controller( 'DetailCtrl', function( $scope, $sce, $location,
    $http, $rootScope, $timeout, $templateCache, $routeParams, $interval )
{
    $scope.data = {
        useBack: false,
        title: '',
        curi_num: '',
        contents: ''
    };

    $scope.gotoBottom = function()
    {
        if ( angular.isDefined( $scope.data.interval ) ) return;

        var nowScrollTop = document.querySelector('chosun-contents').scrollTop;
        var targetScrollTop = document.querySelector('.contents').clientHeight;
        var tok = targetScrollTop / 10;

        $scope.data.interval = $interval(function() {
            nowScrollTop += tok;
            document.querySelector('chosun-contents').scrollTop = nowScrollTop;

            if( nowScrollTop >= targetScrollTop )
            {
                $scope.stopScroll();
            }
            console.log( "INTERVAL" );
        }, 100 );
    }

    $scope.stopScroll = function()
    {
        $interval.cancel( $scope.data.interval );
        $scope.data.interval = undefined;
    }

    /**
     * 페이지 로드
     *
     * @author 조태호
     */
    $scope.$on('$viewContentLoaded', function()
    {
        doLoading();

        $scope.data.title = $routeParams.shortname;
        $scope.data.curi_num = $routeParams.curi_num;

        $rootScope.httpRequestToServer(
            $rootScope.config.externalUrls.detail,
            $scope.data.curi_num,
            $rootScope.config.class,
            {
                year: $rootScope.config.year
            },
            function( data ) {
                if( data.result === 'success' )
                {
                    var res = "";

                    for( var i in data.data  )
                    {
                        if( i < 9 ) { res += '<span>' + data.data[i] + '</span>' }
                        else if( i === 9 ) { res += '<span>' + data.data[i] }
                        else {
                            res += data.data[i] + "<BR>";
                        }
                    }

                    $timeout(function() {
                        $scope.data.contents = $sce.trustAsHtml( res + '</span>' );
                        stopLoading( 400 );
                    });
                }
            }
        );
    });
});
