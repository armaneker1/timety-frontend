<script>
    jQuery(document).ready(function(){
        jQuery("#people_search_button").click(function(){
            jQuery.sessionphp.get('id',function(id){
                var userId = id;
                if(userId!=null && userId>0)
                {    
                    openFriendsPopup(userId, 3);
                }
            });
        });
        jQuery("#people_search_input").keyup(function(event){
            if(event.keyCode==13)
            {
                jQuery.sessionphp.get('id',function(id){
                    var userId = id;
                    if(userId!=null && userId>0)
                    {    
                        openFriendsPopup(userId, 3);
                    }
                });
              
            }
        });
    });
</script>

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

    <p class="find_friends" id="profile_friends_p_list" style="font-size: 16px;display: none;">People you know</p>
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


    <div class="invite" id="search_people_div">
        <input name="people_search_input" type="text" id="people_search_input"
               class="user_inpt invite_friends icon_bg"   value=""
               placeholder="Search People" />
        <button type="button" name="" value="" class="invite_btn" id="people_search_button"
                onclick="return false;">Search</button>
    </div>
    <p class="find_friends" id="profile_friends2_p_list" style="font-size: 16px;display: none;">People you might want to know</p>
    <ul class="suggest_friend_ul"  id="profile_friends2_ul_list" style="min-height: 100px;display: none;">

    </ul>

    <div class="invite" style="margin-top: 10px;height:40px;max-height: 50px;margin-right: 3px;">
        <button type="button" name="" value="" class="invite_btn" style="float: right;"
                onclick="closeFriendsPopup();return false;">Close</button>
    </div>
</div>