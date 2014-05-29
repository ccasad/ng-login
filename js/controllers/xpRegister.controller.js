'use strict';

var xpLoginApp = angular.module('xpLoginApp');

xpLoginApp.controller('XpRegisterCtrl', ['$rootScope', '$scope', '$location', 'xpAuthFactory', 
  function($rootScope, $scope, $location, xpAuthFactory) {
    $scope.role = xpAuthFactory.userRoles.user;
    $scope.userRoles = xpAuthFactory.userRoles;

    $scope.register = function() {
      console.log($scope.role.bitMask);
      xpAuthFactory.register({
          firstName: $scope.firstName,
          lastName: $scope.lastName,
          email: $scope.email,
          password: $scope.password,
          bitMask: $scope.role.bitMask
        },
        function() {
          $location.path('/');
        },
        function(err) {
          $rootScope.error = err;
        });
    };
  }
]);
