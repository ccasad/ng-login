'use strict';

var xpLoginApp = angular.module('xpLoginApp');

xpLoginApp.controller('XpNavCtrl', ['$rootScope', '$scope', '$location', 'xpAuthFactory', 
  function($rootScope, $scope, $location, xpAuthFactory) {
    $scope.user = xpAuthFactory.user;
    $scope.userRoles = xpAuthFactory.userRoles;
    $scope.accessLevels = xpAuthFactory.accessLevels;

    $scope.logout = function() {
      xpAuthFactory.logout(function() {
          $location.path('/login');
        }, function() {
          $rootScope.error = "Failed to logout";
        });
    };
  }
]);
