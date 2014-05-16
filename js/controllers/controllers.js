'use strict';

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

xpLoginApp.controller('XpLoginCtrl', ['$rootScope', '$scope', '$location', '$window', 'xpAuth', function($rootScope, $scope, $location, $window, xpAuth) {

  $scope.rememberme = true;
  $scope.login = function() {
    xpAuth.login({
        username: $scope.username,
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

  $scope.loginOauth = function(provider) {
    $window.location.href = '/auth/' + provider;
  };
}]);

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

