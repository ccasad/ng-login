'use strict';

xpLoginApp.factory('xpUsers', function($http) {
  return {
    getAll: function(success, error) {
      $http.get('/ng-login/api/users').success(success).error(error);
    }
  };
});
