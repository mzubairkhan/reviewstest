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

        BI.Admin.Pages.listAllPages();

        $(document).on("click", ".editUser", function () {
            alert('Not implemented currently');
        });

        $(document).on("click", ".modal_trigger", function () {
            var id = $(this).closest('tr').attr('id');
            BI.Common.showModal({type: "basic", title: "Confirm Delete", body: 0, button: "Yes delete !" });

            $('.confirm').click(function(){
                BI.Admin.Pages.removePage(id);
            })

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
                    <div class="portlet-body flip-scroll" id="div-pages">

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