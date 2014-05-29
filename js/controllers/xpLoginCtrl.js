'use strict';

var xpLoginApp = angular.module('xpLoginApp');

xpLoginApp.controller('XpLoginCtrl', ['$rootScope', '$scope', '$location', '$window', 'xpAuth', 'xpApiRouterFactory', 
  function($rootScope, $scope, $location, $window, xpAuth, xpApiRouterFactory) {

    xpApiRouterFactory.setCsrfToken();

    $scope.rememberme = true;
    $scope.login = function() {
      xpAuth.login({
          email: $scope.email,
          password: $scope.password,
          rememberme: $scope.rememberme
        },
        function(res) {
          $location.path('/');
        },
        function(err) {
          $rootScope.error = "Failed to login";
        });
    };
  }
]);
