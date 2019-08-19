var gamegraph = angular.module( 'gamegraph', []);

gamegraph.controller( 'GameGraphCtrl', function( $scope, $sce, $location,
    $http, $rootScope, $timeout, $routeParams, $interval, upload )
{
    $scope.data = {
        title: '소셜그래프미니게임',
        stdate: '20170731',
        isLoaded: false,
        round: 0,
        stTime: 0,
        edTime: 0,
        info: {},
        chart: undefined,
        chartData: {
            labels: [],
            series: [
                []
            ]
        },
        interval: undefined,
        size: 1.0,
        gtime: 0,       // 10회에 그래프 1회 그리기
        bet: 50000,
        betted: 0,
        state: 0,       // 0:배팅 1:게임중 2:정산중
        wait: 0,        // 게임이 시작하기전 베팅 기다리는 시간 5초
        fwait: 0        // 게임이 끝나면 3초 기다림
    }

    $scope.$on('$viewContentLoaded', function()
    {
        api( 'STOCK', 'MYINFO', {
            user: $rootScope.config.account.uid
        }, function( data )
        {
            $timeout( function() {
                $scope.data.info = data.data;
                $scope.data.isLoaded = true;
                $scope.initializeGraph();
            });
        });
    });

    $scope.$on('$destroy', function() {
        $scope.data.isLoaded = false;
        $scope.pauseGame();
    });

    $scope.doBet = function()
    {
        if( $scope.data.betted > 0 )
        {
            Alert( '이미 배팅하셨습니다.' );
            return;
        }

        if( $scope.data.bet > $scope.data.info.money )
        {
            Alert( '보유 현금이 부족합니다!' );
            return;
        }

        if( $scope.data.bet < 1 )
        {
            Alert( '금액을 입력하세요!' );
            return;
        }

        if( $scope.data.bet > 300000 )
        {
            Alert( '30만원 이상 베팅은 안됩니다!' );
            return;
        }

        $scope.data.betted = $scope.data.bet;
        $scope.data.info.money -= $scope.data.bet;

        $scope.updateInfomation();
    }

    $scope.replaceF = function( str )
    {
        if( str == 0 ) return 0;

        var reg = /(^[+-]?\d+)(\d{3})/;
        var n = (( str * 1 ).toFixed(0) + '' );

        while (reg.test(n)) n = n.replace(reg, '$1' + ',' + '$2');

        return n;
    }

    $scope.countsHandle = function()
    {
        if( $scope.data.bet > 300000 )
        {
            Alert( '최대 배팅액수(30만)를 넘었습니다!' );
            $timeout( function() {
                document.activeElement.blur();
                $scope.data.bet = 300000;
            });
        }
    }

    $scope.focusedCountsHandle = function()
    {
        $timeout( function() {
            $scope.data.bet = '';
        });
    }

    $scope.pauseGame = function() {
        $scope.data.betted = 0;
        $interval.cancel( $scope.data.interval );
        $scope.data.interval = undefined;
    }

    $scope.initializeGraph = function()
    {
        $scope.data.chart = new Chartist.Line( '.ct-chart', $scope.data.chartData, {
            showPoint: false,
            fullWidth: true,
            showLabel: false,
            axisX: {
                showGrid: false,
                showLabel: false,
                offset: 0
            },
            axisY: {
                showGrid: false,
                showLabel: false,
                offset: 0
            },
            chartPadding: 5,
            low: 0,
            high: 1
        });

        $scope.resetGame();
    }

    $scope.waitTimer = function()
    {
        if( !$scope.data.isLoaded ) return;
        if( $scope.data.wait > 1 )
        {
            $scope.data.wait--;
            $timeout( $scope.waitTimer, 1000 );
            return;
        }

        $scope.runInterval();
    }

    $scope.fwaitTimer = function()
    {
        if( !$scope.data.isLoaded ) return;
        if( $scope.data.fwait > 0 )
        {
            $scope.data.fwait--;
            $timeout( $scope.fwaitTimer, 1000 );
            return;
        }

        $scope.resetGame();
    }

    $scope.resetGame = function()
    {
        $scope.data.wait = 6;
        $scope.data.state = 0;
        $scope.data.size = 1.0;
        $scope.data.chartData.labels = [];
        $scope.data.chartData.series[0] = [];

        $rootScope.httpRequestToServer( $rootScope.config.externalUrls.gamechart, '', '', {

        }, function( data ) {
            $timeout(function() {
                var thisTime = ( new Date() ).getTime();

                $scope.data.stTime = data.stTime;
                $scope.data.edTime = data.edTime;
                $scope.data.gtime = 11;

                if( $scope.data.stTime < thisTime )
                {
                    var loopKey = thisTime - $scope.data.stTime;
                    var idx = 0;
                    var tsize = 1.0;
                    var qsize = 0;

                    while( loopKey > idx )
                    {
                        qsize = 0;
                        var nextStep = Math.tan(( Math.PI / 2 / 500 ) * idx / 500 );
                        if( qsize < 1.5 ) { qsize += 0.0005; }
                        else if( qsize < 3 ) { qsize += 0.001; }
                        else if( qsize < 5 ) { qsize += 0.002; }
                        else if( qsize < 10 ) { qsize += 0.003; }
                        else if( qsize < 20 ) { qsize += 0.005; }
                        else { qsize += 0.008; }

                        if( $scope.data.gtime > 9 )
                            $scope.data.chartData.labels.push( tsize + ( 10000 - qsize * 20000 ));

                        tsize += qsize;

                        if( $scope.data.gtime > 9 )
                            $scope.data.chartData.series[0].push( nextStep );

                        idx += 10;
                        $scope.data.gtime++;
                        if( $scope.data.gtime > 10 )
                        {
                            $scope.data.gtime = 0;
                        }
                    }

                    $scope.data.size = tsize;
                    $scope.data.chart.update( $scope.data.chartData );

                    $scope.runInterval();
                }
                else
                {
                    $scope.data.wait = (( $scope.data.stTime - thisTime ) / 1000 ).toFixed(0) * 1;
                    $scope.waitTimer();
                }
            });
        });
    }

    $scope.runInterval = function()
    {
        if( angular.isDefined( $scope.data.interval )) return;
        $scope.data.state = 1;
        $scope.data.interval = $interval( $scope.playGame, 50 );
    }

    $scope.sizeStr = function()
    {
        var size = $scope.data.size.toFixed( 2 );
        return $scope.data.state == 0 ? $scope.data.wait + '초 후 게임이 시작됩니다' : ( $scope.data.state == 1 ? size + 'x' : '@' + size + 'x' );
    }

    $scope.playGame = function()
    {
        if( !angular.isDefined( $scope.data.chart )) return;
        if( !$scope.data.isLoaded ) return;

        var thisTime = ( new Date() ).getTime();
        var nextStep = Math.tan(( Math.PI / 2 / 500 ) * ( thisTime - $scope.data.stTime ) / 500 );
        var tsize = 0.0005;

        if( $scope.data.size < 1.5 ) { tsize += 0.0005; }
        else if( $scope.data.size < 3 ) { tsize += 0.001; }
        else if( $scope.data.size < 5 ) { tsize += 0.002; }
        else if( $scope.data.size < 10 ) { tsize += 0.003; }
        else if( $scope.data.size < 20 ) { tsize += 0.005; }
        else { tsize += 0.008; }

        if( $scope.data.gtime > 9 )
            $scope.data.chartData.labels.push( $scope.data.size + ( 10000 - tsize * 20000 ));

        $scope.data.size += tsize;

        if( $scope.data.gtime > 9 )
            $scope.data.chartData.series[0].push( nextStep );

        $scope.data.gtime++;
        if( $scope.data.gtime > 10 ) $scope.data.gtime = 0;

        $scope.data.chart.update( $scope.data.chartData );


        // *******************************************************************//
        // Game Reset
        if( thisTime > $scope.data.edTime )
        {
            $scope.data.state = 2;
            $scope.data.fwait = 3;
            $scope.pauseGame();
            $scope.fwaitTimer();
        }
    }

    // ***********************************************************************//
    // 게임 정산
    $scope.doCommit = function()
    {
        if( $scope.data.betted == 0 )
        {
            Alert( '게임 시작전에 배팅을 하셔야 합니다!' );
            return;
        }

        $scope.data.info.money += ( $scope.data.betted * $scope.data.size ).toFixed(0) * 1;
        $scope.data.betted = 0;
        $scope.updateInfomation();
    }

    $scope.updateInfomation = function()
    {
        if( !$scope.data.isLoaded ) return;

        api( 'STOCK', 'UPD', {
            user: $rootScope.config.account.uid,
            key: $scope.data.info.money
        }, function( data )
        {
            //
        });
    }
});
