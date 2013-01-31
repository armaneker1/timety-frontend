<div class="follow_ekr" id="profile_friends" style="display: none;position: fixed;">
    <div class="f_friend">
        <p class="find_friends" id="profile_friends_header">Find Friends</p>

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


        <button type="button" id="profile_friends_find"  class="invite_btn" style="float: right;width: 91px;">Find Friends</button>


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

    <ul class="suggest_friend_ul"  id="profile_friends_ul_list" style="min-height: 100px;display: none;">
        <li  id="profile_friends_li_template" style="display: none;">
            <img src="<?= HOSTNAME ?>images/anonymous.jpg" width="30"
                 height="30" border="0" align="absmiddle" class="follow_res" />
            <span class="follow_ad">
            </span> 
            <button type="button" name="" value="" class="followed_btn"
                    id="foll_id"
                    onclick="unfollowUser(-1,-1,this);">follow</button>
        </li>
    </ul>

    <div class="invite" style="margin-top: 10px;height:40px;max-height: 50px;margin-right: 3px;">
        <button type="button" name="" value="" class="invite_btn" style="float: right;"
                onclick="closeFollowing();return false;">Close</button>
    </div>
</div>