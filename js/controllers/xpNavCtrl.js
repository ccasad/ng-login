'use strict';

var xpLoginApp = angular.module('xpLoginApp');

xpLoginApp.controller('XpNavCtrl', ['$rootScope', '$scope', '$location', 'xpAuth', function($rootScope, $scope, $location, xpAuth) {
  $scope.user = xpAuth.user;
  $scope.userRoles = xpAuth.userRoles;
  $scope.accessLevels = xpAuth.accessLevels;

  $scope.logout = function() {
    xpAuth.logout(function() {
        $location.path('/login');
      }, function() {
        $rootScope.error = "Failed to logout";
      });
  };
}]);
