<?php
if (!isset($userIdS) || empty($userIdS)) {
    $userIdS = "null";
}
if (!isset($puserIdS) || empty($puserIdS)) {
    $puserIdS = "null";
}
?>
<script>
    jQuery(document).ready(function(){
        jQuery("#people_search_button").click(function(){
            var userId = <?= $userIdS ?>;
            var reqUserId = <?= $puserIdS ?>;
            if(userId!=null && userId>0)
            {    
                openFriendsPopup(userId,reqUserId, 3);
            }
        });
        jQuery("#people_search_input").keyup(function(event){
            if(event.keyCode==13)
            {
                var userId = <?= $userIdS ?>;
                var reqUserId = <?= $puserIdS ?>;
                if(userId!=null && userId>0)
                {    
                    openFriendsPopup(userId,reqUserId, 3);
                }
            }
        });
    });
</script>

<div class="follow_ekr" id="profile_friends" style="display: none;position: relative;">
    <div class="f_friend">
        <p class="find_friends" id="profile_friends_header"><?= LanguageUtils::getText("LANG_PAGE_FOLLOWING_TEMPLATE_FIND_FRIENDS") ?></p>

        <button type="button" name="" value=""
                id="profile_friends_fb_button"
                class="face back_btn sosyal_icon"></button>

        <button type="button" name="" value=""
                id="profile_friends_tw_button"
                class="tweet back_btn sosyal_icon"></button>

        <button type="button" name="" value=""
                id="profile_friends_gg_button"
                class="googl_plus back_btn sosyal_icon"></button>

        <!-- <button type="button" name="" value=""
                id="profile_friends_fq_button"
                class="googl_plus back_btn sosyal_icon"></button>  -->


        <button type="button" id="profile_friends_find"  class="invite_btn friends_find_buttons" style="float: right;width: 91px;"><?= LanguageUtils::getText("LANG_PAGE_FOLLOWING_TEMPLATE_FIND_FRIENDS") ?></button>
        <button style="display: none;" id="addSocialReturnButton"
                onclick="addSocialReturnButton();return false;"></button>
        <button style="display: none;" id="addSocialErrorReturnButton" type="button" errorText=""
                onclick="socialWindowButtonCliked=true;jQuery('#spinner').hide();showRegisterError(this);"></button>
    </div>
    <div style="display: block; min-height: 20px;">
        <div class="add_t_ek" id="spinner" style="display: none;background-image: none;padding-left: 0px;">
            <img src="<?= HOSTNAME ?>images/loader.gif" style="height: 20px;">
           <!-- <span class="bold">Loading...</span> -->
        </div>
    </div>

    <p class="find_friends" id="profile_friends_p_list" style="font-size: 16px;display: none;"><?= LanguageUtils::getText("LANG_PAGE_FOLLOWING_TEMPLATE_PEOPLE_YOU_KNOW") ?></p>
    <ul class="suggest_friend_ul"  id="profile_friends_ul_list" style="min-height: 37px;display: none;">
        <li  id="profile_friends_li_template" style="display: none;">
            <img src="<?= HOSTNAME ?>images/anonymous.png" width="30"
                 height="30" border="0" align="absmiddle" class="follow_res" />
            <span class="follow_ad">
            </span> 
            <a type="button" 
               name="" 
               value="" 
               class="followed_btn"
               id="foll_id"
               follow_id=""
               active_class="follow_btn"
               passive_class="followed_btn"
               f_status="followed"
               onclick="followUser(-1,-1,this);">
                <span class="follow_text"><?= LanguageUtils::getText("LANG_PAGE_FOLLOWING_TEMPLATE_FOLLOW") ?></span>
                <span class="following_text"><?= LanguageUtils::getText("LANG_PAGE_FOLLOWING_TEMPLATE_FOLLOWING") ?></span>
                <span class="unfollow_text"><?= LanguageUtils::getText("LANG_PAGE_FOLLOWING_TEMPLATE_UNFOLLOW") ?></span>
            </a>
        </li>
    </ul>


    <div class="invite" id="search_people_div">
        <input name="people_search_input" type="text" id="people_search_input"
               class="user_inpt invite_friends icon_bg"   value=""
               placeholder="Search People" />
        <button type="button" name="" value="" class="invite_btn friends_find_buttons" id="people_search_button"
                onclick="return false;"><?= LanguageUtils::getText("LANG_PAGE_FOLLOWING_TEMPLATE_SEARCH") ?></button>
    </div>
    <p class="find_friends" id="profile_friends2_p_list" style="font-size: 16px;display: none;"><?= LanguageUtils::getText("LANG_PAGE_FOLLOWING_TEMPLATE_PEOPLE_YOU_MIGHT_KNOW") ?></p>
    <ul class="suggest_friend_ul"  id="profile_friends2_ul_list" style="min-height: 37px;display: none;">

    </ul>

    <div class="invite" style="margin-top: 10px;height:40px;max-height: 50px;">
        <button type="button" name="" value="" class="invite_btn friends_find_buttons" style="float: right;"
                onclick="closeFriendsPopup();return false;">Close</button>
    </div>
</div>