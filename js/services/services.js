'use strict';

xpLoginApp.factory('xpAuth', function($http, $cookieStore){

  var accessLevels = routingConfig.accessLevels;
  var userRoles = routingConfig.userRoles;
  var currentUser = $cookieStore.get('user') || { username: '', role: userRoles.public };

  $cookieStore.remove('user');

  function changeUser(user) {
    angular.extend(currentUser, user);
  }

  return {
    authorize: function(accessLevel, role) {
      if (role === undefined) {
        role = currentUser.role;
      }
      return accessLevel.bitMask & role.bitMask;
    },
    isLoggedIn: function(user) {
      if (user === undefined) {
        user = currentUser;
      }
      return user.role.title === userRoles.user.title || user.role.title === userRoles.admin.title;
    },
    register: function(user, success, error) {
      $http.post('/ng-login/api/register', user).success(function(res) {
        changeUser(res);
        success();
      }).error(error);
    },
    login: function(user, success, error) {
      $http.post('/ng-login/api/login', user).success(function(user){
        changeUser(user);
        success(user);
      }).error(error);
    },
    logout: function(success, error) {
      $http.post('/ng-login/api/logout').success(function(){
        changeUser({
          username: '',
          role: userRoles.public
        });
        success();
      }).error(error);
    },
    accessLevels: accessLevels,
    userRoles: userRoles,
    user: currentUser
  };
});

xpLoginApp.factory('xpUsers', function($http) {
  return {
    getAll: function(success, error) {
      $http.get('/ng-login/api/users').success(success).error(error);
    }
  };
});
