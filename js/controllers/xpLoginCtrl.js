'use strict';

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
