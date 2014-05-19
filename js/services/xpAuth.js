'use strict';

xpLoginApp.factory('xpAuth', function($http, $cookieStore){

  var accessLevels = routingConfig.accessLevels;
  var userRoles = routingConfig.userRoles;
  var currentUser = $cookieStore.get('user') || { username: '', role: userRoles.public, token: '' };

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
    register: function(res, success, error) {
      $http.post('/ng-login/api/register', res).success(function(res) {
        changeUser(res);
        success();
      }).error(error);
    },
    login: function(res, success, error) {
      $http.post('/ng-login/apirouter.php?route=login', res).success(function(res){
        var user = res.data;
        changeUser(user);
        success(user);
      }).error(error);
    },
    logout: function(success, error) {
      $http.post('/ng-login/apirouter.php?route=logout').success(function(){
        changeUser({
          username: '',
          role: userRoles.public,
          token: ''
        });
        success();
      }).error(error);
    },
    accessLevels: accessLevels,
    userRoles: userRoles,
    user: currentUser
  };
});

