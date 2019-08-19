var stocklist = angular.module( 'stocklist', []);

stocklist.controller( 'StockListCtrl', function( $scope, $sce, $location,
    $http, $rootScope, $timeout, $routeParams, $interval, upload )
{
    $scope.data = {
        title: '모의주식 미니게임',
        mode: 0,
        myinfo: {},
        modeList: [ '코스피', '코스닥', '내 주식', '랭킹', '상점' ],
        storeList: [],
        source: [],
        searchQuery: '',
        now: (new Date()).getTime()
    }

    $scope.$on('$viewContentLoaded', function()
    {
        $routeParams.focus = $routeParams.focus || 0;
        $scope.viewMode( $routeParams.focus );
    });

    $scope.doStoreProc = function( storeItem )
    {
        if( storeItem.handle ) storeItem.handle();
    }

    $scope.resetConfirm = function()
    {
        Confirm( '파산신청은 되돌릴수 없습니다<BR>진행할까요?', function( data )
        {
            if( data )
            {
                api( 'STOCK', 'RESET', {
                    user: $rootScope.config.account.uid
                }, function( data ) {
                    if( data.result === 'success' )
                    {
                        Alert( '초기화가 완료되었습니다' );
                        $rootScope.gohref( '/stocklist/2' );
                    }
                });
            }
        });
    }

    $scope.viewMode = function( index )
    {
        $scope.data.mode = index;

        if( index == 2 )
        {
            // 내 정보
            $scope.loadMyInfo();
            $scope.loadMySources();
        }
        else if( index == 3 )
        {
            // 랭킹
            $scope.loadRankSources();
        }
        else if( index == 4 )
        {
            // 상점
        }
        else if( index < 2 )
        {
            // 주식 정보
            $scope.data.searchQuery = '';
            $scope.loadSources();
        }
    }

    $scope.floor = function( str )
    {
        if( !str ) return '0';
        return Math.floor( str ).format();
    }

    $scope.clearSearchQuery = function()
    {
        $scope.data.searchQuery = ''
        $scope.viewMode( $scope.data.mode );
    }

    $scope.replaceN = function( str )
    {
        if( !str ) return '0';
        return str.replace( /&nbsp;/ig, '▲' );
    }

    $scope.replaceK = function( str )
    {
        if( !str ) return '0';
        return Math.round( str / 1000 ).format() + 'k';
    }

    $scope.replaceF = function( str )
    {
        if( !str ) return '0';
        return str.format();
    }

    $scope.loadRankSources = function()
    {
        api( 'STOCK', 'RANK', {},
        function( data )
        {
            $timeout( function() {
                $scope.data.source = data.data;
                $scope.data.now = (new Date()).getTime();
            });
        });
    }

    $scope.loadSources = function()
    {
        api( 'STOCK', 'LIVE', {
            flag: $scope.data.mode +1,
            token: $scope.data.searchQuery
        }, function( data )
        {
            $timeout( function() {
                $scope.data.source = data.data;
                $scope.data.now = (new Date()).getTime();
            });
        });
    }

    $scope.loadMySources = function()
    {
        api( 'STOCK', 'MYLIVE', {
            user: $rootScope.config.account.uid
        }, function( data )
        {
            $timeout( function()
            {
                if( data.data )
                    for( var x=0 ; x<data.data.length ; x++ )
                    {
                        data.data[x].subcost = Math.abs( Math.floor(( data.data[x].cost * data.data[x].counts - data.data[x].stcost ) / data.data[x].counts )).format();
                        data.data[x].subcosttotal = Math.abs( data.data[x].cost * data.data[x].counts - data.data[x].stcost ).format();
                        data.data[x].subissue = data.data[x].cost * data.data[x].counts > data.data[x].stcost;
                    }

                $scope.data.source = data.data;
                $scope.data.now = (new Date()).getTime();
            });
        });
    }

    $scope.loadMyInfo = function()
    {
        api( 'STOCK', 'MYINFO', {
            user: $rootScope.config.account.uid
        }, function( data )
        {
            $timeout( function() {
                $scope.data.myinfo = data.data;
            });
        });
    }

    $scope.focusMe = function( ele )
    {
        ele.target.focus();
    }

    $scope.goSGraphGame = function()
    {
        $rootScope.gohref( '/gamegraph' );
    }

    $scope.data.storeList = [
        // { title: '내이름을 불러줘', memo: '닉네임을 설정합니다', cost: 1000000, handle: null },
        { title: '파산신청', memo: '즉시 자산을 처음으로 되돌립니다', cost: 0, handle: $scope.resetConfirm },
        { title: '소셜그래프게임', memo: '누르면 되돌릴수 없습니다...', cost: 0, handle: $scope.goSGraphGame }
    ];
});
