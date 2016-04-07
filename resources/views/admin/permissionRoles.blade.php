@include('templates.header')
<script>
    $(function(){
        var permission_id = '<?php echo $option['params']['permission_id'];?>';
        BI.Admin.showPermissionRoles(permission_id);


    });
</script>
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
            <div class="row" id="div-Roles">
                <div class="col-md-12">

                    <form class="permission-form" method="post">

                        <div class="form-group">
                            <div id="div-permissionRolesList">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="clearfix">
            </div>
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
@include('templates.footer')