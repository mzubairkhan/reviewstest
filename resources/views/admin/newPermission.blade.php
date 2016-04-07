@include('templates.header')
<script type="text/javascript">

    var permission_id = 0;
    <?php if(isset($option['params']['id']))  {
        $id=$option['params']['id'];?>
        permission_id = '<?php echo $id; ?>';
    <?php } ?>



    $(function(){

        BI.Admin.populateModules(permission_id);

        $(document).on("click", "#btn-CreatePermission", function () {
            var title = $('#title').val();
            if($.trim(title) == '') {
                return;
            }
            var isBackEnd = 0;
            if($('#chk-IsBackend').is(':checked')) {
                isBackEnd = 1;
            }
            var moduleId = $('#sel-moduleList').val();

            BI.Admin.createPermission(title, isBackEnd, moduleId , 0);
        });


        $(document).keypress(function(e) {
            if(e.which == 13) {
                var title = $('#title').val();
                if($.trim(title) == '') {
                    return;
                }
                var isBackEnd = 0;
                if($('#chk-IsBackend').is(':checked')) {
                    isBackEnd = 1;
                }
                var moduleId = $('#sel-moduleList').val();

                BI.Admin.createPermission(title, isBackEnd, moduleId , 0);
            }
        });

        $(document).on("click", "#btn-UpdatePermission", function () {
            var title = $('#title').val();
            if($.trim(title) == '') {
                return;
            }
            var isBackEnd = 0;
            if($('#chk-IsBackend').is(':checked')) {
                isBackEnd = 1;
            }
            var moduleId = $('#sel-moduleList').val();
            var permissionId = $('#permission_id').val();
            BI.Admin.createPermission(title, isBackEnd, moduleId ,permissionId);
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

            <div class="row" id="div-CreateUser">
                <div class="col-md-12">
                    <!-- BEGIN REGISTRATION FORM -->
                    <form class="permission-form" method="post">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button>
                            <span class="message"></span>
                        </div>
                        <h3>Add Permission</h3>
                        <div class="form-group">
                            <label class="control-label visible-ie8 visible-ie9">Title</label>
                            <div class="input-icon">
                                <i class="fa fa-font"></i>
                                <input class="form-control placeholder-no-fix" type="text" placeholder="Title" name="title" id="title"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox-list">
                                <input type="checkbox" id="chk-IsBackend" value="true"> Backend
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                <label class="control-label">Module:</label>
                                <select class="form-control" id="sel-moduleList">
                                </select>
                            </div>
                        </div>
                        <?php if(isset($id))  {?>
                            <input type="hidden" name="id" id="permission_id" value="<?php echo $id; ?>"/>
                            <div class="form-actions">
                                <button type="button" id="btn-UpdatePermission" class="btn green">
                                    Save
                                </button>
                            </div>
                        <?php } else {?>
                        <div class="form-actions">
                            <button type="button" id="btn-CreatePermission" class="btn green">
                                Add
                            </button>
                        </div>
                        <?php } ?>
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

@include('templates.footer')
