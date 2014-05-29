'use strict';

var xpLoginApp = angular.module('xpLoginApp', ['ngCookies', 'ui.router'])

xpLoginApp.constant('APP_GLOBALS',{
  baseUrl: '/mcg2/app/',
  roles: [
    'public',     // bitmask=1
    'user',       // bitmask=2
    'admin',      // bitmask=4
    'student',    // bitmask=8
    'advisor'     // bitmask=16
  ],
  accessLevels: {
    'public' : "*",
    'anon': ['public'],
    'user' : ['user', 'admin', 'student', 'advisor'],
    'admin': ['admin']
  }
});

xpLoginApp.run(['$http', '$rootScope', '$state', 'xpAuth', 
  function ($http, $rootScope, $state, xpAuth) {

    $rootScope.$on("$stateChangeStart", function (event, toState, toParams, fromState, fromParams) {

      var user = xpAuth.user;
      $http.defaults.headers.common['USER_TOKEN'] = user.token;

      if (!xpAuth.authorize(toState.data.access)) {

        $rootScope.error = "Access Denied";
        event.preventDefault();
        
        if (fromState.url === '^') {
          if (xpAuth.isLoggedIn()) {
            $state.go('user.home');
          } else {
            $rootScope.error = null;
            $state.go('anon.login');
          }
        }
      }
    });
  }
]);
