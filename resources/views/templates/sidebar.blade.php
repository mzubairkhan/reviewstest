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
    majid
<div class="page-sidebar navbar-collapse collapse">
<!-- add "navbar-no-scroll" class to disable the scrolling of the sidebar menu -->
<!-- BEGIN SIDEBAR MENU -->
<ul class="page-sidebar-menu" data-auto-scroll="true" data-slide-speed="200">
<li class="sidebar-toggler-wrapper margin-bottom-20">
    <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
    <div class="sidebar-toggler hidden-phone">
    </div>
    <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
</li>
<!--
<li class="sidebar-search-wrapper">
    <!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
<!--    <form class="sidebar-search" action="" method="POST">
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
<!--</li> -->
    <?php
        $session_data_side_bar = $this->session->all_userdata();

        function isValidLinkForUser($link, $session_data)
        {
            $logged_in = $session_data['logged_in'];
            $permissions = $logged_in['permissions'];
            $valid = false;
            if(isset($permissions)) {
                foreach($permissions as $perm) {
                    if($perm['title'] == $link) {
                        $valid = true;
                        break;
                    }
                }
            }
            return $valid;

        }
    ?>

<li class="sBar-dashboard start">
    <a href="<?php echo base_url() ;?>">
        <!--<i class="fa fa-home"></i>-->
            <span class="title">
                Dashboard
            </span>
            <span>
            </span>
    </a>
</li>
<?php if (isValidLinkForUser('retention-renewal_campaign',$session_data_side_bar) || isValidLinkForUser('retention-expired_customers',$session_data_side_bar) || isValidLinkForUser('retention-list_reporting',$session_data_side_bar)) { ?>
    <li class="sBar-retention">
        <a href="javascript:;">
            <!--<i class="fa fa-shopping-cart"></i>-->
                <span class="title">
                    Retention
                </span>
                <span class="arrow ">
                </span>
        </a>
        <ul class="sub-menu">
            <?php if (isValidLinkForUser('retention-renewal_campaign',$session_data_side_bar)) { ?>
                <li class="renewal_campaign">
                    <a href="<?php echo base_url() ;?>retention/renewal_campaign">
                        <!--<i class="fa fa-bullhorn"></i>-->
                        Campaigns
                    </a>
                </li>
            <?php } ?>

            <?php if (isValidLinkForUser('retention-expired_customers',$session_data_side_bar)) { ?>
                <li class="expired_customers">
                    <a href="<?php echo base_url() ;?>retention/expired_customers">
                        <!--<i class="fa fa-shopping-cart"></i>-->
                        Expired customers
                    </a>
                </li>
            <?php } ?>

            <?php if (isValidLinkForUser('retention-list_reporting',$session_data_side_bar)) { ?>
                <li class="list_reporting">
                    <a href="<?php echo base_url() ;?>retention/list_reporting">
                        <!--<i class="fa fa-shopping-cart"></i>-->
                        List reporting
                    </a>
                </li>
            <?php } ?>

        </ul>
    </li>
<?php } ?>
<?php if (isValidLinkForUser('CLT-index',$session_data_side_bar) || isValidLinkForUser('CLT-customerStats',$session_data_side_bar)) { ?>
    <li class="sBar-clt">
        <a href="javascript:;">
            <!--<i class="fa fa-gift"></i>-->
            <span class="title">
                Customer Life Time
            </span>
            <span class="arrow">
            </span>
        </a>
        <ul class="sub-menu">
            <?php if (isValidLinkForUser('CLT-index',$session_data_side_bar)) { ?>
                <li class="tooltips churn" data-container="body" data-placement="right" data-html="true" data-original-title="Churn rate calculation">
                    <a href="<?php echo base_url() ;?>CLT/index">
                        <span class="title">
                            Calculate Churn rate
                        </span>
                    </a>
                </li>
            <?php } ?>
            <?php if (isValidLinkForUser('CLT-customerStats',$session_data_side_bar)) { ?>
                <li class="tooltips churn" data-container="body" data-placement="right" data-html="true" data-original-title="Customer stats">
                    <a href="<?php echo base_url() ;?>CLT/customerStats">
                        <span class="title">
                            Cusomer Stats
                        </span>
                    </a>
                </li>
            <?php } ?>

        </ul>
    </li>
<?php } ?>
<?php if (isValidLinkForUser('network-session',$session_data_side_bar)  || isValidLinkForUser('network-home',$session_data_side_bar) || isValidLinkForUser('network-server',$session_data_side_bar) || isValidLinkForUser('network-source',$session_data_side_bar) || isValidLinkForUser('network-home',$session_data_side_bar) || isValidLinkForUser('network-multiLoginWhiteListedUsers',$session_data_side_bar) || isValidLinkForUser('network-compare',$session_data_side_bar) || isValidLinkForUser('network-maps',$session_data_side_bar) || isValidLinkForUser('network-grid',$session_data_side_bar) || isValidLinkForUser('network-monitorAccounting',$session_data_side_bar)  || isValidLinkForUser('network-inventory',$session_data_side_bar) || isValidLinkForUser('network-shortSession',$session_data_side_bar) || isValidLinkForUser('network-monitorServerLogs',$session_data_side_bar) || isValidLinkForUser('network-blackListedUsers',$session_data_side_bar) || isValidLinkForUser('network-systemBenchmarking',$session_data_side_bar)) { ?>
    <li class="sBar-network">
        <a href="javascript:;">
            <!--<i class="fa fa-sitemap"></i>-->
                <span class="title">
                    VPN Network
                </span>
                <span class="arrow ">
                </span>
        </a>
        <ul class="sub-menu">
            <?php if (isValidLinkForUser('network-home',$session_data_side_bar)) { ?>

            <li class="home">
                <a href="<?php echo base_url() ;?>network/home">
                    <!--<i class="fa fa-briefcase"></i>-->
                    Home
                </a>
            </li>
            <?php } ?>

            <?php if (isValidLinkForUser('network-session',$session_data_side_bar)) { ?>

                <li class="session">
                    <a href="<?php echo base_url() ;?>network/session">
                        <!--<i class="fa fa-briefcase"></i>-->
                        Session
                    </a>
                </li>
            <?php } ?>

            <?php if (isValidLinkForUser('network-server',$session_data_side_bar)) { ?>

                <li class="n_server">
                    <a href="<?php echo base_url() ;?>network/server">
                        <!--<i class="fa fa-briefcase"></i>-->
                        Server
                    </a>
                </li>
            <?php } ?>

            <?php if (isValidLinkForUser('network-source',$session_data_side_bar)) { ?>

                <li class="n_source">
                    <a href="<?php echo base_url() ;?>network/source">
                        <!--<i class="fa fa-briefcase"></i>-->
                        Source
                    </a>
                </li>
            <?php } ?>

            <?php if (isValidLinkForUser('network-country',$session_data_side_bar)) { ?>

                <li class="n_country">
                    <a href="<?php echo base_url() ;?>network/country">
                        <!--<i class="fa fa-briefcase"></i>-->
                        Destination Country
                    </a>
                </li>
            <?php } ?>

            <?php if (isValidLinkForUser('network-grid',$session_data_side_bar)) { ?>

                <li class="n_grid">
                    <a href="<?php echo base_url() ;?>network/grid">
                        <!--<i class="fa fa-briefcase"></i>-->
                        Grid
                    </a>
                </li>
            <?php } ?>

            <?php if (isValidLinkForUser('network-compare',$session_data_side_bar)) { ?>

                <li class="n_compare">
                    <a href="<?php echo base_url() ;?>network/compare">
                        <!--<i class="fa fa-briefcase"></i>-->
                        Compare
                    </a>
                </li>
            <?php } ?>


            <?php if (isValidLinkForUser('network-maps',$session_data_side_bar)) { ?>

                <li class="maps">
                    <a href="<?php echo base_url() ;?>network/maps">
                        <!--<i class="fa fa-briefcase"></i>-->
                        Maps
                    </a>
                </li>
            <?php } ?>

            <?php if (isValidLinkForUser('network-multiLoginWhiteListedUsers',$session_data_side_bar)) { ?>

                <li class="multiLoginWhiteListedUsers">
                    <a href="<?php echo base_url() ;?>network/multiLoginWhiteListedUsers" title="Multi Login WhiteListed Users">
                        <!--<i class="fa fa-briefcase"></i>-->
                        White list
                    </a>
                </li>
            <?php } ?>

            <?php if (isValidLinkARRForUser('network-blackListedUsers',$session_data_side_bar)) { ?>

                <li class="blackListedUsers">
                    <a href="<?php echo base_url() ;?>network/blackListedUsers" title="Black listed Users">
                        <!--<i class="fa fa-briefcase"></i>-->
                        Black listed Users
                    </a>
                </li>
            <?php } ?>


            <?php if (isValidLinkForUser('network-shortSession',$session_data_side_bar)) { ?>

                <li class="multiLoginWhiteListedUsers">
                    <a href="<?php echo base_url() ;?>network/shortSession" title="Short Sessions">
                        <!--<i class="fa fa-briefcase"></i>-->
                        Short Sessions
                    </a>
                </li>
            <?php } ?>

            <?php if (isValidLinkForUser('network-monitorAccounting',$session_data_side_bar) || isValidLinkForUser('network-monitorServerLogs',$session_data_side_bar)) { ?>
                <?php
                $class_sub_menu = '';
                $style_sub_menu_item = '';
                $style_ul = '';
                if($this->options['sub-page'] == 'monitorAccounting' || $this->options['sub-page'] == 'monitorServerLogs') {
                    $class_sub_menu = ' open';
                    $style_ul = 'style="display:block;"';
                    $style_sub_menu_item = 'style="background:none !important;"';
                }
                ?>

                <li class="sBar-monitor <?php echo $class_sub_menu; ?>">
                    <a href="javascript:;" <?php echo $style_sub_menu_item; ?>>
                        <!--<i class="fa fa-gift"></i>-->
                    <span class="title">
                        Monitor
                    </span>
                    <span class="arrow">
                    </span>
                    </a>
                    <ul class="sub-menu" <?php echo $style_ul; ?>>
                        <?php if (isValidLinkForUser('network-monitorAccounting',$session_data_side_bar)) { ?>
                            <li class="tooltips monitorAccounting" data-container="body" data-placement="right" data-html="true" data-original-title="Monitor Network Accounting">
                                <a href="<?php echo base_url() ;?>network/monitorAccounting">
                                <span class="title">
                                    Accounting Failures
                                </span>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if (isValidLinkForUser('network-monitorServerLogs',$session_data_side_bar)) { ?>
                            <li class="tooltips monitorServerLogs" data-container="body" data-placement="right" data-html="true" data-original-title="Speed Test Errors">
                                <a href="<?php echo base_url() ;?>network/monitorServerLogs">
                                <span class="title">
                                    Speed Test Errors
                                </span>
                                </a>
                            </li>
                        <?php } ?>

                    </ul>
                </li>
            <?php } ?>


            <?php if (isValidLinkForUser('network-inventory',$session_data_side_bar)) { ?>

                <li class="inventory">
                    <a href="<?php echo base_url() ;?>network/inventory" title="Inventory">
                        <!--<i class="fa fa-briefcase"></i>-->
                        Inventory
                    </a>
                </li>
            <?php } ?>
            <?php if (isValidLinkForUser('network-systemBenchmarking',$session_data_side_bar)) { ?>

                <li class="systemBenchmarking">
                    <a href="<?php echo base_url() ;?>network/systemBenchmarking" title="System Benchmarking">
                        <!--<i class="fa fa-briefcase"></i>-->
                        System Benchmarking
                    </a>
                </li>
            <?php } ?>

        </ul>
    </li>
<?php } ?>
<?php if (isValidLinkForUser('reseller-usage',$session_data_side_bar) ) { ?>
    <li class="sBar-reseller">
        <a href="javascript:;">
            <!--<i class="fa fa-gift"></i>-->
            <span class="title">
                Reseller
            </span>
            <span class="arrow">
            </span>
        </a>
        <ul class="sub-menu">
            <?php if (isValidLinkForUser('reseller-usage',$session_data_side_bar)) { ?>
                <li class="tooltips usage" data-container="body" data-placement="right" data-html="true" data-original-title="Usage">
                    <a href="<?php echo base_url() ;?>reseller/usage">
                        <span class="title">
                            Usage
                        </span>
                    </a>
                </li>
            <?php } ?>

        </ul>
    </li>
<?php } ?>
<?php if (isValidLinkForUser('VpnTesting-regions',$session_data_side_bar) || isValidLinkForUser('VpnTesting-vms',$session_data_side_bar) ) { ?>
<li class="sBar-VpnTesting">
    <a href="javascript:;">
        <!--<i class="fa fa-gift"></i>-->
            <span class="title">
                 VPN Testing
            </span>
            <span class="arrow">
            </span>
    </a>
    <ul class="sub-menu">
        <?php if (isValidLinkForUser('VpnTesting-regions',$session_data_side_bar)) { ?>
            <li class="tooltips regions" data-container="body" data-placement="right" data-html="true" data-original-title="Regions">
                <a href="<?php echo base_url() ;?>VpnTesting/regions">
                        <span class="title">
                            Regions Mgmt.
                        </span>
                </a>
            </li>
        <?php } ?>
        <?php if (isValidLinkForUser('VpnTesting-vms',$session_data_side_bar)) { ?>
            <li class="tooltips vms" data-container="body" data-placement="right" data-html="true" data-original-title="VMs">
                <a href="<?php echo base_url() ;?>VpnTesting/vms">
                        <span class="title">
                            VMs Mgmt.
                        </span>
                </a>
            </li>
        <?php } ?>
    </ul>
</li>
<?php } ?>
<?php if (isValidLinkForUser('smartdns-index',$session_data_side_bar) || isValidLinkForUser('smartdns-networkSpeed',$session_data_side_bar) || isValidLinkForUser('smartdns-whiteList',$session_data_side_bar)) { ?>
    <li class="sBar-smartdns">
        <a href="javascript:;">
            <!--<i class="fa fa-gift"></i>-->
            <span class="title">
                 SmartDNS Network
            </span>
            <span class="arrow">
            </span>
        </a>
        <ul class="sub-menu">
            <?php if (isValidLinkForUser('smartdns-index',$session_data_side_bar)) { ?>
                <li class="tooltips index" data-container="body" data-placement="right" data-html="true" data-original-title="Home">
                    <a href="<?php echo base_url() ;?>smartdns/index">
                        <span class="title">
                            Home
                        </span>
                    </a>
                </li>
            <?php } ?>
            <?php if (isValidLinkForUser('smartdns-networkSpeed',$session_data_side_bar)) { ?>
                <li class="tooltips index" data-container="body" data-placement="right" data-html="true" data-original-title="Home">
                    <a href="<?php echo base_url() ;?>smartdns/networkSpeed">
                        <span class="title">
                            Network Speed
                        </span>
                    </a>
                </li>
            <?php } ?>

            <?php if (isValidLinkForUser('smartdns-whiteList',$session_data_side_bar)) { ?>
                <li class="tooltips whiteList" data-container="body" data-placement="right" data-html="true" data-original-title="White List">
                    <a href="<?php echo base_url() ;?>smartdns/whiteList">
                        <span class="title">
                            White List
                        </span>
                    </a>
                </li>
            <?php } ?>


        </ul>
    </li>
<?php } ?>

<?php if (isValidLinkForUser('vpnnetwork-ip',$session_data_side_bar) || isValidLinkForUser('vpnnetwork-newIP',$session_data_side_bar) ) { ?>

    <li class="sBar-ip">
        <a href="javascript:;">
           <!-- <i class="fa fa-sitemap"></i>-->
            <span class="title">
                ACL ( RDS and BI )
            </span>
            <span class="arrow ">
            </span>
        </a>
        <ul class="sub-menu">
            <li class="view">
                <a href="<?php echo base_url() ;?>vpnnetwork/ip">
                    <!--<i class="fa fa-bullhorn"></i>-->
                    View IPs
                </a>
            </li>
            <li class="new">
                <a href="<?php echo base_url() ;?>vpnnetwork/newIP">
                    <!--<i class="fa fa-shopping-cart"></i>-->
                    Add an IP
                </a>
            </li>

        </ul>
    </li>
<?php } ?>

<?php if (isValidLinkForUser('affiliate-reports',$session_data_side_bar) ) { ?>
    <li class="sBar-affiliate">
        <a href="javascript:;">
            <!--<i class="fa fa-gift"></i>-->
            <span class="title">
                 Affiliate
            </span>
            <span class="arrow">
            </span>
        </a>
        <ul class="sub-menu">
            <?php if (isValidLinkForUser('affiliate-reports',$session_data_side_bar)) { ?>
                <li class="tooltips reports" data-container="body" data-placement="right" data-html="true" data-original-title="Reports">
                    <a href="<?php echo base_url() ;?>affiliate/reports">
                        <span class="title">
                            Reports
                        </span>
                    </a>
                </li>
            <?php } ?>



        </ul>
    </li>
<?php } ?>

</ul>
<!-- END SIDEBAR MENU -->
</div>
</div>