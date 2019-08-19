var stockbuy = angular.module( 'stockbuy', []);

stockbuy.controller( 'StockBuyCtrl', function( $scope, $sce, $location,
    $http, $rootScope, $timeout, $routeParams, $interval, upload )
{
    $scope.data = {
        title: '모의주식 미니게임',
        info: false,
        mode: 0,
        code: '',
        modeList: [ '매수', '매도' ],
        counts: ''
    }

    $scope.$on('$viewContentLoaded', function()
    {
        $scope.data.code = $routeParams.code;
        $scope.viewMode( $routeParams.type );
    });

    $scope.loadDetail = function()
    {
        api( 'STOCK', 'DETAIL_BUY', {
            user: $rootScope.config.account.uid,
            code: $scope.data.code
        }, function( data )
        {
            $timeout( function() {
                $scope.data.info = data.data[0];
            });
        });
    }

    $scope.loadDetailForPay = function()
    {
        api( 'STOCK', 'DETAIL_PAY', {
            user: $rootScope.config.account.uid,
            code: $scope.data.code
        }, function( data )
        {
            $timeout( function() {
                if( !data.data )
                {
                    Alert( '내가 가진 이 종목의 주식이 없습니다!' );
                    $scope.viewMode( 0 );
                    return;
                }

                $scope.data.info = data.data[0];
            });
        });
    }

    $scope.countsHandle = function()
    {
        if( $scope.data.mode == 0 && $scope.data.counts > Math.floor( $scope.data.info.money / $scope.data.info.cost ))
        {
            Alert( '최대 매수 가능주 수를 넘었습니다!' );
            $timeout( function() {
                document.activeElement.blur();
                $scope.data.counts = Math.floor( $scope.data.info.money / $scope.data.info.cost ) || '';
            });
        }

        if( $scope.data.mode == 1 && $scope.data.counts > $scope.data.info.counts )
        {
            Alert( '최대 매도 가능주 수를 넘었습니다!' );
            $timeout( function() {
                document.activeElement.blur();
                $scope.data.counts = $scope.data.info.counts || '';
            });
        }
    }

    $scope.commitBuy = function()
    {
        if( !$scope.data.counts )
        {
            Alert( '매수할 주식수를 입력해주세요!' );
            return;
        }

        api( 'STOCK', 'DETAIL_BUY', {
            user: $rootScope.config.account.uid,
            code: $scope.data.code
        }, function( data )
        {
            $timeout( function() {
                $scope.data.info = data.data[0];

                if( $scope.data.counts > Math.floor( $scope.data.info.money / $scope.data.info.cost ))
                {
                    Alert( '최대 매수 가능주 수를 넘었습니다!' );
                    $timeout( function() {
                        document.activeElement.blur();
                        $scope.data.counts = Math.floor( $scope.data.info.money / $scope.data.info.cost ) || '';
                    });
                }
                else
                {
                    api( 'STOCK', 'BUY_NOW', {
                        user: $rootScope.config.account.uid,
                        code: $scope.data.code,
                        counts: $scope.data.counts
                    }, function( data )
                    {
                        if( data.result == 'error' )
                        {
                            Alert( '매수에 실패하였습니다!' );
                            $scope.loadDetail();
                        }
                        else
                        {
                            Alert( '성공적으로 매수하였습니다' );
                            $rootScope.gohref( '/stocklist/2' );
                        }
                    });
                }
            });
        });
    }

    $scope.commitPay = function()
    {
        if( !$scope.data.counts )
        {
            Alert( '매도할 주식수를 입력해주세요!' );
            return;
        }

        api( 'STOCK', 'DETAIL_PAY', {
            user: $rootScope.config.account.uid,
            code: $scope.data.code
        }, function( data )
        {
            $timeout( function()
            {
                if( !data.data )
                {
                    Alert( '매도 가능한 주가 없습니다!' );
                    $rootScope.goBack();
                    return;
                }

                $scope.data.info = data.data[0];
                if( $scope.data.counts > $scope.data.info.counts )
                {
                    Alert( '최대 매도 가능주 수를 넘었습니다!' );
                    $timeout( function() {
                        document.activeElement.blur();
                        $scope.data.counts = $scope.data.info.counts || '';
                    });
                }
                else
                {
                    api( 'STOCK', 'PAY_NOW', {
                        user: $rootScope.config.account.uid,
                        code: $scope.data.code,
                        counts: $scope.data.counts
                    }, function( data )
                    {
                        if( data.result == 'error' )
                        {
                            Alert( '매도에 실패하였습니다!' );
                            $scope.loadDetailForPay();
                        }
                        else
                        {
                            Alert( '성공적으로 매도하였습니다' );
                            $rootScope.gohref( '/stocklist/2' );
                        }
                    });
                }
            });
        });
    }

    $scope.focusedCountsHandle = function()
    {
        $timeout( function() {
            $scope.data.counts = '';
        });
    }

    $scope.floor = function( num )
    {
        return Math.floor( num ).format();
    }

    $scope.replaceN = function( str )
    {
        return str.replace( /&nbsp;/ig, '▲' );
    }

    $scope.replaceK = function( str )
    {
        return Math.round( str / 1000 ).format() + 'k';
    }

    $scope.replaceF = function( str )
    {
        return str.format();
    }

    $scope.viewMode = function( index )
    {
        $scope.data.mode = index;

        if( index == 0 )
        {
            $scope.loadDetail();
        }
        else
        {
            $scope.loadDetailForPay();
        }
    }
});
