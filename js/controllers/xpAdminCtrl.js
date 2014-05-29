'use strict';

var xpLoginApp = angular.module('xpLoginApp');

xpLoginApp.controller('XpAdminCtrl', ['$rootScope', '$scope', 'xpUsers', 'xpAuth', function($rootScope, $scope, xpUsers, xpAuth) {
  $scope.loading = true;
  $scope.userRoles = xpAuth.userRoles;

  $scope.users = [];

  xpUsers.getAll(function(res) {
      $scope.users = res.data;
      $scope.loading = false;
    }, function(err) {
      $rootScope.error = "Failed to fetch users.";
      $scope.loading = false;
    });
}]);
