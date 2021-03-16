<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 4/5                                                      |
// +----------------------------------------------------------------------+
// | This source file is a part of iScripts eSwap                     |
// +----------------------------------------------------------------------+
// | Authors: Programmer<simi@armia.com>        		              |
// +----------------------------------------------------------------------+
// | Copyrights Armia Systems, Inc and iScripts.com � 2005                |
// | All rights reserved                                                  |
// +----------------------------------------------------------------------+
// | This script may not be distributed, sold, given away for free to     |
// | third party, or used as a part of any internet services such as      |
// | webdesign etc.                                                       |
// +----------------------------------------------------------------------+
$toplinks=toplinks(TABLEPREFIX);
for($i=0;$i<mysqli_num_rows($toplinks);$i++) {
    $MenuEnableArray[]=mysqli_result($toplinks,$i,'vCategoryTitle');
}//end for loop

//checking array
if(is_array($MenuEnableArray)) {
    switch($PGTITLE) {
        case "sales":
            $checks='id="current"';
            break;

        case "swap":
            $checks2='id="current"';
            break;

        case "wish":
            $checks3='id="current"';
            break;

        case "saleapproval":
            $checks4='id="current"';
            break;

        case "swapapproval":
            $checks5='id="current"';
            break;

        case "featuredapproval":
            $checks6='id="current"';
            break;

        case "cms":
            $checks7 = 'id="current';
    }//end switch

    if(in_array('Sell',$MenuEnableArray)) {
        $SaleMenu='<li class="maintext2"><a href="'.$rootserver.'/admin/sales.php" '. $checks.'>Sales</a></li>';
        $SaleMenu2='<li class="maintext2"><a href="'.$rootserver.'/admin/saleapproval.php" '. $checks4.'>Approve Sale</a></li>';
        $SaleMenu3='<li class="maintext2"><a href="'.$rootserver.'/admin/featuredapproval.php" '. $checks6.'>Approve Item Addition</a></li>';
    }//end if

    if(in_array('Swap',$MenuEnableArray)) {
        $SwapMenu='<li class="maintext2"><a href="'.$rootserver.'/admin/swap.php" '. $checks2.'>Swap</a></li>';
        $SwapMenu2='<li class="maintext2"><a href="'.$rootserver.'/admin/swapapproval.php" '. $checks5.'>Approve Swap/Wish</a></li>';
    }//end if

    if(in_array('Wish',$MenuEnableArray)) {
        $WishMenu='<li class="maintext2"><a href="'.$rootserver.'/admin/wish.php" '. $checks3.'>Wish</a></li>';
        $SwapMenu2='<li class="maintext2"><a href="'.$rootserver.'/admin/swapapproval.php" '. $checks5.'>Approve Swap/Wish</a></li>';
    }//end if
}//end if

//escropayements
if(DisplayLookUp('Enable Escrow')=='Yes') {
    switch($PGTITLE) {
        case "pending_settlement":
            $escheck='id="current"';
            break;
    }//end switch

    $escrow1='<li class="maintext2"><a href="'.$rootserver.'/admin/usersettlements.php" '.$escheck.'>Pending Settlements</a></li>';
    $escrow2='<li class="maintext2"><a href="javascript:calculator(this);" class="jQescrow">Escrow Calculator</a></li>';
}//end if
?>
<script type="text/javascript" src="<?php echo SITE_URL;?>/js/jquery.js"></script>
<script language=javascript>
     var $jqr=jQuery.noConflict();
    function calculator(event)
    {
        // $jqr('#jQescrow').addClass("current");
         $jqr('.jQescrow').attr('id', 'current');
        window.open('../calculator.php','','width=300,height=300,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,copyhistory=no,resizable=no');
    }//end function
</script>
<table width="78%"  border="0" cellspacing="0" cellpadding="0" >
    <tr>
        <td align="left" valign="top" class="left_panel"><div id="menu7">
                <ul>
                    <!-- CSS Tabs -->
                    <li class="maintext2 menu_subheader"><a href="#" id="current">
                    <span class=" glyphicon glyphicon-cog"></span>
                    Site Settings</a></li>
                    <div id="submenu7">
                        <ul>
                            <li class="maintext2">
                            <a href="<?php echo $rootserver?>/admin/setconf.php" <?php if($PGTITLE=='settings') {
                                    echo 'id="current"';
                                }?>>Main</a>
                            </li>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/points.php" <?php if($PGTITLE=='points.php') {
                                    echo 'id="current"';
                                }?>>Points Manager</a>
                            </li>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/payment_settings.php" <?php if($PGTITLE=='payment_settings') {
                                    echo 'id="current"';
                                }?>>Payment</a>
                            </li>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/registration_settings.php" <?php if($PGTITLE=='registration_settings') {
                                    echo 'id="current"';
                                }?>>Registration</a>
                            </li>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/changepassword.php" <?php if($PGTITLE=='changepass') {
                                    echo 'id="current"';
                                }?>>Password</a>
                            </li>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/client_modules.php" <?php if($PGTITLE=='client_modules') {
                                echo 'id="current"';
                            }?>>Modules</a></li>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/meta_tags.php" <?php if($PGTITLE=='meta_tags') {
                                echo 'id="current"';
                                }?>>Meta Tags</a>
                            </li>
                                <?php
                                //... (DisplayLookUp('15') => 1 if free registration is enabled => 0 if free registration is disabled
                                //Here plan will only get enabled if free registration is disabled
                                if(DisplayLookUp('15')!='1' && DisplayLookUp('plan_system')=='yes'){
                                ?>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/plans.php" <?php if($PGTITLE=='plans') {
                                    echo 'id="current"';
                                }?>>Plans</a>
                            </li>
                                <?php
                                }//end if
                                ?>
                        </ul>
                    </div>
                    <li class="maintext2 menu_subheader"><a href="#" id="current"><span class="glyphicon glyphicon-folder-close"></span>Miscellaneous</a></li>
                    <div id="submenu7">
                        <ul>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/categories.php" <?php if($PGTITLE=='categories') {
                                echo 'id="current"';
                            }?>>Categories</a></li>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/languages.php" <?php if($PGTITLE=='languages') {
                                echo 'id="current"';
                                }?>>Languages</a>
                            </li>
                                <?php if(DisplayLookUp('BannerDisplay')=='Yes') {
                                ?>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/banners.php" <?php if($PGTITLE=='banners') {
                                        echo 'id="current"';
                                    }?>>Banners</a>
                            </li>
                                    <?php  }//end if?>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/sliders.php" <?php if($PGTITLE=='sliders') {
                                        echo 'id="current"';
                                    }?>>Sliders</a>
                            </li>
                                <?php echo $SaleMenu;?>
                                <?php echo $SwapMenu;?>
                                <?php echo $WishMenu;?>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/feedbacks.php" <?php if($PGTITLE=='feedback') {
                                    echo 'id="current"';
                                }?>>Feedback</a>
                            </li>
                                <?php echo $escrow1?>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/newsletter.php" <?php if($PGTITLE=='newsletter') {
                                    echo 'id="current"';
                                }?>>NewsLetter</a>
                            </li>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/survey.php" <?php if($PGTITLE=='survey') {
                                echo 'id="current"';
                            }?>>Referral Payments</a></li>
                            <?php echo $escrow2?>
                        </ul>
                    </div>
                    <li class="maintext2 menu_subheader"><a href="#" id="current">
                    <span class="glyphicon glyphicon-file"></span>Account Summary</a></li>
                    <div id="submenu7">
                        <ul>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/useraccountsummary.php" <?php if($PGTITLE=='account') {
                                    echo 'id="current"';
                                }?>>User Settled Account</a>
                            </li>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/useraccountsummary_other.php" <?php if($PGTITLE=='account2') {
                                echo 'id="current"';
                            }?>>User Sales Account</a></li>
                        </ul>
                    </div>
                    <li class="maintext2 menu_subheader"><a href="#" id="current">
                    <span class="glyphicon glyphicon-list-alt"></span>Content Management</a></li>
                    <div id="submenu7">
                        <ul>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/update_cms.php" <?php if($PGTITLE=='cms') {
                                echo 'id="current"';
                            }?>>CMS</a>
                            </li>

                                <!--<li class="maintext2"><a href="<?php echo $rootserver?>/admin/home.php" <?php if($PGTITLE=='home') {
                                        echo 'id="current"';
                                        }?>>Home</a></li>
                                        <li class="maintext2"><a href="<?php echo $rootserver?>/admin/contact.php" <?php if($PGTITLE=='contact') {
                                        echo 'id="current"';
                                        }?>>Contact Us</a></li>
                                        <li class="maintext2"><a href="<?php echo $rootserver?>/admin/about.php" <?php if($PGTITLE=='about') {
                                        echo 'id="current"';
                                        }?>>About Us</a></li>
                                        <li class="maintext2"><a href="<?php echo $rootserver?>/admin/privacy.php" <?php if($PGTITLE=='privacy') {
                                        echo 'id="current"';
                                        }?>>Privacy Policy</a></li>
                                        <li class="maintext2"><a href="<?php echo $rootserver?>/admin/terms.php" <?php if($PGTITLE=='terms') {
                                        echo 'id="current"';
                                        }?>>Terms</a></li> -->
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/faq.php" <?php if($PGTITLE=='faq') {
                                    echo 'id="current"';
                                }?>>FAQ</a>
                            </li>

                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/email_contents.php" <?php if($PGTITLE=='email_contents') {
                                echo 'id="current"';
                                }?>>Mail Contents</a>
                            </li>
                        </ul>
                    </div>
                    <li class="maintext2 menu_subheader"><a href="#" id="current">
                    <span class="glyphicon glyphicon-user"></span>User Management</a></li>
                    <div id="submenu7">
                        <ul>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/users.php" <?php if($PGTITLE=='users') {
                                echo 'id="current"';
                            }?>>Registered Users</a></li>
                            <?php
                                    //checking point enable in website
                                    if($EnablePoint=='1' || $EnablePoint=='2') {
                                ?>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/users_points.php" <?php if($PGTITLE=='users_points.php') {
                                    echo 'id="current"';
                                    }?>>User <?php echo POINT_NAME;?></a>
                            </li>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/users_point_orders.php" <?php if($PGTITLE=='users_point_orders.php') {
                                    echo 'id="current"';
                                }?>><?php echo POINT_NAME;?> Purchases</a>
                            </li>

                                    <?php
                                }//end if
                                ?>
                            <!--<li class="maintext2"><a href="<?php echo $rootserver?>/admin/users_monthlypayments.php" <?php if($PGTITLE=='users_monthlypayments.php') {
                                    echo 'id="current"';
                                }?>>Monthly Payments</a>
                            </li>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/monthlypayments_pending.php" <?php if($PGTITLE=='users_monthlypayments.php') {
                                    echo 'id="current"';
                                }?>>Monthly Payments Pending</a>
                            </li>-->
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/users_confirm_orders.php" <?php if($PGTITLE=='users_confirm_orders.php') {
                                        echo 'id="current"';
                                    }?>>Success Fee Orders</a>
                            </li>
                            <!--<li class="maintext2"><a href="<?php echo $rootserver?>/admin/manadatoryfields.php" <?php /*if($PGTITLE=='user_reg_fields') {
                                    echo 'id="current"';
                                }*/ ?>>Registration Form Fields</a>
                            </li>-->
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/usersactivation.php" <?php if($PGTITLE=='useractivation') {
                                    echo 'id="current"';
                                }?>>Users Awaiting Activation</a>
                            </li>
                        </ul>
                    </div>
                            <?php /*if($SaleMenu2!='' or $SaleMenu3!='' or $SwapMenu2!='') {
                                echo '<li class="maintext2 menu_subheader"><a href="#" id="current">
								<span class=" glyphicon glyphicon-ok"></span>Approvals</a></li>';
                            }*/
                            ?>
<!--                     <div id="submenu7">
    <ul>
        <?php echo $SaleMenu2;?>
        <?php echo $SwapMenu2;?>
        <?php echo $SaleMenu3;?>
    </ul>
</div> -->
                    <li class="maintext2 menu_subheader"><a href="#" id="current">
                    <span class="glyphicon glyphicon-file"></span>
                    Reports</a></li>
                    <div id="submenu7">
                        <ul>
                                <?php
                                //checking point enable in website
                                if(DisplayLookUp('EnablePoint')!='1') {
                                    ?>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/sale_report.php" <?php if($PGTITLE=='sale_report.php') {
                                        echo 'id="current"';
                                    }?>>Sales</a>
                            </li>
                                    <?php
                                }//end if
                                else {
                                    ?>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/success_report.php" <?php if($PGTITLE=='success_report.php') {
                                    echo 'id="current"';
                                }?>>Success Fee</a>
                            </li>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/point_report.php" <?php if($PGTITLE=='point_report.php') {
                                        echo 'id="current"';
                                    }?>><?php echo POINT_NAME;?> Orders</a>
                            </li>
                                    <?php
                                }//end if
                                ?>
                        </ul>
                    </div>
                    <li class="maintext2 menu_subheader"><a href="#" id="current">
                    <span class="glyphicon glyphicon-question-sign"></span>
                    Help</a></li>
                    <div id="submenu7">
                        <ul>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/help_category.php" <?php if($PGTITLE=='help_category') {
                                    echo 'id="current"';
                                }?>>Help Category</a>
                            </li>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/help_admin.php" <?php if($PGTITLE=='help_admin') {
                                    echo 'id="current"';
                                }?>>Admin Help</a>
                            </li>
                            <li class="maintext2"><a href="<?php echo $rootserver?>/admin/help.php" <?php if($PGTITLE=='help') {
                                    echo 'id="current"';
                                }?>>User Help</a>
                            </li>
                        </ul>
                    </div>
                </ul>
            </div>
        </td>
    </tr>
</table>