'use strict';

xpLoginApp.controller('XpAdminCtrl', ['$rootScope', '$scope', 'xpUsers', 'xpAuth', function($rootScope, $scope, xpUsers, xpAuth) {
  $scope.loading = true;
  $scope.userRoles = xpAuth.userRoles;

  xpUsers.getAll(function(res) {
      $scope.users = res;
      $scope.loading = false;
    }, function(err) {
      $rootScope.error = "Failed to fetch users.";
      $scope.loading = false;
    });
}]);
