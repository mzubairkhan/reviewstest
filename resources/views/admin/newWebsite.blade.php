@include('templates.header')
<script type="text/javascript">

    var param_id = 0;
    <?php if(isset($option['params']['websiteId']))  {
    $websiteId=$option['params']['websiteId'];
    ?>
    param_id = '<?php echo $websiteId; ?>';
    //alert(param_id);
    <?php } ?>

    $(function(){

        if(param_id !== 0) {

            BI.Admin.Websites.getWebsite(param_id);
        }

        BI.Admin.CustomField.getFieldsByModule("website", param_id);


        $("#form").submit(function(e){
            $('#submit_form').attr("disabled", "disabled").html("Updating...");
            e.preventDefault();
            var fromd = new FormData(this);
            if(param_id !== 0) {
               BI.Admin.Websites.updateWebsite(fromd );
                var forms = $('.cf_form');
                $.each(forms, function(k,v){
                    var cf_data = $(v).serialize();
                    BI.Admin.CustomField.updateFieldByModule("website", cf_data);
                });
            } else {
                BI.Admin.Websites.createWebsite(fromd );
            }
        });



        $("#cancel").click(function(){
            window.location.replace(BI.Config.base_path+"/admin/websites");
        });

        $("#meta_keywords").select2({
            tags: []
        });

        $("#setting_compare_vpn").select2({
            tags: []
        });


        $( ".colorpicker-default" ).blur(function() {
            var color = $(this).val();
            $(this).parent(".col-md-7").parent(".form-group").find(".current_color").css("background-color", color);

        });

        $('.remove_image').click(function(){
            $("#theme_logo").val('');
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

        {{--<form id="form" action="javascript:void(0)" class="form-horizontal page_new" method="post" enctype="multipart/form-data">--}}
                    {!! Form::open(['url' => 'website']) !!}
                        <input id="website_id" name="website_id" type="hidden" class="form-control" value="<?php if(isset($websiteId))  { echo $websiteId; } ?>">
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
                                                        <label class="control-label col-md-3">{!! Form::label('Title', 'Title:') !!}</label>
                                                        <div class="col-md-9">
                                                            {!! Form::text('title',null,[ 'class'=>'form-control', 'placeholder'=>'Page title']) !!}
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">{!! Form::label('Domain', 'Domain:') !!}</label>
                                                        <div class="col-md-9">
                                                            {!! Form::text('domain',null,[ 'class'=>'form-control', 'placeholder'=>'e.g www.expressvpnreviews.com']) !!}
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">{!! Form::label('Website Type', 'Website Type:') !!}</label>
                                                        <div class="col-md-9">
                                                            {!! Form::select('website_type',array('1' => 'Affiliate', '2' => 'Non Affiliate', '3' => 'Spam'),null ,[ 'class'=>'form-control']) !!}


                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">{!! Form::label('Server IP', 'Server IP:') !!}</label>
                                                        <div class="col-md-9 input-icon">
                                                            <i class="fa fa-globe"></i>
                                                            {!! Form::text('domain',null,[ 'class'=>'form-control', 'placeholder'=>'Optional']) !!}
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">{!! Form::label('GIT Repo', 'GIT Repo:') !!}</label>
                                                        <div class="col-md-9 input-icon">
                                                            <i class="fa fa-github"></i>
                                                            {!! Form::text('git_repo',null,[ 'class'=>'form-control', 'placeholder'=>'Optional']) !!}
                                                        </div>
                                                    </div>

                                            </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">{!! Form::label('Meta Title', 'Meta Title:') !!}</label>
                                                        <div class="col-md-9">
                                                            {!! Form::text('meta[title]',null,[ 'class'=>'form-control']) !!}
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">{!! Form::label('Keyword', 'Keyword:') !!}</label>
                                                        <div class="col-md-9">
                                                            {!! Form::text('meta[keywords]',null,[ 'class'=>'form-control']) !!}

                                                        </div>
                                                    </div>




                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">{!! Form::label('Description', 'Description:') !!}</label>
                                                        <div class="col-md-9">
                                                            {!! Form::text('meta[description]',null,[ 'class'=>'form-control']) !!}
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="clearfix">
                                                    <br />
                                                </div>
                                                <h3 class="form-section">Website Configuration </h3>
                                                <div class="col-md-6">

                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">{!! Form::label('Global title', 'Global title:') !!}</label>
                                                        <div class="col-md-9">
                                                            {!! Form::text('setting[global_title]',null,[ 'class'=>'form-control']) !!}

                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">{!! Form::label('CTA Text', 'CTA Text:') !!}</label>
                                                        <div class="col-md-9">
                                                            {!! Form::text('setting[cta_text]',null,[ 'class'=>'form-control','placeholder'=>'e.g GET VPN NOW']) !!}
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">{!! Form::label('CTA Link', 'CTA Link:') !!}</label>
                                                        <div class="col-md-9">
                                                            <input id="setting_cta_link" name="setting[cta_link]" type="text" class="form-control" placeholder="e.g http://www.abc.com?aff=1">
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">{!! Form::label('Copyright text', 'Copyright text:') !!}</label>
                                                        <div class="col-md-9">
                                                            {!! Form::text('setting[cp_text]',null,[ 'class'=>'form-control','placeholder'=>'e.g abc.com - All Rights Reserved']) !!}
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">{!! Form::label('Providers IDs', 'Providers IDs:') !!}</label>
                                                        <div class="col-md-9">
                                                            {!! Form::text('setting[cp_text]',null,[ 'class'=>'form-control','placeholder'=>'Provider for compare VPN page']) !!}
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">{!! Form::label('Featured Pro ID', 'Featured Pro ID:') !!}</label>
                                                        <div class="col-md-9">
                                                            {!! Form::text('setting[featured_pro]',null,[ 'class'=>'form-control','placeholder'=>'Featured Provider for compare VPN page']) !!}
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        {{--<label class="control-label col-md-3">Website logo</label>
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
                                                                        <input type="file" name="website_logo">
                                                                    </span>
                                                                    <a href="#" class="btn default fileinput-exists remove_image" data-dismiss="fileinput">
                                                                        Remove
                                                                    </a>
                                                                </div>
                                                                <input id="theme_logo" name="theme[logo]" type="hidden" class="" value="">
                                                            </div>

                                                        </div>--}}
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">{!! Form::label('Main color', 'Main color:') !!}</label>
                                                        <div class="col-md-7">
                                                            {!! Form::text('theme[main_color]',null,[ 'class'=>'colorpicker-default form-control','placeholder'=>'Featured Provider for compare VPN page']) !!}
                                                        </div>
                                                        <div class="col-md-2">
                                                            {!! Form::text(null,[ 'class'=>'main_color form-control current_color']) !!}
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">{!! Form::label('Links color', 'Links color:') !!}</label>
                                                        <div class="col-md-7">
                                                            <input id="theme_links_color" name="theme[links_color]" type="text" class="colorpicker-default form-control" value="">
                                                            {!! Form::text('theme[main_color]',null,[ 'class'=>'colorpicker-default form-control','placeholder'=>'Featured Provider for compare VPN page']) !!}
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="text" class="links_color form-control current_color" value="">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">CTA color</label>
                                                        <div class="col-md-7">
                                                            <input id="theme_buttons_color" name="theme[buttons_color]" type="text" class="colorpicker-default form-control" value="">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="text" class="buttons_color form-control current_color" value="">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Logo text color</label>
                                                        <div class="col-md-7">
                                                            <input id="theme_logo_color" name="theme[logo_color]" type="text" class="colorpicker-default form-control" value="">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="text" class="logo_color form-control current_color" value="">
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Footer Bg.</label>
                                                        <div class="col-md-7">
                                                            <input id="theme_footer" name="theme[footer]" type="text" class="colorpicker-default form-control" value="">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="text" class="footer form-control current_color" value="">
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
                                                        {!! Form::close() !!}
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
@include('templates.footer')