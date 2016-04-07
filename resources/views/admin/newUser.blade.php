@include('templates.header')
<script type="text/javascript">

    var user_id = 0;
    <?php if(isset($option['params']['userId']))  {
            $userId= $option['params']['userId'];
    ?>
    user_id = '<?php echo $userId; ?>';
    <?php } ?>

    $(function(){

        if(user_id != 0) {

            BI.Admin.User.getUser(user_id);
        }
        $(document).on("click", "#btn-CreateUser", function () {

            var username = $('#username').val();
            var password = $('#password').val();
            var rpassword = $('#rpassword').val();
            var email = $('#email').val();


         /*   if(password != rpassword) {
                return;
            }
            if($.trim(username) == '' || $.trim(password) == '' || $.trim(email) == '' ) {
                return;
            }
*/
            var first_name = $('#first_name').val();
            var last_name = $('#last_name').val();

            var isFix = 0;
            if($('#chk-isFix').is(':checked')) {
                isFix = 1;
            }

            var isPercent = 0;
            if($('#chk-isPercent').is(':checked')) {
                isPercent = 1;
            }

            var percentValue = $('#percentValue').val();
            var fixValue = $('#fixValue').val();
            var extra = {};
            extra.isFix = isFix;
            extra.isPercent = isPercent;
            extra.percentValue = percentValue;
            extra.fixValue = fixValue;
            var extraStr = JSON.stringify(extra);


            BI.Admin.User.createUser(username, email, password, first_name, last_name , extraStr) ;
        });

        $(document).on("click", "#btn-UpdateUser", function () {

            var isFix = 0;
            if($('#chk-isFix').is(':checked')) {
                isFix = 1;
            }

            var isPercent = 0;
            if($('#chk-isPercent').is(':checked')) {
                isPercent = 1;
            }

            var percentValue = $('#percentValue').val();
            var fixValue = $('#fixValue').val();
            var extra = {};
            extra.isFix = isFix;
            extra.isPercent = isPercent;
            extra.percentValue = percentValue;
            extra.fixValue = fixValue;
            var extraStr = JSON.stringify(extra);

            var user_id = $('#user_id').val();
            BI.Admin.User.updateUser(user_id, extraStr);
        });

        $('#chk-isFix').change(function(event) {
            $('#fixValue').prop('disabled',!$('#fixValue').prop('disabled'));
        });
        $('#chk-isPercent').change(function(event) {
            $('#percentValue').prop('disabled',!$('#percentValue').prop('disabled'));
        });

    });
</script>
<style>
    .mrg-Admin { margin-left: 225px!important;}
</style>
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">

    <!-- BEGIN SIDEBAR -->
    @include('templates.admin_sidebar')

    <!-- END SIDEBAR -->

    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content mrg-Admin">

            @include('templates.admin_topbar')


            <!-- END DASHBOARD STATS -->

            <div class="row" id="div-CreateUser">
                <div class="col-md-12">
                    <!-- BEGIN REGISTRATION FORM -->
                    <form class="register-form" method="post">
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        <span class="message"></span>
                    </div>
                    <input type="hidden" name="user_id" id="user_id" value="" />
                    <?php if(!isset($userId))  { ?>

                            <p>
                        Enter your personal details below:
                    </p>
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9">First Name</label>
                        <div class="input-icon">
                            <i class="fa fa-font"></i>
                            <input class="form-control placeholder-no-fix" type="text" placeholder="First Name" name="first_name" id="first_name"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9">Last Name</label>
                        <div class="input-icon">
                            <i class="fa fa-font"></i>
                            <input class="form-control placeholder-no-fix" type="text" placeholder="Last Name" name="last_name" id="last_name"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                        <label class="control-label visible-ie8 visible-ie9">Email</label>
                        <div class="input-icon">
                            <i class="fa fa-envelope"></i>
                            <input class="form-control placeholder-no-fix" type="email" placeholder="Email" name="email" id="email"/>
                        </div>
                    </div>

                    <p>
                        Enter your account details below:
                    </p>
                    <div class="form-group">
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
                                <input class="form-control placeholder-no-fix" type="password" autocomplete="off" id="password" placeholder="Password" name="password"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label visible-ie8 visible-ie9">Re-type Your Password</label>
                            <div class="controls">
                                <div class="input-icon">
                                    <i class="fa fa-check"></i>
                                    <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Re-type Your Password" name="rpassword" id="rpassword" />
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <p>
                        Partner Coupon Settings:
                    </p>
                    <div class="form-group">
                        <div class="checkbox-list">
                            <input type="checkbox" id="chk-isFix" value="true"> Allow Fix Value
                            <input type="text" autocomplete="off"  name="fixValue" id="fixValue" disabled />
                        </div>
                        <br/>
                        <div class="checkbox-list">
                            <input type="checkbox" id="chk-isPercent" value="true"> Allow Percentage Value
                            <input type="text" autocomplete="off" name="percentValue" id="percentValue" disabled />
                        </div>
                    </div>



                    <div class="form-actions">
                        <?php if(isset($userId))  {?>
                        <button type="button" id="btn-UpdateUser" class="btn green pull-right">
                           Update Partner <i class="m-icon-swapright m-icon-white"></i>
                        </button>
                            <?php } else { ?>
                        <button type="button" id="btn-CreateUser" class="btn green pull-right">
                            Add Partner <i class="m-icon-swapright m-icon-white"></i>
                        </button>
                        <?php } ?>

                    </div>
                    </form>
                    <!-- END REGISTRATION FORM -->
                </div>
            </div>



            <div class="clearfix">
            </div>
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
