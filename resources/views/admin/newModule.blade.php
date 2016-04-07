@include('templates.header')
<script type="text/javascript">
    $(function(){

        $(document).on("click", "#btn-CreateModule", function () {
            var title = $('#title').val();
            if($.trim(title) == '') {
                return;
            }

            BI.Admin.createModule(title);
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
                    <form class="module-form" method="post">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button>
                            <span class="message"></span>
                        </div>
                        <h3>Add Module</h3>
                        <div class="form-group">
                            <label class="control-label visible-ie8 visible-ie9">Title</label>
                            <div class="input-icon">
                                <i class="fa fa-font"></i>
                                <input class="form-control placeholder-no-fix" type="text" placeholder="Title" name="title" id="title"/>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" id="btn-CreateModule" class="btn green">
                                Add
                            </button>
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
