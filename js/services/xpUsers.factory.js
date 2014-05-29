'use strict';

var xpLoginApp = angular.module('xpLoginApp');

xpLoginApp.factory('xpUsersFactory', ['$http', 
	function($http) {
	  return {
	    getAll: function(success, error) {
	      $http.get('/ng-login/apirouter.php?route=users')
		      .success(success)
		      .error(error);
	    }
	  };
	}
]);

