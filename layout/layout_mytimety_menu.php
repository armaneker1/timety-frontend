<?php
$showMenu = true;
$hideBar = false;
//if (!empty($user) && !empty($user->id) && !empty($user->userName) && $user->status > 2) {
if (isset($page_id) && ($page_id == "profile" || $page_id == "editevent" || $page_id == "user" || $page_id == "createaccount" || $page_id == "signin" || $page_id == "registerPI")) {
    $showMenu = false;
}
//}
$recommended_class = true;
$all_class = false;
if (empty($user)) {
    $recommended_class = false;
    $all_class = true;
}
if (isset($page_id) && $page_id == "user") {
    $hideBar = true;
}
?>

<tr>
    <td colspan="3">
        <table class="mytimety_category_table" id="mytimety_category_table">
            <tr>
                <?php if ($showMenu) { ?>
                    <td width="375" valign="middle" id="populer_top_menus_my">
                        <div class="mytimety_menu_class">
                            <div class="mytimety_category_item_recommended_div">
                                <span id="mytimety_category_item_recommended" channelid="1" class="mytimety_category_item <?php if($recommended_class) {echo 'mytimety_category_item_selected';} ?>"><?= LanguageUtils::getText("LANG_PAGE_MY_TIMETY_MENU_RECOMMENDED_EVENTS") ?></span>
                            </div> 
                            <span class="hd_line">|</span> 
                            <div class="mytimety_category_item_everything_div">
                                <span id="mytimety_category_item_everything" class="mytimety_category_item <?php if($all_class) {echo 'mytimety_category_item_selected';} ?>"><?= LanguageUtils::getText("LANG_PAGE_MY_TIMETY_MENU_ALL_EVENTS") ?></span> 
                            </div>
                            <span class="hd_line">|</span> 
                            <div id="mytimety_category_item_categories_btn" class="mytimety_category_item_categories_btn">
                                <span id="mytimety_category_item_categories" class="mytimety_category_item"><?= LanguageUtils::getText("LANG_PAGE_MY_TIMETY_MENU_CATEGORIES") ?></span> 
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
                        </div>
                    </td>
                <?php } ?>
                <td align="left" valign="middle" class="u_line" width="100%" style='<?php
                if ($hideBar) {
                    echo 'display:none;';
                }
                ?>'>
                        <?php if ($showMenu) { ?>
                        <input id="populer_top_menus_my_ico" type="button" class="gn_btn" />
                    <?php } ?>
                </td>
            </tr>
        </table>
    </td>
</tr>