'use strict';

xpLoginApp.controller('XpRegisterCtrl', ['$rootScope', '$scope', '$location', 'xpAuth', function($rootScope, $scope, $location, xpAuth) {
  $scope.role = xpAuth.userRoles.user;
  $scope.userRoles = xpAuth.userRoles;

  $scope.register = function() {
    console.log($scope.role.bitMask);
    xpAuth.register({
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
}]);
