<?php
    include 'logic/entry.php';
    function isactive($p) {
        return $p == PAGE ? 'class="active"':'';
    }
    if(R('cheml')) {
        $DB->query('update `%%user` set `email` = {$cheml} where `id` = ' . USERID);
        $_SESSION['info'] = 'Email changed to ' . R('cheml') . ' for current account';
        header('location: ' . PAGE);
    }
    if(R('chpwd')) {
        if(R('chpwd.new') != R('chpwd.confirm'))
            $_SESSION['info'] = 'Password confirm doesn\'t match';
        else {
            $res = $DB->query('update `%%user` set `password` = md5(concat("' . SEED . '", {$chpwd.new})) where `id` = ' . USERID . ' and `password` = md5(concat("' . SEED . '", {$chpwd.old}))');
            if($res && $DB->affected())
                $_SESSION['info'] = 'Password changed for current account';
            else
                $_SESSION['info'] = 'Password not change, maybe the old password is wrong or the new password is the same as the old';
        }
        header('location: ' . PAGE);
    }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <title>Factory Business Simulation System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Le styles -->
    <link href="static/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }

      @media (max-width: 980px) {
        /* Enable use of floated navbar text */
        .navbar-text.pull-right {
          float: none;
          padding-left: 5px;
          padding-right: 5px;
        }
      }
    </style>
    <link href="static/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link href="static/css/common.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="static/js/html5shiv.js"></script>
    <![endif]-->
    <script src="static/js/jquery.min.js"></script>
    <script type="text/javascript">
        function chpwd() {
            $('#chpwdDlg').modal();
        }
        function cheml() {
            $('#chemlDlg').modal();
        }
        $(function() {
            $('.tiped').tooltip();
            if($('.bs-docs-sidebar')[0]) {
                $('#topAlertBar').css({textIndent: 350});
            }
            $('#topAlertBar .padding').animate({width:20},150).animate({width:0},150).animate({width:20},150).animate({width:0},150);
        });
    </script>

  </head>

  <body>

    <div id="chemlDlg" class="modal hide fade">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Change email</h3>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" action="{:PAGE}" method="post" id="chemlForm">
            <div class="control-group">
                <label class="control-label" for="cheml">
                    Email
                </label>
                <div class="controls">
                    <input type="text" id="cheml" name="cheml" value="{:USEREMAIL}"/>
                </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal">Cancel</a>
        <a href="#" class="btn btn-primary" onclick="$('#chemlForm').submit()">Submit</a>
      </div>
    </div>
    
    <div id="chpwdDlg" class="modal hide fade">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Change password</h3>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" action="{:PAGE}" method="post" id="chpwdForm">
            <div class="control-group">
                <label class="control-label" for="chpwd.old">
                    Old password
                </label>
                <div class="controls">
                    <input type="password" id="chpwd.old" name="chpwd.old"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="chpwd.new">
                    New password
                </label>
                <div class="controls">
                    <input type="password" id="chpwd.new" name="chpwd.new"/>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="chpwd.confirm">
                    Confirm password
                </label>
                <div class="controls">
                    <input type="password" id="chpwd.confirm" name="chpwd.confirm"/>
                </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal">Cancel</a>
        <a href="#" class="btn btn-primary" onclick="$('#chpwdForm').submit()">Submit</a>
      </div>
    </div>
    
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid fixwidth-content">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <div class="nav-collapse collapse">
            <ul class="nav pull-right">
              <li class="navbar-text">
                Logged in as
              </li>
              <li class="dropdown">
                <a href="#" style="padding-left: 7px" class="dropdown-toggle" data-toggle="dropdown">
                    <b>{:USERNAME}</b>
                    [{:USEREMAIL}]
                    <i class="caret"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="javascript:chpwd();">Change password</a></li>
                    <li><a href="javascript:cheml();">Change email</a></li>
                    <li class="divider"></li>
                    <li><a href="login.php">Exit</a></li>
                </ul>
              </li>
            </ul>
            <ul class="nav">
              <li {:isactive('index.php')}><a href="index.php">Home</a></li>
              <li {:isactive('gamelist.php')}><a href="gamelist.php">Existing Simulations</a></li>
              <li {:isactive('startgame.php')}><a href="startgame.php">Start New Simulation</a></li>
              <li {:isactive('subaccount.php')}><a href="subaccount.php">Sub-Accounts</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <?php if(SYSINFO) { ?>
        <div id="topAlertBar" class="fixwidth-content alert alert-success" style="margin-top: 10px; margin-bottom: 20px;">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <input type="text" style="visibility:hidden; width:0px;height:10px;padding:0;margin:0;" class="padding"/>
            {:SYSINFO}
        </div>
    <?php } ?>
