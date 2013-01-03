<?php
$user = null;

if (isset($_SESSION['id'])) {
    $user = new User();
    $user = UserUtils::getUserById($_SESSION['id']);
} else {
    //check cookie
    $rmm = false;
    if (isset($_COOKIE[COOKIE_KEY_RM]))
        $rmm = $_COOKIE[COOKIE_KEY_RM];
    if ($rmm && isset($_COOKIE[COOKIE_KEY_UN]) && isset($_COOKIE[COOKIE_KEY_PSS])) {
        $uname = base64_decode($_COOKIE[COOKIE_KEY_UN]);
        $upass = base64_decode($_COOKIE[COOKIE_KEY_PSS]);
        if (!empty($uname) && !empty($upass)) {
            $user = UserUtils::login($uname, $upass);
            if (!empty($user))
                $_SESSION['id'] = $user->id;
        }
    }
}
?>
<div class="u_bg"></div>
<div id="top_blm">
    <div id="top_blm_sol">
        <div class="logo"><a href="<?= HOSTNAME ?>"><img src="<?= HOSTNAME ?>images/timete.png" width="82" height="36" border="0" /></a></div>
        <div class="t_bs">
            <input type="button" name="" value="" id="add_event_button" class="add_event_btn" id="main_dropable" onclick="return false;"/>
            <input type="button" name="" value="" id="search_event_button" class="search_btn" onclick="return false;"/>
            <!--
                <div class="add_event">
                <input name="" type="text" class="add_event_input" value="enter event here" />
                <button type="button" name="" value="" class="kbtn icon_bg"></button>
                <button type="button" name="" value="" class="tbtn icon_bg"></button>
                <button type="button" name="" value="" class="cbtn icon_bg"></button>

                </div>
                <button type="button" name="" value="" class="addbtn">Add</button>
            -->

            <input type="text" id="hiddenSearch" class="user_inpt invite_friends icon_bg" style="opacity: 0"/>
<?php
if (!empty($user) && !empty($user->id) && !empty($user->userName) && $user->status > 2) {
    ?>
                <script type="text/javascript">
                    jQuery("#add_event_button").click(openCreatePopup);
                </script>
            <?php } else { ?>
                <script type="text/javascript">
                    function  to_home() {
                        window.location="<?= PAGE_LOGIN ?>";
                    }
                    jQuery("#add_event_button").click(to_home);
                </script>
<?php } ?>
<?php
if ((!empty($user->id) && !empty($user->userName) && $user->status > 2) || empty($user)) {
    ?>
                <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/searchbar.js"></script>
            <?php } ?>
        </div>
    </div>
    <div id="top_blm_sag">
            <?php
            if (!empty($user) && !empty($user->id) && !empty($user->userName)) {
                if ($user->status > 2) {
                    ?>

                <script>
                    
                    function changeChannel(item){
                        jQuery('.top_menu_ul_li_a_selected').addClass('top_menu_ul_li_a');
                        jQuery('.top_menu_ul_li_a_selected').removeClass('top_menu_ul_li_a_selected');
                        jQuery(item).removeClass('top_menu_ul_li_a');
                        jQuery(item).addClass('top_menu_ul_li_a_selected');
                        wookmarkFiller(document.optionsWookmark,true);
                    }    
                </script>
                <div class="top_menu">
                    <ul>
                        <li class="t_m_line"><a href="#" channelId="2" onclick="changeChannel(this)" class="top_menu_ul_li_a">My Timete</a><img width="150" height="150" src="<?= HOSTNAME ?>images/drop.png" class="main_dropable_"></img></li>
                        <li class="t_m_line"><a href="#" channelId="3" onclick="changeChannel(this)" class="top_menu_ul_li_a">Following</a></li>
                        <li class="t_m_line"><a href="#" channelId="1" onclick="changeChannel(this)" class="top_menu_ul_li_a_selected">Populer</a></li>
                        <li><a href="<?= PAGE_LOGOUT ?>" class="top_menu_ul_li_a">Logout</a></li>
                    </ul>
                </div>
                <div class="avatar"> <a href="#"><img src="<?php echo $user->getUserPic(); ?>" width="32" height="32" border="0" /></a>
        <?php if ($user->getUserNotificationCount()) { ?>
                        <div class="avtr_box"><?= $user->getUserNotificationCount() ?></div>
        <?php } ?>
                </div>
        <?php } else {
        ?>
                <div class="top_menu">
                    <ul>
                        <li><a href="<?= PAGE_LOGOUT ?>" class="top_menu_ul_li_a">Logout</a></li>
                    </ul>
                </div>
                <div class="avatar"> <a href="#"><img src="<?php echo $user->getUserPic(); ?>" width="32" height="32" border="0" /></a>
                <?php if ($user->getUserNotificationCount()) { ?>
                        <div class="avtr_box"><?= $user->getUserNotificationCount() ?></div>
        <?php } ?>
                </div>

    <?php
    }
} else {

    $signin_class = "";
    $create_class = "";

    if (!empty($sign_page_type) && $sign_page_type == "createaccount") {
        $create_class = "cr_acc_hover";
    }

    if (!empty($sign_page_type) && $sign_page_type == "signin") {
        $signin_class = "sgn_in_hover";
    }
    ?>
            <div class="t_account"><a href="<?= PAGE_SIGNUP ?>" class="cr_acc <?= $create_class ?>">create account</a><a href="<?= PAGE_LOGIN ?>" class="sgn_in <?= $signin_class ?>">sign-in </a></div>

        <?php } ?>
    </div>
</div>
