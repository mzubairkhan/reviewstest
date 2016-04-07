@include('templates.header')
<script type="text/javascript">

    var cf_id = 0;
    <?php if(isset($option['params']['cf_id']))  {
    $cf_id=$option['params']['cf_id'];
    ?>
    cf_id = '<?php echo $cf_id; ?>';
    <?php } ?>

    $(function(){
        BI.Admin.CustomField.manageFields(cf_id);

        if(cf_id !== 0) {

            BI.Admin.CustomField.getFieldGroup(cf_id );
            BI.Admin.CustomField.getGroupFields(cf_id, "created_on");
        }

        $("#submit_form").click(function(){
            $(this).attr("disabled", "disabled").html("Updating !");
            var $data = $("#form").submit().serialize();

            if(cf_id  !== 0) {
                BI.Admin.CustomField.updateGroup($data);
            } else {
                BI.Admin.CustomField.createGroup($data);
            }

        });



        $( "input[name=title]" ).blur(function() {
            var title_value = $(this).val();
            $("input[name=key]").val(BI.Common.convertToSlug(title_value));
        });

        $("#cancel").click(function(){
            window.location.replace(BI.Config.base_path+"admin/customfields");
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
                    <form id="form" action="javascript:void(0)" class="form-horizontal page_new" method="post">
                        <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        <span class="message"></span>

                         <?php if(isset($cf_id)) { ?>
                            <input type="hidden" class="group_id" name="id" value="<?php echo $cf_id ?>"/>
                        <?php } ?>
                    </div>

                        <div class="tab-pane  active" id="tab_2">

                                <div class="portlet-body form">
                                    <!-- BEGIN FORM-->

                                        <div class="form-body">
                                            <h3 class="form-section">Custom Field Group Info</h3>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Title</label>
                                                        <div class="col-md-9">
                                                            <input name="title" type="text" class="form-control">

                                                        </div>
                                                    </div>




                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Select Module</label>
                                                        <div class="col-md-9">
                                                            <select name="module_type" class="form-control module_type">
                                                                <option value="website">Websites</option>
                                                                <option value="provider">Provider Profile</option>
                                                                <option value="page">Pages</option>
                                                            </select>
                                                        </div>

                                                     </div>

                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Status</label>
                                                        <div class="col-md-9">
                                                            <select name="status" class="form-control status">
                                                                <option value="0">Disable</option>
                                                                <option value="1">Enable</option>
                                                            </select>
                                                        </div>

                                                     </div>

                                            </div>


                                            </div>
                                            <!--/row-->
                                        </div>
                                </div>

                        </div>
                        <div class="col-md-12">

                            <h3 class="form-section">Fields</h3>
                            <?php if(!empty($cf_id)): ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-ellipsis-horizontal"></i> <i class="fa fa-plus"></i> Add new field <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu" id="cf_add">
                                    <li>
                                        <a class="btn_link" data-cftype="input" href="javascript:void(0)">
                                            Text Input
                                        </a>
                                    </li>
                                    <li>
                                        <a class="btn_link" data-cftype="textarea" href="javascript:void(0)">
                                            Textarea
                                        </a>
                                    </li>
                                    <li>
                                        <a class="btn_link" data-cftype="select" href="javascript:void(0)">
                                            Select Box
                                        </a>
                                    </li>
                                    <li>
                                        <a class="btn_link" data-cftype="tags" href="javascript:void(0)">
                                            Tag box
                                        </a>
                                    </li>
                                    <li>
                                        <a class="btn_link" data-cftype="checkbox" href="javascript:void(0)">
                                            Checkbox
                                        </a>
                                    </li>
                                    <li>
                                        <a class="btn_link" data-cftype="boolean" href="javascript:void(0)">
                                            True/False
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <hr />
                            <?php endif; ?>
                            <div id="cf_group_fields">

                            </div>


                            <div class="clearfix"></div>

                            <div class="form-actions fluid">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-offset-3 col-md-9">
                                            <button id="submit_form" type="submit" class="btn green">Update</button>
                                            <button id="cancel" type="button" class="btn default">Cancel</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                    </div>
                                </div>
                            </div>
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
@include('templates.footer')

