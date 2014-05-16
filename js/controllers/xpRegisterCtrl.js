'use strict';

xpLoginApp.controller('XpRegisterCtrl', ['$rootScope', '$scope', '$location', 'xpAuth', function($rootScope, $scope, $location, xpAuth) {
  $scope.role = xpAuth.userRoles.user;
  $scope.userRoles = xpAuth.userRoles;

  $scope.register = function() {
    xpAuth.register({
        username: $scope.username,
        password: $scope.password,
        role: $scope.role
      },
      function() {
        $location.path('/');
      },
      function(err) {
        $rootScope.error = err;
      });
  };
}]);
