'use strict';

xpLoginApp.directive('xpAccessLevel', ['xpAuth', function(xpAuth) {
  return {
    restrict: 'A',
    link: function($scope, element, attrs) {
      var prevDisp = element.css('display');
      var userRole;
      var accessLevel;

      $scope.user = xpAuth.user;
      $scope.$watch('user', function(user) {
        if (user.role) {
          userRole = user.role;
        }    
        updateCSS();
      }, true);

      attrs.$observe('accessLevel', function(al) {
        if (al) {
          accessLevel = $scope.$eval(al);
        } 
        updateCSS();
      });

      function updateCSS() {
        if (userRole && accessLevel) {
          if (!xpAuth.authorize(accessLevel, userRole)) {
            element.css('display', 'none');
          } else {
            element.css('display', prevDisp);
          }
        }
      }
    }
  };
}]);

xpLoginApp.directive('xpActiveNav', ['$location', function($location) {
  return {
    restrict: 'A',
    link: function(scope, element, attrs) {
      var anchor = element[0];
      if (element[0].tagName.toUpperCase() != 'A') {
        anchor = element.find('a')[0];
      }
      var path = anchor.href;

      scope.location = $location;
      scope.$watch('location.absUrl()', function(newPath) {
        path = normalizeUrl(path);
        newPath = normalizeUrl(newPath);

        if (path === newPath || (attrs.activeNav === 'nestedTop' && newPath.indexOf(path) === 0)) {
          element.addClass('active');
        } else {
          element.removeClass('active');
        }
      });
    }
  };

  function normalizeUrl(url) {
    if (url[url.length - 1] !== '/') {
      url = url + '/';
    }
    return url;
  }
}]);