<!DOCTYPE html>

<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->

<!-- BEGIN HEAD -->
<head>
    @include('templates.head')

</head>
<!-- END HEAD -->
<script>
    <?php

        //$bi = $this->config->item('bi');
       // $api_path = $bi['api_path'];
       // $api_header = $bi['api_header'];

         $api_path = "http://dev.laravel.com/api/api/public";
         $api_header = "*PVPN1234567#";

           $data= Session::get('logged_in');
            $mode = $data['mode'];
            $token = $data['token'];
            $user_id = $data['userid'];
            $user_roles = $data['rules'];
     ?>

    var api_path = "<?php echo $api_path; ?>";
    var api_header = "<?php echo $api_header; ?>";
    var mode = "<?php echo $mode; ?>";
    var base_path = "{{ url('/') }}";
    var provider_s3 = "<?php echo 'http://d2m0vedobb1qh5.cloudfront.net/apps/'; ?>";
    var website_s3 = "<?php echo 'http://d2m0vedobb1qh5.cloudfront.net/apps/'; ?>";
    var media_s3 = "<?php echo 'http://d2m0vedobb1qh5.cloudfront.net/media/'; ?>";
    var token = "<?php echo $token;?>";
    var user_id = "<?php  echo $user_id;?>";
    var user_roles = '<?php echo json_encode($user_roles); ?>';
    var BI = BI || {};

    BI.Config = {
        'api_path' : api_path,
        'api_header' : api_header,
        'mode' : mode,
        'base_path' : base_path,
        'provider_s3' : provider_s3,
        'website_s3' : website_s3,
        'media_s3' : media_s3,
        'token' : token,
        'user_id' : user_id,
        'name' : '',
        'roles' : user_roles
    };
</script>
<!-- BEGIN BODY -->
<body class="page-header-fixed">
@include('templates.js')


<script type="text/javascript">
    $( document ).ready(function() {
    var web_title = $.cookie("site_title");

    $('#select_site').text(web_title);
    BI.Common.getWebsiteList();



    $( ".select_website" ).delegate( "a", "click", function() {
        var that=$(this);
        var website_id=that.data('website_id');
        var website_title=that.data('website_title');

            document.cookie='site_id='+website_id;
            document.cookie='site_title='+website_title;
            BI.Admin.Pages.listAllPages();

            var web_title = $.cookie("site_title");
            $('#select_site').text(web_title);

        });
    });

    BI.Config.roles = $.parseJSON(user_roles);
</script>

<!-- BEGIN HEADER -->
<div class="header navbar navbar-fixed-top" style="">
<!-- BEGIN TOP NAVIGATION BAR -->
<div class="header-inner">
<!-- BEGIN LOGO -->
<a style="color: #fff;" class="navbar-brand" href="{{ url('/') }}">
    <h4 style="  color: #fff;margin-left: 24px;margin-top: 0;">Reviews Websites CMS </h4>
</a>
<!-- END LOGO -->
<!-- BEGIN RESPONSIVE MENU TOGGLER -->
<a href="javascript:;" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse" style="background: #C2C2C2;padding-left: 5px;padding-right: 5px;">
    <img src="<?php  echo asset('public/img/menu-toggler.png');?>" alt=""/>
</a>
<!-- END RESPONSIVE MENU TOGGLER -->
<!-- BEGIN TOP NAVIGATION MENU -->
<ul class="nav navbar-nav pull-right">
<?php
    //if($session_data['logged_in']['is_admin'] == 1) {
?>

<?php //} ?>
<!-- BEGIN USER LOGIN DROPDOWN -->
    <li class="dropdown user">

        <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
            <!--<img alt="" src="<?php /*echo asset_url(); */?>img/avatar1_small.jpg"/>-->
                <span class="username" id="select_site" style=""> Select Website
                </span>
            <i class="fa fa-angle-down"></i>
        </a>
        <ul class="dropdown-menu select_website">


        </ul>
    </li>
    <li class="dropdown user">

    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
        <!--<img alt="" src="<?php /*echo asset_url(); */?>img/avatar1_small.jpg"/>-->
            <span id="span-userName" class="username" style="">
            </span>
        <i class="fa fa-angle-down"></i>
    </a>
    <ul class="dropdown-menu">
        <!--<li>
            <a href="<?php /*echo base_url() ;*/?>user/profile">
                <i class="fa fa-user"></i> My Profile
            </a>
        </li>
        <li class="divider">
        </li>-->
        <li>
            <a href="{{ url('user/logout') }}">
                <i class="fa fa-key"></i> Log Out
            </a>
        </li>
    </ul>
</li>
<!-- END USER LOGIN DROPDOWN -->
</ul>
<!-- END TOP NAVIGATION MENU -->
</div>
<!-- END TOP NAVIGATION BAR -->
</div>
<!-- END HEADER -->