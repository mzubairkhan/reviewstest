@include('templates.header')
<script type="text/javascript">

    var param_id = 0;
    <?php if(isset($option['params']['pageId']))  {
    $pageId=$option['params']['pageId'];?>
    param_id = '<?php echo $pageId; ?>';
    <?php } ?>

    $(function(){

        BI.Admin.Pages.getWebsitesList();

        if(param_id !== 0) {

            BI.Admin.Pages.getPage(param_id);
        }


        BI.Admin.CustomField.getFieldsByModule("page", param_id);


        $("#form").submit(function(){
            $('#submit_form').attr("disabled", "disabled").html("Updating...");
            BI.Common.CKupdate();
            var $data = $(this).serialize();
            if(param_id !== 0) {
                BI.Admin.Pages.updatePage($data );
                var forms = $('.cf_form');
                $.each(forms, function(k,v){
                    var cf_data = $(v).serialize();
                    BI.Admin.CustomField.updateFieldByModule("page", cf_data);
                });
            } else
            {
                BI.Admin.Pages.createPage($data );
            }
            return false;
        });

        $("#cancel").click(function(){
            window.location.replace(BI.Config.base_path+"/admin/pages");
        });

        $("#meta_keywords").select2({
            tags: []
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
                        <input id="page_id" name="page_id" type="hidden" class="form-control">
                        <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        <span class="message"></span>
                    </div>

                        <div class="tab-pane  active" id="tab_2">

                                <div class="portlet-body form">
                                    <!-- BEGIN FORM-->

                                        <div class="form-body">
                                            <h3 class="form-section">Required Info</h3>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Title</label>
                                                        <div class="col-md-9">
                                                            <input name="title" type="text" class="form-control" placeholder="Page title">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Status</label>
                                                        <div class="col-md-9">
                                                            <select name="status" class="form-control">
                                                                <option value="1">Enable</option>
                                                                <option value="0">Disable</option>
                                                            </select>
                                                        </div>

                                                     </div>


                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Select Website</label>
                                                        <div class="col-md-9">
                                                            <select name="website_id" class="form-control" id="website_id" required="">
                                                                <option value=""></option>
                                                            </select>
                                                        </div>
                                                    </div>

                                            </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Meta Title</label>
                                                        <div class="col-md-9">
                                                            <input id="meta_title" name="meta[title]" type="text" class="form-control">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Keyword</label>
                                                        <div class="col-md-9">
                                                            <input name="meta[keywords]" type="hidden" id="meta_keywords" class="form-control select2" value="">

                                                        </div>
                                                    </div>




                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Description</label>
                                                        <div class="col-md-9">
                                                            <input id="meta_description" name="meta[description]" type="text" class="form-control">
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="col-md-12">
                                                    <h3 class="form-section">Content</h3>

                                                    <div class="form-group last">

                                                        <div class="col-md-12">
                                                            <textarea class="ckeditor form-control" name="content" rows="6" data-error-container="#editor2_error"></textarea>
                                                            <div id="editor2_error">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--/row-->
                                        </div>
                                        <div class="clearfix"></div>

                                    <div id="cf_group_fields">
                                        <h3 class="form-section">Fields</h3>
                                    </div>

                                        <div class="form-actions fluid">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="col-md-offset-3 col-md-9">
                                                        <button id="submit_form" type="submit" class="btn green">Submit</button>
                                                        <button id="cancel" type="button" class="btn default">Cancel</button>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                </div>
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
