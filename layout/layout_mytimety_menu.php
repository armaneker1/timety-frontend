<?php
$showMenu = true;
if (!empty($user) && !empty($user->id) && !empty($user->userName) && $user->status > 2) {
    if (isset($page_id) && ($page_id == "profile" || $page_id == "editevent" || $page_id == "user" || $page_id == "createaccount" || $page_id == "signin" || $page_id == "registerPI")) {
        $showMenu = false;
    }
}
?>

<tr>
    <td colspan="3">
        <table class="mytimety_category_table" id="mytimety_category_table">
            <tr>
                <?php if ($showMenu) { ?>
                    <td width="375" valign="middle">
                        <div class="mytimety_category_item_recommended_div">
                            <span id="mytimety_category_item_recommended" channelid="1" class="mytimety_category_item mytimety_category_item_selected">Recommended Events</span>
                        </div> 
                        <span class="hd_line">|</span> 
                        <div class="mytimety_category_item_everything_div">
                            <span id="mytimety_category_item_everything" class="mytimety_category_item">All Events</span> 
                        </div>
                        <span class="hd_line">|</span> 
                        <div id="mytimety_category_item_categories_btn" class="mytimety_category_item_categories_btn">
                            <span id="mytimety_category_item_categories" class="mytimety_category_item">Categories</span> 
                            <div id="populer_top_menu_my" class="mytimety_category_item_categories_container" style="display: none;">
                                <div  class="my_timete_popup" >
                                    <div class="kck_detay_ok"></div>
                                    <ul id="populer_top_menu_ul_my">
                                        <?php
                                        $lang = LANG_EN_US;
                                        if (!empty($user)) {
                                            $lang = $user->language;
                                        }
                                        $cats = MenuUtils::getCategories($lang);
                                        foreach ($cats as $cat) {
                                            ?>
                                            <li cat_id="<?= $cat->getId() ?>" id="my_cat_id<?= $cat->getId() ?>" style="cursor:pointer"  slc="false">
                                                <button type="button" class="ekle icon_bg"></button>
                                                <span><?= $cat->getName() ?></span>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </td>
                <?php } ?>
                <td align="left" valign="middle" class="u_line" width="100%">
                    <?php if ($showMenu) { ?>
                        <input type="button" class="gn_btn" />
                    <?php } ?>
                </td>
            </tr>
        </table>
    </td>
</tr>