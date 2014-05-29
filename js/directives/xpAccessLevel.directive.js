'use strict';

xpLoginApp.directive('xpAccessLevel', ['xpAuthFactory', 
  function(xpAuthFactory) {
    return {
      restrict: 'A',
      link: function($scope, element, attrs) {
        var prevDisp = element.css('display');
        var userRole;
        var accessLevel;

        $scope.user = xpAuthFactory.user;
        $scope.$watch('user', function(user) {
          if (user.role) {
            userRole = user.role;
          }    
          updateCSS();
        }, true);

        attrs.$observe('xpAccessLevel', function(al) {
          if (al) {
            accessLevel = $scope.$eval(al);
          } 
          updateCSS();
        });

        function updateCSS() {
          if (userRole && accessLevel) {
            if (!xpAuthFactory.authorize(accessLevel, userRole)) {
              element.css('display', 'none');
            } else {
              element.css('display', prevDisp);
            }
          }
        }
      }
    };
  }
]);
