@include('templates.header')
<script type="text-template" id="tmp_SimpleTable">
    <table class="table table-bordered table-striped table-condensed flip-content" id="tbl-SimpleTable">
        <thead class="flip-content">
        <tr>
            ((header_columns))
        </tr>
        </thead>
        <tbody>
        ((rows))

        </tbody>
    </table>
</script>

<script>
    $(function(){

        var params = $.parseJSON('<?php echo json_encode($option['params']); ?>');
        if(params.message != undefined ) {
            BI.Common.showPageSuccess(params.message);
        }

        $("#form").submit(function(e){

            $('#submit_form').attr("disabled", "disabled").html("Uploading...");
            e.preventDefault();
            var $data = new FormData(this);
            BI.Admin.Media.upload($data );
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

    <div class="page-content-wrapper">
        <div class="page-content mrg-Admin">
            @include('templates.admin_topbar')



            <!-- END DASHBOARD STATS -->

            <div class="row" id="div-CreateUser">
                <div class="col-md-12">
                    <!-- BEGIN REGISTRATION FORM -->
                    <form id="form" action="javascript:void(0)" class="form-horizontal page_new" method="post" enctype="multipart/form-data">

                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button>
                            <span class="message"></span>
                        </div>

                        <div class="tab-pane  active" id="tab_2">

                            <div class="portlet-body form">
                                <!-- BEGIN FORM-->

                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="col-md-9">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                            <img id="file" src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt=""/>
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
                                                                        <input type="file" name="file">
                                                                    </span>
                                                            <a href="#" class="btn default fileinput-exists remove_image" data-dismiss="fileinput">
                                                                Remove
                                                            </a>
                                                        </div>
                                                    </div>

                                                    <div class="image_url"> </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!--/row-->
                                </div>
                            </div>

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
</div>
<!-- END CONTAINER -->
@include('templates.footer')