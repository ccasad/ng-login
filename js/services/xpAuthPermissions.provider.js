'use strict';

var xpLoginApp = angular.module('xpLoginApp');

xpLoginApp.provider('xpAuthPermissions', ['APP_GLOBALS', 
  function(APP_GLOBALS) {

    this.userRoles = [];
    this.accessLevels = [];

    this.$get = function($http, $cookieStore) {
      var self = this;
      return {
        userRoles: self.userRoles,
        accessLevels: self.accessLevels
      };
    };

    this.setRolesAndAccessLevels = function() {
      this.setUserRoles();
      this.setAccessLevels();
    };

    this.setUserRoles = function() {
      var bitMask = "01";
      var userRoles = {};
      var roles = APP_GLOBALS.roles;

      for (var role in roles) {
        var intCode = parseInt(bitMask, 2);
        userRoles[roles[role]] = {
          bitMask: intCode,
          title: roles[role]
        };
        bitMask = (intCode << 1 ).toString(2);
      }

      this.userRoles = userRoles;
    };

    this.setAccessLevels = function() {

      var accessLevelDeclarations = APP_GLOBALS.accessLevels;
      var userRoles = this.userRoles;
      var accessLevels = {};

      for (var level in accessLevelDeclarations) {

        if (typeof accessLevelDeclarations[level] == 'string') {
          if (accessLevelDeclarations[level] == '*') {
            var resultBitMask = '';

            for (var role in userRoles) {
              resultBitMask += "1"
            }
            accessLevels[level] = {
              bitMask: parseInt(resultBitMask, 2)
            };
          } else {
            console.log("Access Control Error: Could not parse '" + accessLevelDeclarations[level] + "' as access definition for level '" + level + "'");
          }
        } else {
          var resultBitMask = 0;
          for (var role in accessLevelDeclarations[level]) {
            if (userRoles.hasOwnProperty(accessLevelDeclarations[level][role])) {
              resultBitMask = resultBitMask | userRoles[accessLevelDeclarations[level][role]].bitMask;
            } else {
              console.log("Access Control Error: Could not find role '" + accessLevelDeclarations[level][role] + "' in registered roles while building access for '" + level + "'");
            }
          }
          accessLevels[level] = {
            bitMask: resultBitMask
          };
        }
      }

      this.accessLevels = accessLevels;
    }
  }
]);
