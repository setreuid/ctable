/**
 * 조대 시간표 웹앱
 *
 * @author 조태호
 */

var chosunApp = angular.module('chosunApp', [
  'ngRoute', 'ngTouch', 'ng-fastclick', 'ngAnimate',
  'monospaced.elastic', 'settings', 'lr.upload',

  'whole', 'whole2', 'daytable', 'detail',      // 시간표
  'board', 'calendar',                          // 게시판
  'stocklist', 'stockbuy',                      // 모의주식
  'gamegraph'                                   // 게임
])

.filter( 'toTrusted', ['$sce', function($sce) {
    return function(text) {
        return $sce.trustAsHtml(text);
    };
}])

.directive('input', function() {
  return {
    restrict: 'E',
    require: 'ngModel',
    link: function(scope, $el, attrs, ngModel) {
      if ($el.type === 'number') {
        ngModel.$parsers.push(function(value) {
          return value.toString();
        });

        ngModel.$formatters.push(function(value) {
          return parseFloat(value, 10);
        });
      }
    }
  }
})

.directive( 'ngEnter', function()
{
    return function(scope, element, attrs)
    {
        element.on('keydown keypress', function(event)
        {
            if (event.which === 13)
            {
                scope.$apply(function()
                {
                    scope.$eval(attrs.ngEnter || attrs.ngClick,
                    {
                        $event: event
                    });
                });
                event.preventDefault();
            }
        });
    };
})

.directive( 'backImg', function()
{
    return {
        scope : {
            name : '@',
            backImg : '@'
        },
        link : function( scope, element, attrs )
        {
            scope.$watch( 'backImg', function()
            {
                // the following two statements are equivalent
                var errCheckChar = attrs.backImg.charAt( attrs.backImg.length - 1 );
                if( errCheckChar === 'N' || errCheckChar === '=' )
                    return;

                element.find( 'tmpbg' ).remove();
                element.append( '<tmpbg></tmpbg>' );

                var tmpBg = element.find( 'tmpbg' );

                var bgImg = new Image();
                bgImg.onload = function () {

                    // if( bgImg.width < 300 && bgImg.height < 200 )
                    // {
                    //     element.css({ 'width' : bgImg.width + 'px', 'height' : bgImg.height + 'px', 'background-image' : 'url(' + attrs.backImg + ')' });
                    // }
                    // else
                    // {
                    // }
                    element.css({
                        'background-image' : 'url(' + attrs.backImg + ')'
                    });

                    setTimeout( function() {
                        tmpBg.css( 'opacity', '0' );
                    }, 300 );

                    setTimeout( function() {
                    //   console.log(tmpBg);
                        tmpBg.remove();
                    }, 1300 );
                };
                bgImg.src = attrs.backImg;
            } );
        }
    };
});


/**
 * 루틴 공용 함수
 *
 * @author 조태호
 */

chosunApp.config(['$routeProvider',
  function( $routeProvider ) {

    $routeProvider

    .when('/splash', {
      templateUrl: 'templates/splash.html?v=' + window.account.svVer,
      controller: 'SplashCtrl'
    })

    .when('/whole', {
      templateUrl: 'templates/timetable/whole.html?v=' + window.account.svVer,
      controller: 'TimeTableWholeCtrl'
    })
    .when('/whole2', {
      templateUrl: 'templates/timetable/whole2.html?v=' + window.account.svVer,
      controller: 'TimeTableWhole2Ctrl'
    })
    .when('/daytable', {
      templateUrl: 'templates/timetable/list.html?v=' + window.account.svVer,
      controller: 'DayTableCtrl'
    })
    .when('/detail/:shortname/:curi_num', {
      templateUrl: 'templates/timetable/detail.html?v=' + window.account.svVer,
      controller: 'DetailCtrl'
    })
    .when('/settings', {
      templateUrl: 'templates/timetable/settings.html?v=' + window.account.svVer,
      controller: 'SettingsCtrl'
    })

    .when('/board/:board_idx/:board_name', {
      templateUrl: 'templates/board/main.html?v=' + window.account.svVer,
      controller: 'BoardCtrl'
    })
    .when('/calendar', {
      templateUrl: 'templates/board/calendar.html?v=' + window.account.svVer,
      controller: 'CalendarCtrl'
    })

    .when('/stocklist/:focus', {
      templateUrl: 'templates/stock/list.html?v=' + window.account.svVer,
      controller: 'StockListCtrl'
    })
    .when('/stockbuy/:code/:type', {
      templateUrl: 'templates/stock/buy.html?v=' + window.account.svVer,
      controller: 'StockBuyCtrl'
    })

    .when('/gamegraph', {
      templateUrl: 'templates/game/graph.html?v=' + window.account.svVer,
      controller: 'GameGraphCtrl'
    })

    .otherwise({
      redirectTo: '/splash'
    });
  }
]);


/**
 * 공용 변수
 *
 * @author 조태호
 */

chosunApp.run( function( $rootScope, $location, $http, $timeout,
    $templateCache, $window, $sce )
{
    $rootScope.config = {
        version: window.account.svVer + ' (2018.08.17)',
        useThr: true,
        useSat: true,
        useMon: true,
        useTue: true,
        useWed: true,
        useThu: true,
        useFri: true,
        viewArea: false,
        hostUrl: 'libs/query.php',
        externalUrls: {
            timetable: 'libs/getPrivateTable.php',
            detail: 'libs/getDetailInfo.php',
            calendar: 'libs/getCalendar.php',
            pingpong: 'libs/ping.php',
            gamechart: 'libs/game/chart.php'
        },
        custcd: 'CHOSUN',
        year: '2017',
        class: '10',
        token: '',
        account: {},
        menuIndex: 0,
        height: 8,
        heightC: 72,
        backdrop: false,
        showVisibleTime: false,
        showOneSee: false,
        showLongRoom: false,
        isProcessLoading: false,
        hour: (new Date()).getHours()
    };

    $rootScope.menus = [
        { idx: 0, icon: $sce.trustAsHtml('schedule'), href: '/daytable' },
        { idx: 1, icon: $sce.trustAsHtml('today'), href: '/whole' },
        { idx: 2, icon: $sce.trustAsHtml('chat'), href: '/board/0/쓸데없는 게시판' },
        { idx: 3, icon: $sce.trustAsHtml('assessment'), href: '/stocklist/0' },
        { idx: 4, icon: $sce.trustAsHtml('&#xE84F'), href: '/calendar' },
        { idx: 5, icon: $sce.trustAsHtml('settings'), href: '/settings' }
    ];

    $rootScope.data = {
        staticColors: [
            "#ffcdd2", "#F8BBD0", "#E1BEE7", "#D1C4E9", "#C5CAE9",
            "#BBDEFB", "#B3E5FC", "#B2EBF2", "#B2DFDB", "#C8E6C9",
            "#DCEDC8", "#F0F4C3", "#FFF9C4", "#FFECB3", "#FFE0B2",
            "#FFCCBC", "#D7CCC8", "#F5F5F5", "#CFD8DC"
        ],
        alert: false,
        alertMessage: '',
        popableColors: [],
        visibleTimes: [],
        colors: [], //  과목 칼라 저장
        timetable: [], //  시간표 데이터
        daytable: [
            { name: '월요일', data: [] },
            { name: '화요일', data: [] },
            { name: '수요일', data: [] },
            { name: '목요일', data: [] },
            { name: '금요일', data: [] },
            { name: '토요일', data: [] },
            { name: '일요일', data: [] }
        ], //  요일별 시간표 데이터
        loading: false,
        imageUrl: '',
        imagePop: false
    };

    ///
    // if( checkWebKit() > 521 )
    // {
    //     $timeout(function () {
    //         $rootScope.config.backdrop = true;
    //     }, 1000);
    // }
    ///

    $rootScope.goBack = function()
    {
        $window.history.back();
    }

    $rootScope.paging = {
        url: 'splash'
    };

    $rootScope.doLoading = function()
    {
        $timeout(function() {
            $rootScope.data.loading = true;
        });
    }

    window.doLoading = $rootScope.doLoading;

    $rootScope.isLoading = function()
    {
        return $rootScope.data.loading;
    }

    $rootScope.stopLoading = function( time )
    {
        time = time || 0;

        $timeout(function() {
            $rootScope.data.loading = false;
        }, time);
    }

    window.stopLoading = $rootScope.stopLoading;

    $rootScope.stopAlert = function()
    {
        $timeout(function() {
            $rootScope.data.alert = false;
        });
    }

    $rootScope.doAlert = function( message )
    {
        $timeout(function() {
            $rootScope.data.alertMessage = message;
            $rootScope.data.alert = true;
        });

        return $rootScope.stopAlert;
    }

    window.Alert = $rootScope.doAlert;

    $rootScope.stopConfirm = function( value )
    {
        $timeout(function() {
            $rootScope.data.confirm = false;
        });

        $rootScope.data.confirmFunction( value );
    }

    $rootScope.doConfirm = function( message, _handleFunction )
    {
        $timeout(function() {
            $rootScope.data.confirmMessage = message;
            $rootScope.data.confirmFunction = _handleFunction;
            $rootScope.data.confirm = true;
        });

        return $rootScope.stopConfirm;
    }

    window.Confirm = $rootScope.doConfirm;

    $rootScope.getCurrentAccount = function()
    {
        return $rootScope.config.account;
    };

    $rootScope.getTimeTable = function()
    {
        return JSON.parse( JSON.stringify( $rootScope.data.timetable ) );
    };

    $rootScope.getDayTable = function( idx )
    {
        return JSON.parse( JSON.stringify( $rootScope.data.daytable[ idx ] ) );
    };

    $rootScope.httpRequestToServer = function( _url, _target, _method, _params, _handleFunction )
    {
        $rootScope.config.isProcessLoading = true;

        var requestParams = {
            target : _target,
            method : _method
        };

        // console.log( _params );

        for( var i in _params )
        {
            requestParams[ i ] = _params[ i ];
        }
        var finalParams = JSON.stringify( requestParams );

        // console.log( "Url request params :" );
        // console.log( requestParams );

        var pack = {

            method : 'POST',
            url : _url,
            data : finalParams,
            headers : {
                'Content-Type' : 'application/json; charset=utf-8'
            }
        };

        $http( pack ).success( function( data )
        {
            // console.log( "Url request result :" );
            // console.log( data );
            $rootScope.config.isProcessLoading = false;

            if( typeof _handleFunction === 'function' )
            {
                _handleFunction( data );
            }

        }).error( function( data )
        {
            $rootScope.config.isProcessLoading = false;
            console.log( data );
        });
    };

    window.api = function( _target, _method, _params, _handleFunction )
    {
        $rootScope.httpRequestToServer( $rootScope.config.hostUrl, _target, _method, _params, _handleFunction );
    };

    window.refreshTimeTable = function( _handleFunction )
    {
        doLoading();
        $rootScope.httpRequestToServer( $rootScope.config.externalUrls.timetable, '', '', {
            userid: $rootScope.getCurrentAccount().uid,
            userpw: $rootScope.getCurrentAccount().upw,
            class: $rootScope.config.class
        }, function( data ) {
            if( data.result === 'success' )
            {
                $rootScope.data.timetable = data.data;
            }
            if( typeof _handleFunction === 'function' ) _handleFunction( data );
            stopLoading();
        } );
    };

    $rootScope.getRandomColor = function()
    {
        var color = $rootScope.data.popableColors.pop();
        return color;
    }

    $rootScope.getCalcedColor = function( code )
    {
        var colors = $rootScope.data.colors;

        for( var i in colors )
        {
            if( colors[i].code === code ) return colors[i].color
        }

        var randColor = $rootScope.getRandomColor();

        $rootScope.data.colors.push( { code: code, color: randColor } );

        return randColor;
    }

    $rootScope.calcTimeTable = function( data, _handleFunction )
    {
        // subject 과목명
        // curi_num 과목코드
        // emp_nm 교수명
        // room_nm 장소

        var result = [];
        var tmp = [];

        $rootScope.data.daytable = [
            { name: '월요일', data: [] },
            { name: '화요일', data: [] },
            { name: '수요일', data: [] },
            { name: '목요일', data: [] },
            { name: '금요일', data: [] },
            { name: '토요일', data: [] },
            { name: '일요일', data: [] }
        ];

        $rootScope.data.visibleTimes = [];

        $rootScope.data.staticColors = shuffleArray($rootScope.data.staticColors);
        $rootScope.data.popableColors = JSON.parse(JSON.stringify($rootScope.data.staticColors));

        for( var i in data )
        {
            if( i > 0 && i % 7 === 0 )
            {
                var isNull = true;
                var time = Math.floor( i / 7 ) + 7;

                if( time < 9 || time > 17 )
                {
                    for( var j in tmp ) { if( tmp[j].curi_num !== '' ) { isNull = false; break; } }
                }
                else
                {
                    isNull = false;
                }

                if( !isNull )
                {
                    result.push( JSON.parse( JSON.stringify( tmp )));

                    if( time > 12 ) { time -= 12; }
                    $rootScope.data.visibleTimes.push( time );
                }

                tmp = [];
            } //  ?교시 구분

            if( data[i].curi_num !== '' ) {
                data[i].color = $rootScope.getCalcedColor( data[i].curi_num );
                data[i].room = data[i].room_nm.match( /[0-9][0-9\-]+/ );
                if( data[i].subject.length > 6 ) {
                    $rootScope.config.height = 10;
                }
            } else
                data[i].color = 'transparent';

            var j = i*1 + 7;
            var count = 0;
            while( data[i].curi_num !== '' && j < data.length )
            {
                if( data[j].curi_num === data[i].curi_num )
                {
                    count++;
                }
                else
                {
                    break;
                }

                j += 7;
            }

            if( i*1 - 7 > 0 )
            {
                if( data[i-7].curi_num === data[i].curi_num )
                {
                    data[i].isBig = true;
                    count = 0;
                }
            }

            data[i].repeat = count;
            data[i].shortname = data[i].subject.substr( 0, 4 );

            $rootScope.data.daytable[i%7].data.push( data[i] );
            tmp.push( data[i] );
        }

        var isNull = true;
        var time = Math.floor( i / 7 ) + 8;

        if( time < 9 || time > 17 )
        {
            for( var j in tmp ) { if( tmp[j].curi_num !== '' ) { isNull = false; break; } }
        }
        else
        {
            isNull = false;
        }

        if( !isNull )
        {
            result.push( JSON.parse( JSON.stringify( tmp )));

            if( time > 12 ) { time -= 12; }
            $rootScope.data.visibleTimes.push( time );
        }

        tmp = [];
        $rootScope.data.timetable = result;

        if( typeof _handleFunction === 'function' ) _handleFunction();
    }

    $rootScope.initTimeTable = function( customClass, _handleFunction, _handleFailedFunction )
    {
        var today = new Date();
        $rootScope.config.year = today.getFullYear();

        var cClass = customClass;

        if( typeof cClass === 'undefined'  )
        {
            cClass = window.localStorage['class'] || '';

            if( cClass === '' )
            {
                var month = today.getMonth()+1;

                if( month > 2 && month < 7 )
                {
                    cClass = '10';
                }
                else if( month > 6 && month < 9 )
                {
                    cClass = '11';
                }
                else if( month > 8 && month < 13 )
                {
                    cClass = '20';
                }
                else if( month > 0 && month < 3 )
                {
                    cClass = '21';
                }
            }
        }

        $rootScope.config.class = cClass;

        api( 'TIMETABLE', 'LOAD', {
            perid: $rootScope.getCurrentAccount().uid,
            custcd: $rootScope.config.custcd,
            year: $rootScope.config.year,
            class: $rootScope.config.class
        }, function( data ) {
            // console.log(data.data);
            if( data.result === 'success' )
            {
                $rootScope.calcTimeTable( JSON.parse( data.data ), _handleFunction );
            }
            else
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

                        $rootScope.calcTimeTable( data.data, _handleFunction );
                    }
                    else
                    {
                        _handleFailedFunction( data.message );
                    }
                });
            }
        });
    }

    // [페이지 이동]
    $rootScope.gohref = function( index )
    {
        $rootScope.paging.url = index;
        $location.path( index );
    }

    $rootScope.gomenu = function( index )
    {
        var item = $rootScope.menus[ index ];

        if( index == 1 )
        {
            item.href = $rootScope.config.usenew01 ? '/whole2' : '/whole';
        }

        $rootScope.config.menuIndex = index;
        $rootScope.paging.url = item.href;
        window.localStorage['lastindex'] = index;
        $location.path( item.href );
    }

    $rootScope.$on('$routeChangeStart', function( event )
    {
        $timeout(function() {
            if( typeof $rootScope.getCurrentAccount().uid === 'undefined' )
            {
                $location.path( 'splash' );
            }
        });
    });

    $rootScope.$on('$routeChangeStart', function( event )
    {
        $timeout(function() {
            if( typeof $rootScope.getCurrentAccount().uid === 'undefined' )
            {
                $location.path( 'splash' );
            }
        });
    });

    $rootScope.transferTime = window.transferTime;

})

.controller( 'SplashCtrl', function( $scope, $sce, $location,
    $http, $rootScope, $timeout )
{
    $scope.data = {
        login: false
    };

    $scope.$on( '$viewContentLoaded', function()
    {
        // if( window.account.svVer*1 > $rootScope.config.version.substr( 0, $rootScope.config.version.indexOf( ' ' ))*1 )
        // {
        //     window.location.reload( true );
        //     return;
        // }

        doLoading();

        $rootScope.config.account.uid = window.account.uid;
        $rootScope.config.account.upw = window.account.upw;

        window.account.uid = window.account.upw = '';

        if( $rootScope.config.account.uid === '' )
        {
            $rootScope.config.account.uid = window.localStorage['uid'] || '';
            $rootScope.config.account.upw = window.localStorage['upw'] || '';

            if( $rootScope.config.account.uid === '' )
            {
                stopLoading();
                $scope.data.login = true;
            }
            else
            {
                $scope.doLogin();
            }
        }
        else
        {
            $scope.doLogin();
        }
    });

    $scope.doLogin = function()
    {
        doLoading();

        $rootScope.initTimeTable( undefined, function() {

            window.localStorage['uid'] = $rootScope.config.account.uid;
            window.localStorage['upw'] = $rootScope.config.account.upw;

            $timeout( function() {
                // if( navigator.userAgent.indexOf('iPhone') > -1 && screen.width != 1125 )
                // {
                //     document.body.classList.add('ios');
                // }

                var lastLocation = window.localStorage['lastindex'] || 0;

                // 데이터 로드
                $rootScope.config.useThr = ( window.localStorage['usethr'] || 'true' ) === 'true';
                $rootScope.config.useSat = ( window.localStorage['usesat'] || 'true' ) === 'true';
                $rootScope.config.useMon = ( window.localStorage['usemon'] || 'true' ) === 'true';
                $rootScope.config.useTue = ( window.localStorage['usetue'] || 'true' ) === 'true';
                $rootScope.config.useWed = ( window.localStorage['usewed'] || 'true' ) === 'true';
                $rootScope.config.useThu = ( window.localStorage['usethu'] || 'true' ) === 'true';
                $rootScope.config.useFri = ( window.localStorage['usefri'] || 'true' ) === 'true';
                $rootScope.config.viewArea = ( window.localStorage['viewarea'] || 'false' ) === 'true';
                $rootScope.config.showVisibleTime = ( window.localStorage['showvisibletime'] || 'false' ) === 'true';
                $rootScope.config.showOneSee = ( window.localStorage['showonesee'] || 'false' ) === 'true';
                $rootScope.config.showLongRoom = ( window.localStorage['showlongroom'] || 'false' ) === 'true';
                $rootScope.config.usenew01 = ( window.localStorage['usenew01'] || 'false' ) === 'true';

                // if( lastLocation === 'splash' ) lastLocation = 'daytable';

                $rootScope.gomenu( lastLocation );
                stopLoading();
            }, 200);
        }, function( failedMessage ) {
            stopLoading();
            $scope.data.login = true;
            Alert( failedMessage );
        });
    }
});


// 공용 함수
// ----------------------------------------------------------

/**
 * Dict를 Urlencoding 하여 반환
 * @author 조태호
 * @param  {Object} obj Params
 * @return {String}     Urlencoded Text
 */
Object.toparams = function ObjecttoParams(obj)
{
  var p = [];
  for (var key in obj)
  {
    p.push(key + '=' + encodeURIComponent(obj[key]));
  }
  return p.join('&');
};

Number.prototype.format = function(){
    if(this==0) return 0;

    var reg = /(^[+-]?\d+)(\d{3})/;
    var n = (this + '');

    while (reg.test(n)) n = n.replace(reg, '$1' + ',' + '$2');

    return n;
};

String.prototype.format = function(){
    var num = parseFloat(this);
    if( isNaN(num) ) return "0";

    return num.format();
};

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
};

function checkWebKit() {
    var result = /AppleWebKit\/([\d.]+)/.exec(navigator.userAgent);
    if (result) {
        return parseFloat(result[1]);
    }
    return null;
};

function is_hangul_char(ch) {
    c = ch.charCodeAt(0);
    if( 0x1100<=c && c<=0x11FF ) return true;
    if( 0x3130<=c && c<=0x318F ) return true;
    if( 0xAC00<=c && c<=0xD7A3 ) return true;
    return false;
}

function shuffle(text, cnt)
{
    var t = text.split('');
    var i = cnt;
    var c = 0;

    var c1, c2, i1, i2;

    while (i-- > 0)
    {
        i1 = Math.floor(Math.random() * t.length);
        i2 = Math.floor(Math.random() * t.length);

        c1 = t[i1];
        c2 = t[i2];

        if (is_hangul_char(c1) && is_hangul_char(c2))
        {
            t[i1] = c2;
            t[i2] = c1;
        }
        else if (c < 5)
        {
            c++;
            i++;
        }
    }

    return t.join('');
}

/**
 * Shuffles array in place.
 * @param {Array} a items An array containing the items.
 */
function shuffleArray(a) {
    var j, x, i;
    for (i = a.length - 1; i > 0; i--) {
        j = Math.floor(Math.random() * (i + 1));
        x = a[i];
        a[i] = a[j];
        a[j] = x;
    }
    return a;
}
