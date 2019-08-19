var board = angular.module('board', []);


/**
 * 게시판
 *
 * @author 조태호
 */
board.controller( 'BoardCtrl', function( $scope, $sce, $location,
    $http, $rootScope, $timeout, $routeParams, $interval, upload )
{
    $scope.data =  {
        board_idx: 0,
        title: '',
        contents: '',
        page: -1,
        lastid: 0,
        isLoading: true,
        enablePlayer: false,
        player: null,
        documents: [],
        online: 0
    };

    $scope.textInputFocus = function()
    {
        document.getElementById('text-input-1').focus();
    }

    $scope.confirmDocument = function()
    {
        if( $scope.data.contents.length < 4 )
        {
            Alert( '4글자 이상 입력해주세요' );
            return;
        }

        doLoading();
        api( 'BOARD', 'NEW', {
            perid: $rootScope.getCurrentAccount().uid,
            custcd: $rootScope.config.custcd,
            board_idx: $scope.data.board_idx,
            data: $scope.data.contents
        }, function( data ) {
            if( data.result === 'success' )
            {
                $scope.procLast();
            }
            $scope.data.contents = '';
            stopLoading();
        });
    }

    $scope.prepareUpload = function( obj )
    {
        doLoading();
        api( 'BOARD', 'NEW', {
            perid: $rootScope.getCurrentAccount().uid,
            custcd: $rootScope.config.custcd,
            board_idx: $scope.data.board_idx,
            data: 'IMAGE'
        }, function( data ) {
            if( data.result === 'success' )
            {
                $scope.doUpload( data.data, obj );
            }
        });
    }

    $scope.doUpload = function( bidx, img )
    {
        upload({
                url: '../../libs/board/upload_document.php',
                method: 'POST',
                data: {
                    board_idx: bidx,
                    upload: angular.element(document.getElementById("imageUpload"))
                }
            }).then(
            function (response) {
                console.log(response.data); // will output whatever you choose to return from the server on a successful upload
                $scope.procLast();
                stopLoading();
            },
            function (response) {
                console.error(response); //  Will return if status code is above 200 and lower than 300, same as $http

                $scope.data.isLoading = true;
                api( 'BOARD', 'REMOVE', {
                    id: bidx
                }, function( data ) {
                    $scope.data.isLoading = false;
                    stopLoading();
                });

                Alert( response.data.error.message );
            }
        );
    }

    $scope.removeMessage = function( item, index )
    {
        if( item.perid !== ' my' && $rootScope.getCurrentAccount().uid !== '20114764' ) { return };

        // console.log( item.id );

        Confirm( '이걸 삭제할까요?', function( data ) {
            if( data )
            {
                doLoading();
                $scope.data.isLoading = true;
                api( 'BOARD', 'REMOVE', {
                    id: item.id
                }, function( data ) {
                    if( data.result === 'success' )
                    {
                        $timeout(function() {
                            $scope.data.documents.splice( index, 1 );
                        });
                    }
                    $scope.data.isLoading = false;
                    stopLoading();
                });
            }
        });
    }

    $scope.good = function( item, index )
    {
        $scope.data.isLoading = true;
        api( 'BOARD', 'LIKE', {
            perid: $rootScope.getCurrentAccount().uid,
            custcd: $rootScope.config.custcd,
            id: item.id,
            like: (item.islike == 1 ? 0 : 1)
        }, function( data ) {
            if( data.result === 'success' )
            {
                item.good = data.data.good;
                item.bad = data.data.bad;
                item.islike = data.data.islike;

                item = $scope.calcDetail(item);
            }
            $scope.data.isLoading = false;
        });
    }

    $scope.bad = function( item, index )
    {
        $scope.data.isLoading = true;
        api( 'BOARD', 'LIKE', {
            perid: $rootScope.getCurrentAccount().uid,
            custcd: $rootScope.config.custcd,
            id: item.id,
            like: (item.islike == 2 ? 0 : 2)
        }, function( data ) {
            if( data.result === 'success' )
            {
                item.good = data.data.good;
                item.bad = data.data.bad;
                item.islike = data.data.islike;

                item = $scope.calcDetail(item);
            }
            $scope.data.isLoading = false;
        });
    }

    $scope.getContent = function(item)
    {
        if (item.open)
        {
            return item.data;
        }
        else
        {
            return item.predata;
        }
    }

    $scope.loadBroadCast = function()
    {
        if( $scope.data.board_idx == '0' )
        {
            api( 'BOARD', 'ONAIR', {}, function( data ) {
                var result = JSON.parse(data.data);
                if( result.stream !== null ) {
                    $scope.visibleBroadCast();
                }
            });
        }
    }

    $scope.visibleBroadCast = function()
    {
        $scope.data.enablePlayer = true;

        var options = {
            width: '100%',
            height: 200,
            autoplay: false,
            channel: "setreuid",
            playsinline: true
            //video: "{VIDEO_ID}"
        };
        $scope.data.player = new Twitch.Player("cbcplayer", options);
        A = $scope.data.player;
    }

    $scope.calculateDistance = function(lat1, lon1, lat2, lon2)
    {
      var R = 6371; // km
      var dLat = (lat2-lat1).toRad();
      var dLon = (lon2-lon1).toRad();
      var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1.toRad()) * Math.cos(lat2.toRad()) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
      var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
      var d = R * c;
      return d;
    }

    $scope.imageUploadClicked = function()
    {
        document.getElementById('imageUpload').focus();
        document.getElementById('imageUpload').click();
    }

    /**
     * 페이지 로드
     *
     * @author 조태호
     */
    $scope.$on('$viewContentLoaded', function()
    {
        $scope.data.board_idx = $routeParams.board_idx;
        $scope.data.title = $routeParams.board_name;

        $scope.data.documents = [];

        $scope.loadData();
        $scope.loadLastData();
        // $scope.loadBroadCast();
    });

    $scope.procLast = function()
    {
        if( $scope.data.isLoading ) { return; }
        $scope.data.isLoading = true;

        api( 'BOARD', 'LOAD_LAST', {
            perid: $rootScope.getCurrentAccount().uid,
            custcd: $rootScope.config.custcd,
            board_idx: $scope.data.board_idx,
            lastid: $scope.data.lastid
        }, function( data ) {
            if( data.result === 'success' && data.data.length > 0 )
            {
                $scope.data.lastid = data.data[0].id;
                $timeout(function() {
                    $scope.data.documents = $scope.calcData( data.data ).concat( $scope.data.documents );
                });
            }
            $scope.data.isLoading = false;
        });

        $scope.loadOnlines();
    }

    $scope.loadOnlines = function()
    {
        $rootScope.httpRequestToServer( $rootScope.config.externalUrls.pingpong, '', '', {
            token: $rootScope.config.token
        }, function( data ) {
            $rootScope.config.token = data.token;
            $timeout(function() {
                $scope.data.online = data.userCount;
            });
        } );
    }

    $scope.popimage = function( imageIndex )
    {
        $timeout(function() {
            $rootScope.data.imageUrl = imageIndex;
            $rootScope.data.imagePop = true;
        });
    }

    $scope.loadLastData = function()
    {
        if ( angular.isDefined($scope.data.interval) ) return;

        $scope.data.interval = $interval(function() {
            $scope.procLast();
        }, 5000);
    }

    $scope.calcData = function( data )
    {
        for( var i in data )
        {
            data[i] = $scope.calcDetail(data[i]);
        }

        return data;
    }

    $scope.calcDetail = function(item)
    {
        var badCount = item.bad * -1 + item.good*1;
        var isLong = item.data.length > 100;

        if (!item.hasOwnProperty('plaindata')) item.plaindata = item.data;

        var predata = (isLong ? item.plaindata.substr(0, 100).replace( /\n/g, '<BR>' ) : item.plaindata.replace( /\n/g, '<BR>' ));

        if (badCount < 0)
        {
            predata = shuffle(predata, badCount * -1);
        }


        item.predata = (isLong ? $sce.trustAsHtml(predata + ' <span class="load-content-more">... 더보기</span>') : $sce.trustAsHtml(predata));
        item.data = $sce.trustAsHtml(item.plaindata.replace( /\n/g, '<BR>' ));

        if (!item.open) item.open = false;

        return item;
    }

    $scope.loadData = function()
    {
        $scope.data.isLoading = true;

        $scope.data.page++;
        api( 'BOARD', 'LOAD', {
            perid: $rootScope.getCurrentAccount().uid,
            custcd: $rootScope.config.custcd,
            board_idx: $scope.data.board_idx,
            page: $scope.data.page
        }, function( data ) {
            // console.log(data);
            if( data.result === 'success' && data.data.length > 0 )
            {
                if( $scope.data.lastid === 0 ) {
                    var i=-1;
                    while( data.data[++i].req > 0 ){};
                    $scope.data.lastid = data.data[i].id;
                }
                $timeout(function() {
                    $scope.data.documents = $scope.data.documents.concat( $scope.calcData( data.data ));
                });
            }
            else
            {
                $scope.data.page = -1;
            }
            // console.log( $scope.data.page );
            $scope.data.isLoading = false;
        });

        $scope.loadOnlines();
    }

    $scope.$on('$destroy', function() {
        // Make sure that the interval is destroyed too
        // console.log( "$destroy" );
        $interval.cancel( $scope.data.interval );
        $scope.data.interval = undefined;
        if( $scope.data.player !== null )
        {
            $scope.data.player.destroy();
            $scope.data.player = null;
        }
    });
});
