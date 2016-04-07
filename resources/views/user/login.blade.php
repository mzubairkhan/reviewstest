@include('templates.login_header')

<script type="text/javascript">
    $(function(){

        $(document).on("click", "#btn-login", function () {

            var username = $('#username').val();
            var password = $('#password').val();
            BI.Admin.User.authenticate(username,password);
        });

        $(document).keypress(function(e) {
            if(e.which == 13) {
                var username = $('#username').val();
                var password = $('#password').val();
                BI.Admin.User.authenticate(username,password);
            }
        });
    });
</script>
<style>
    .login .logo {
        margin: 0 auto;
        margin-top: 60px;
        padding: 15px;
        background: white;
        text-align: center;
        width: 360px;
        border-bottom: 1px solid green;
    }
</style>
<!-- BEGIN BODY -->
<body class="login">
<!-- BEGIN LOGO -->
<div class="logo">

    <a href="   {{ url('/') }}">
        <img src="<?php  echo asset('img/logo_purevpn.png');?>" alt=""/>
    </a>
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
<!-- BEGIN LOGIN FORM -->
<form class="login-form" method="post">
    <h3 class="form-title">Login to your account</h3>
    <div class="alert alert-danger display-hide">
        <button class="close" data-close="alert"></button>
			<span>
				 Enter any username and password.
			</span>
    </div>
    <div class="form-group">
        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
        <label class="control-label visible-ie8 visible-ie9">Username</label>
        <div class="input-icon">
            <i class="fa fa-user"></i>
            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username" id="username"/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label visible-ie8 visible-ie9">Password</label>
        <div class="input-icon">
            <i class="fa fa-lock"></i>
            <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password" id="password"/>
        </div>
    </div>
    <div class="form-actions">
       <span id="login-error"></span>
       <button type="button" class="btn green pull-right" id="btn-login">
            Login <i class="m-icon-swapright m-icon-white"></i>
        </button>
    </div>
</form>
<!-- END LOGIN FORM -->

</div>
<!-- END LOGIN -->

@include('templates.login_footer')