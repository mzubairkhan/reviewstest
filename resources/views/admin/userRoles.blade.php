@include('templates.header')
<script>
    $(function(){

        var user_id = '<?php echo $option['params']['user_id']; ?>';
        BI.Admin.showUserRoles(user_id);

        $(document).on("click", "#btn-AssociateRolesToUser", function () {
            var arr = [];
            $("input:checkbox[name=chk-Generic]:checked").each(function()
            {
              arr.push($(this).val());
            });
            var arr_str = JSON.stringify(arr);
            BI.Admin.associateRolesToUser(user_id,arr_str);
        });

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
                            <div class="checkbox-list" id="div-userRolesList">
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" id="btn-AssociateRolesToUser" class="btn green">
                                Associate
                            </button>
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
