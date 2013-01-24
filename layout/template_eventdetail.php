<script>
    jQuery(document).ready(function(){
        //set share buttons
        jQuery("#fb_share_button").click(shareThisFacebook);
        jQuery("#tw_share_button").click(shareThisTwitter);
        jQuery("#gg_share_button").click(shareThisGoogle);
    });
</script>

<div class="genel_detay_yeni" id="genel_detay_yeni" style="display: none;padding-bottom: 45px;">
    <div class="gdy_sol">
        <h1 class="gdy_baslik" id="gdy_event_title">Event Title</h1>
        <h2 class="gdy_zaman"  id="gdy_event_date">Event Date</h2>
        <p class="gdy_metin"   id="gdy_event_description">Event Description</p>
        <div class="gdy_resim">
            <img id="big_image_header" src="<?= HOSTNAME ?>images/loader.gif" width="30" height="30" border="0" />
        </div>
        <div class="gdy_bgln">
            <div class="bgln_rsm">
                <div class="gdy_creator_img gdy_bg_loader" id="image_creator" ></div>
            </div>
            <div class="bgln_user">
                <h1 id="name_creator">Event Creator</h1>
            </div>
            <button class="gdy_btn" id="button_reshare">Reshare</button>
            <button class="gdy_btn" id="button_maybe">Maybe</button>
            <button class="gdy_btn_mavi" id="button_join">
                <img src="<?= HOSTNAME ?>images/ti.png" width="17" height="18" class="gdy_btn_res" />Join</button>
        </div>
    </div>
    <div class="gdy_sag">
        <div class="sosyal_btn">
            <button id="fb_share_button" type="button" name="" value="" class="face back_btn sosyal_icon"/>
        </div>
        <div class="sosyal_btn">
            <button id="tw_share_button" type="button" name="" value="" class="tweet back_btn sosyal_icon"/>
        </div>
        <div class="sosyal_btn">
            <button id="gg_share_button" type="button" name="" value="" class="googl_plus back_btn sosyal_icon"/>
        </div>
    </div>
    <div class="gdy_alt">
        <div class="gdy_satir" id="gdy_images_div_container">
            <div class="gdy_alt_sol">
                <img src="<?= HOSTNAME ?>images/rsm.png" width="27" height="24" align="middle" />
            </div>
            <div class="gdy_alt_orta" id="gdy_images_div">
            </div>
            <div class="gdy_alt_sag">
                <p id="gdy_images_count">5</p>
                <p><a href="#">
                        <img src="<?= HOSTNAME ?>images/bendedok.png" width="12" height="13" border="0" />
                    </a>
                </p>
            </div>
        </div>
        <div class="gdy_satir" id="gdy_users_div_container">
            <div class="gdy_alt_sol">
                <img src="<?= HOSTNAME ?>images/klnc.png" width="22" height="20" align="middle" />
            </div>
            <div class="gdy_alt_orta" id="gdy_users_div">
            </div>
            <div class="gdy_alt_sag">
                <p id="gdy_users_count">8</p>
                <p><a href="#">
                        <img src="<?= HOSTNAME ?>images/bendedok.png" width="12" height="13" border="0" />
                    </a>
                </p>
            </div>
        </div>
        <div id="write_comment" class="gdy_satir" style="display: none;">
            <div class="gdy_alt_sol_yorum">
                <img src="<?= HOSTNAME ?>images/yz.png" width="22" height="23" align="middle" style="margin-top: -5px;">
            </div>
            <div class="gdy_alt_orta_yorum gdy_alt_orta_yorum_bggri_sendbtn bggri">
                <input name="" type="text" class="gdyorum" id="sendComment" eventid="" placeholder="Your message...">
                <button class="gdy_send" type="button" onclick="sendComment()">Send</button>
            </div>
        </div>
        <div class="gdy_satir comment_classs" id="comment_template" style="display: none;">
            <div class="gdy_alt_sol_yorum">
                <div style="width:56px;height:31px;margin-top: -7px;" id="comment_user_img"></div>
            </div>
            <div class="gdy_alt_orta_yorum gdy_alt_orta_yorum_bggri bggri">
                <h1 id="comment_user"></h1>
                <p  id="comment_text"></p>
            </div>
        </div>

        <div class="tumyorumlar" id="tumyorumlar" style="display: none">
            <a href="#" id="tumyorumlar_text">See all 4 comments...</a>
        </div>
    </div>
</div>
