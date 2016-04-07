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

        BI.Admin.Media.listAll();

        $(document).on("click", ".git_pull", function () {
           // BI.Admin.Git.pull(this);
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
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button>
                        <span class="message"></span>
                    </div>
                    <div class="table-responsive">
                        <table id="git" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th> id </th>
                                <th> image </th>
                                <th> link </th>
                                <th> date uploaded </th>
                            </tr>
                            </thead>
                            <tbody id="media-data">


                            </tbody>
                        </table>
                    </div>
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