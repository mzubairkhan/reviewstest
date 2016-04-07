<!DOCTYPE html>

<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->

<!-- BEGIN HEAD -->
<head>
    @include('templates.head')
   <link href="{!! asset('css/pages/login.css') !!}" media="all" rel="stylesheet" type="text/css" />

</head>

<script>
    <?php

       /* $bi = $this->config->item('bi');
        $api_path = $bi['api_path'];
        $api_header = $bi['api_header'];*/

     ?>

    var api_path = "http://dev.laravel.com/api/api/public";
    var api_header = "*PVPN1234567#";
    var BI = BI || {};
    BI.Config = {
        'api_path' : api_path,
        'api_header' : api_header,
        'mode' : 'production'
    };
</script>



    @include('templates.js')
<script type="text/javascript" src="{!! asset('scripts/custom/login.js') !!}"></script>


<script>
    jQuery(document).ready(function() {
        App.init();
        //Login.init();
    });
</script>