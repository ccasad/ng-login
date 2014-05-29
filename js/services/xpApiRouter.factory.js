'use strict';

var xpLoginApp = angular.module('xpLoginApp');

xpLoginApp.factory('xpApiRouterFactory', ['$state', '$http', '$rootScope',
  function ($state, $http, $rootScope) {
    var apiRouterFunctions = {};

    apiRouterFunctions.setCsrfToken = function() {
    	if ($rootScope.csrfToken === undefined || $rootScope.csrfToken.length < 1) {
	    	$http.get('/ng-login/apirouter.php?route=csrf')
	        .success(function(data) {
	          $rootScope.csrfToken = data;
	          $http.defaults.headers.common['CSRF_TOKEN'] = $rootScope.csrfToken;
	        })
	        .error(function(data, status, headers, config) {

	        });
    	}
    };

    return apiRouterFunctions;
  }
]);
