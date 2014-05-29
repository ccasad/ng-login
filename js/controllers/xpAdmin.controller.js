'use strict';

var xpLoginApp = angular.module('xpLoginApp');

xpLoginApp.controller('XpAdminCtrl', ['$rootScope', '$scope', 'xpUsersFactory', 'xpAuthFactory', 
  function($rootScope, $scope, xpUsersFactory, xpAuthFactory) {
    $scope.loading = true;
    $scope.userRoles = xpAuthFactory.userRoles;

    $scope.users = [];

    xpUsersFactory.getAll(function(res) {
        $scope.users = res.data;
        $scope.loading = false;
      }, function(err) {
        $rootScope.error = "Failed to fetch users.";
        $scope.loading = false;
      });
  }
]);
