<?php
// Create token and set session
session_start();
$token = hash('sha256', uniqid(mt_rand(), true));
$_SESSION['XSRF'] = $token;
?>

<!DOCTYPE html>
<html lang="en" data-ng-app="xpLoginApp">
  <head>
    <base href="/ng-login/">
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="0" />    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Login Test</title>

    <link rel="stylesheet" href="css/app.css">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>           
      <script type="text/javascript" src="js/libs/html5shiv.js"></script>
      <script type="text/javascript" src="js/libs/respond.min.js"></script>      
    <![endif]-->
  </head>
  <body data-ng-cloak="">

    <div data-ng-controller="XpNavCtrl" class="navbar">
      <div class="navbar-inner">
        <div class="container-fluid">
          <ul class="nav nav-tabs">
            <li data-xp-access-level="accessLevels.anon" data-xp-active-nav="">
              <a href="login">Log in</a>
            </li>
            <li data-xp-access-level="accessLevels.anon" data-xp-active-nav="">
              <a href="register">Register</a>
            </li>
            <li data-xp-access-level="accessLevels.user" data-xp-active-nav="">
              <a href="/ng-login/">Home</a>
            </li>
            <li data-xp-access-level="accessLevels.user" data-xp-active-nav="nestedTop">
              <a href="private">Private</a>
            </li>
            <li data-xp-access-level="accessLevels.admin" data-xp-active-nav="">
              <a href="admin">Admin</a>
            </li>
            <li data-xp-access-level="accessLevels.user">
              <a href="" data-ng-click="logout()">Log out</a>
            </li>

            <div id="userInfo" data-xp-access-level="accessLevels.user" class="pull-right">
            Welcome&nbsp;<strong>{{ user.username }}&nbsp;</strong>
            <span data-ng-class="{'label-info': user.role.title == userRoles.user.title, 'label-success': user.role.title == userRoles.admin.title}" class="label">{{ user.role.title }}</span>
            </div>
          </ul>
        </div>
      </div>
    </div>

    <div data-ui-view class="container"></div>

    <div id="alertBox" data-ng-show="error" class="alert alert-danger">
      <button type="button" data-ng-click="error = null;" class="close">&times;</button>
      <strong>Oh no!&nbsp;</strong>
      <span data-ng-bind="error"></span>
    </div>

    <script src="http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.4.4/underscore-min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.5/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.5/angular-cookies.min.js"></script>
    <script src="js/libs/angular-ui-router.js"></script>

    <script src="js/routingConfig.js"></script>
    <script src="js/app.js"></script>
    <script src="js/services/xpAuth.js"></script>
    <script src="js/services/xpUsers.js"></script>
    <script src="js/controllers/xpAdminCtrl.js"></script>
    <script src="js/controllers/xpLoginCtrl.js"></script>
    <script src="js/controllers/xpNavCtrl.js"></script>
    <script src="js/controllers/xpRegisterCtrl.js"></script>
    <script src="js/directives/xpAccessLevel.js"></script>
    <script src="js/directives/xpActiveNav.js"></script>
    
    <script>
      /* Give token to Angular client */
      xpLoginApp.constant("CSRF_TOKEN", '<?=$_SESSION['XSRF'];?>'); 
    </script>

  </body>
</html>