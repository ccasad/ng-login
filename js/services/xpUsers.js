'use strict';

xpLoginApp.factory('xpUsers', ['$http', function($http) {

  return {
    getAll: function(success, error) {
      //$http.get('/ng-login/api/users').success(success).error(error);
      $http.get('/ng-login/apirouter.php?route=users').success(success).error(error);
    }
  };
}]);
