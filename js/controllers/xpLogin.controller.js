'use strict';

var xpLoginApp = angular.module('xpLoginApp');

xpLoginApp.controller('XpLoginCtrl', ['$rootScope', '$scope', '$location', '$window', 'xpAuthFactory', 'xpApiRouterFactory', 
  function($rootScope, $scope, $location, $window, xpAuthFactory, xpApiRouterFactory) {

    xpApiRouterFactory.setCsrfToken();

    $scope.rememberme = true;
    $scope.login = function() {
      xpAuthFactory.login({
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
