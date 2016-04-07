<script type="text/javascript">
    var options = $.parseJSON('<?php echo json_encode($option)?>');

    var page = options['page'];
    var subPage = options['sub-page'];


    $(function(){
        $('.sBar-'+ page).addClass('active');
        $('.sBar-'+ page + ' a').append('<span class="selected"></span>');
        $('.sBar-'+ page + ' .arrow').addClass('open');
        $('.sBar-'+ page + ' .' + subPage).addClass('active');
    });
</script>
<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <!-- add "navbar-no-scroll" class to disable the scrolling of the sidebar menu -->
        <!-- BEGIN SIDEBAR MENU -->
        <ul class="page-sidebar-menu" data-auto-scroll="true" data-slide-speed="200">
            <li class="sidebar-toggler-wrapper">
                <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                <div class="sidebar-toggler hidden-phone">
                </div>
                <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
            </li>
            <li class="sidebar-search-wrapper">
                <!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
                <form class="sidebar-search" action="" method="POST">
                    <div class="form-container">
                        <div class="input-box">
                            <a href="javascript:;" class="remove">
                            </a>
                            <input type="text" placeholder="Search..."/>
                            <input type="button" class="submit" value=" "/>
                        </div>
                    </div>
                </form>
                <!-- END RESPONSIVE QUICK SEARCH FORM -->
            </li>
            <li class="sBar-dashboard">
                <a href="{{ url('admin/index') }}">
                    <i class="fa fa-home"></i>
						<span class="title">
                            Admin Dashboard
						</span>
                </a>
            </li>

            <li class="sBar-users">
                <a href="javascript:;">
                    <i class="fa fa-users"></i>
						<span class="title">
                            Users
                        </span>
						<span class="arrow">
						</span>
                </a>
                <ul class="sub-menu">
                    <li class="view">
                        <a href="{{ url('admin/users') }}">
                            <i class="fa fa-eye"></i>
                            View Users
                        </a>
                    </li>
                    <li class="new">
                        <a href="{{ url('admin/newUser') }}">
                            <i class="fa fa-plus"></i>
                            Add a User
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sBar-pages">
                <a href="javascript:;">
                    <i class="fa fa-bars"></i>
						<span class="title">
                            Pages
                        </span>
						<span class="arrow">
						</span>
                </a>
                <ul class="sub-menu">
                    <li class="view">
                        <a href="{{ url('admin/pages') }}">
                            <i class="fa fa-eye"></i>
                            View Pages
                        </a>
                    </li>
                    <li class="new">
                        <a href="{{ url('admin/newPage') }}">
                            <i class="fa fa-plus"></i>
                            Add a Pages
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sBar-providers">
                <a href="javascript:;">
                    <i class="fa fa-cloud"></i>
						<span class="title">
                            Providers
                        </span>
						<span class="arrow">
						</span>
                </a>

                <ul class="sub-menu">
                    <li class="view">
                        <a href="{{ url('admin/providers') }}">
                            <i class="fa fa-eye"></i>
                            View Providers
                        </a>
                    </li>
                    <li class="new">
                        <a href="{{ url('admin/newProvider') }}">
                            <i class="fa fa-plus"></i>
                            Add a Provider
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sBar-customfields">
                <a href="javascript:;">
                    <i class="fa fa-tasks"></i>
						<span class="title">
                            Custom Fields
                        </span>
						<span class="arrow">
						</span>
                </a>
                <ul class="sub-menu">
                    <li class="view">
                        <a href="{{ url('admin/customfields') }}">
                            <i class="fa fa-eye"></i>
                            View custom fields
                        </a>
                    </li>
                    <li class="new">
                        <a href="{{ url('admin/newCustomfield') }}">
                            <i class="fa fa-plus"></i>
                            Add a custom fields
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sBar-websites">
                <a href="javascript:;">
                    <i class="fa fa-globe"></i>
						<span class="title">
                            Websites
                        </span>
						<span class="arrow">
						</span>
                </a>
                <ul class="sub-menu">
                    <li class="view">
                        <a href="{{ url('admin/websites') }}">
                            <i class="fa fa-eye"></i>
                            View websites
                        </a>
                    </li>
                    <li class="new">
                        <a href="{{ url('admin/newWebsite') }}">
                            <i class="fa fa-plus"></i>
                            Add a website
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sBar-git">
                <a href="javascript:;">
                    <i class="fa fa-github"></i>
						<span class="title">
                            Git Repos
                        </span>
						<span class="arrow">
						</span>
                </a>
                <ul class="sub-menu">
                    <li class="view">
                        <a href="{{ url('admin/git') }}">
                            <i class="fa fa-eye"></i>
                            Manage git repos
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sBar-media">
                <a href="javascript:;">
                    <i class="fa fa-picture-o"></i>
						<span class="title">
                            Media
                        </span>
						<span class="arrow">
						</span>
                </a>
                <ul class="sub-menu">
                    <li class="new">
                        <a href="{{ url('admin/newMedia') }}">
                            <i class="fa fa-plus"></i>
                            Add new
                        </a>
                    </li>
                    <li class="view">
                        <a href="{{ url('admin/media') }}">
                            <i class="fa fa-eye"></i>
                            View media
                        </a>
                    </li>
                </ul>
            </li>



            <li class="sBar-media">
                            <a href="javascript:;">
                                <i class="fa fa-rocket"></i>
            						<span class="title">
                                        Roles
                                    </span>
            						<span class="arrow">
            						</span>
                            </a>
                            <ul class="sub-menu">
                                <li class="new">
                                    <a href="{{ url('admin/newRole') }}">
                                        <i class="fa fa-plus"></i>
                                        Add new
                                    </a>
                                </li>
                                <li class="view">
                                    <a href="{{ url('admin/roles') }}">
                                        <i class="fa fa-eye"></i>
                                        View roles
                                    </a>
                                </li>
                            </ul>
             </li>


            <li class="sBar-media">
                            <a href="javascript:;">
                                <i class="fa fa-certificate"></i>
            						<span class="title">
                                        Modules
                                    </span>
            						<span class="arrow">
            						</span>
                            </a>
                            <ul class="sub-menu">
                                <li class="new">
                                    <a href="{{ url('admin/newModule') }}">
                                        <i class="fa fa-plus"></i>
                                        Add new
                                    </a>
                                </li>
                                <li class="view">
                                    <a href="{{ url('admin/modules') }}">
                                        <i class="fa fa-eye"></i>
                                        View modules
                                    </a>
                                </li>
                            </ul>
             </li>


            <li class="sBar-media">
                            <a href="javascript:;">
                                <i class="fa fa-lock"></i>
            						<span class="title">
                                       Permissions
                                    </span>
            						<span class="arrow">
            						</span>
                            </a>
                            <ul class="sub-menu">
                                <li class="new">
                                    <a href="{{ url('admin/newPermission') }}">
                                        <i class="fa fa-plus"></i>
                                        Add new
                                    </a>
                                </li>
                                <li class="view">
                                    <a href="{{ url('admin/permissions') }}">
                                        <i class="fa fa-eye"></i>
                                        View permissions
                                    </a>
                                </li>
                            </ul>
             </li>
        </ul>
        <!-- END SIDEBAR MENU -->
    </div>
</div>