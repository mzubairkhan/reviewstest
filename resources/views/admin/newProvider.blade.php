@include('templates.header')
<script type="text/javascript">

    var provider_id = 0;
    <?php if(isset($option['params']['providerId']))  {
    $providerId=$option['params']['providerId'];
    ?>

    provider_id = '<?php echo $providerId; ?>';
    <?php } ?>

    $(function(){

        if(provider_id !== 0) {

            BI.Admin.Providers.get(provider_id);
        }

        BI.Admin.CustomField.getFieldsByModule("provider", provider_id);

        $("#form").submit(function(e){
            $('#submit_form').attr("disabled", "disabled").html("Updating...");
            e.preventDefault();
            var $data = new FormData(this);
            if(provider_id !== 0) {
                BI.Admin.Providers.update($data );
                var forms = $('.cf_form');
                $.each(forms, function(k,v){
                    var cf_data = $(v).serialize();
                    BI.Admin.CustomField.updateFieldByModule("provider", cf_data);
                });
            } else {
                BI.Admin.Providers.create($data );
            }
        });

        $("#cancel").click(function(){
            window.location.replace(BI.Config.base_path+"/admin/providers");
        });

        $('.remove_image').click(function(){
            $("#logo").val('');
        })



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
                    <form id="form" action="javascript:void(0)" class="form-horizontal page_new" method="post" enctype="multipart/form-data">
                        <input id="provider" name="provider" type="hidden" class="form-control">
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
                                                        <label class="control-label col-md-3">Price</label>
                                                        <div class="col-md-9">
                                                            <input name="price" type="text" class="form-control" placeholder="eg. 4.5">
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Visit Link</label>
                                                        <div class="col-md-9">
                                                            <input name="visit_link" type="text" class="form-control" placeholder="http://abc.com?aff=1">
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Discount</label>
                                                        <div class="col-md-9">
                                                            <input name="discount" type="text" class="form-control" placeholder="eg. 10">
                                                        </div>
                                                    </div>

                                            </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Provider logo</label>
                                                        <div class="col-md-9">
                                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                                <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                                    <img id="website_logo" src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt=""/>
                                                                </div>
                                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                                                                </div>
                                                                <div>
                                                                    <span class="btn default btn-file">
                                                                        <span class="fileinput-new">
                                                                             Select image
                                                                        </span>
                                                                        <span class="fileinput-exists">
                                                                             Change
                                                                        </span>
                                                                        <input type="file" name="provider_logo">
                                                                    </span>
                                                                    <a href="#" class="btn default fileinput-exists remove_image" data-dismiss="fileinput">
                                                                        Remove
                                                                    </a>
                                                                </div>
                                                                <input id="logo" name="logo" type="hidden" class="" value="">
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                              <!--  <div class="col-md-12">
                                                    <h3 class="form-section">Description</h3>

                                                    <div class="form-group last">

                                                        <div class="col-md-12">
                                                            <textarea class="ckeditor form-control" name="description" rows="6" data-error-container="#editor2_error"></textarea>
                                                            <div id="editor2_error">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>-->


                                            </div>
                                            <!--/row-->
                                        </div>
                                </div>

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
