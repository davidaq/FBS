<?php include 'logic/entry.php'; ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <title>Sign In</title>
    <!-- Le styles -->
    <link href="static/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 70px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

      .form-signin {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }

      .form-signin .help-inline {
        display: none;
      }
      .form-signin .error .help-inline {
        display: inline;
      }

    </style>
    <link href="static/css/bootstrap-responsive.min.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="static/js/html5shiv.js"></script>
    <![endif]-->
    <script src="static/js/jquery.min.js"></script>
    <script type="text/javascript">
        $(function() {
            var loginResult = '<?php echo $result; ?>';
            if(loginResult == 'username') {
                $('#usernameField').addClass('error');
            } else if(loginResult == 'password') {
                $('#passwordField').addClass('error');
            }
        });
    </script>
    {:fillForms();}
  </head>

  <body>
    <div class="container">

      <form class="form-signin" action="login.php" method="post">
        <h2 class="form-signin-heading">Please sign in</h2>
        <div class="control-group" id="usernameField">
            <input type="text" class="input-block-level" placeholder="User name" name="login.username">
            <span class="help-inline">User account doesn't exist</span>
        </div>
        <div class="control-group" id="passwordField">
            <input type="password" class="input-block-level" placeholder="Password" name="login.password">
            <span class="help-inline">Password incorrect</span>
        </div>
        <label class="checkbox">
          <input type="checkbox" value="true" name="login.remember"> Remember me
        </label>
        <input type="hidden" name="submit" value="true"/>
        <input class="btn btn-large btn-primary" type="submit" value="Sign in"/>
      </form>

    </div> <!-- /container -->

    <script src="static/js/bootstrap.min.js"></script>
  </body>
</html>

