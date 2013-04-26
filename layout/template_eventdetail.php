<div class="genel_detay_yeni" id="genel_detay_yeni" style="position: relative; display: none;padding-bottom: 45px;">
    <div class="gdy_sol">
        <h1 class="gdy_baslik" id="gdy_event_title">Event Title</h1>
        <p class="gdy_metin"   id="gdy_event_description">Event Description</p>
        <p class="gdy_zaman"  id="gdy_event_date">
            <span class="gn">DD</span> 
            <span class="ay">MM</span> 
            <span class="yil">YYYY</span> 
            <span class="hd_line">|</span> 
            <span class="gn d_day">dddd</span>
            <span class="">at</span> 
            <span class="gn d_hour">HH:mm</span>
        </p>
        <p class="gdy_location"  id="gdy_event_location">Event Location</p>
        <div class="gdy_resim">
            <img id="big_image_header" src="<?= HOSTNAME ?>images/loader.gif" width="30" height="30" border="0" />
            <iframe id="youtube_player" style="display: none;" type="text/html" width="" height="" frameborder="0" src="<?=HOSTNAME?>cache/index.html"></iframe>
        </div>
        <div class="gdy_bgln">
            <div class="bgln_rsm">
                <div class="gdy_creator_img gdy_bg_loader" id="image_creator" ></div>
            </div>
            <div class="bgln_user">
                <h1 id="name_creator" class="name_creator">Event Creator</h1>
                <h1 id="about_creator" class="about_creator"></h1>
                <a  type="button" name="" value="" disabled="disabled" class="modal_follow_btn" id="foll_modal_creator" onclick="followUser(null,null,this,'modal_');">
                    <span class="follow_text"><?=  LanguageUtils::getText("LANG_PAGE_EVENT_DETAIL_FOLLOW")?></span>
                    <span class="following_text"><?=  LanguageUtils::getText("LANG_PAGE_EVENT_DETAIL_FOLLOWING")?></span>
                    <span class="unfollow_text"><?=  LanguageUtils::getText("LANG_PAGE_EVENT_DETAIL_UNFOLLOW")?></span>
                </a>
            </div>


            <!-- like and share -->
            <div class="likeshare" style="float: right;right: 12px;" id="likeshare_modal_panel">
                <!-- like button -->
                <div id="div_like_btn_div_modal_panel" class="timelineLikes" style=""> 
                    <a  id="div_like_btn_modal_panel" 
                        data-toggle="tooltip" 
                        data-placement="bottom" 
                        title=""
                        class="timelineButton like_btn"  
                        class_aktif="like_btn_aktif" 
                        class_pass="like_btn"      
                        pressed="false"  
                        onclick="return false;"></a>
                </div>
                <!-- like button -->


                <!-- share button -->
                <div id="div_share_btn_div_modal_panel" class="timelineLikes" style=""> 
                    <a  id="div_share_btn_modal_panel" 
                        data-toggle="tooltip" 
                        data-placement="bottom" 
                        title=""
                        class="timelineButton share_btn"
                        class_aktif="share_btn_aktif" 
                        class_pass="share_btn"      
                        pressed="false"  
                        onclick="return false;"></a>
                </div>
                <!-- share button -->

                <!-- maybe button -->
                <div id="div_maybe_btn_div_modal_panel" class="timelineLikes" style=""> 
                    <a  id="div_maybe_btn_modal_panel" 
                        data-toggle="tooltip" 
                        data-placement="bottom" 
                        title=""
                        class="timelineButton maybe_btn"  
                        class_aktif="maybe_btn_aktif" 
                        class_pass="maybe_btn"      
                        pressed="false"  
                        onclick="return false;"></a>
                </div>
                <!-- maybe button -->

                <!-- join button -->
                <div id="div_join_btn_div_modal_panel" class="timelineLikes" style=""> 
                    <a  id="div_join_btn_modal_panel" 
                        data-toggle="tooltip" 
                        data-placement="bottom" 
                        title=""
                        class="timelineButton join_btn"
                        class_aktif="join_btn_aktif" 
                        class_pass="join_btn"      
                        pressed="false"  
                        onclick="return false;"></a>
                </div>
                <!-- join button -->

                <!-- edit button -->
                <div id="div_edit_btn_div_modal_panel" class="timelineLikes" style="float: right;margin-right: 9px;"> 
                    <a  id="div_edit_btn_modal_panel" 
                        data-toggle="tooltip" 
                        data-placement="bottom" 
                        title=""
                        class="timelineButton edit_btn"  
                        class_aktif="edit_btn_aktif" 
                        class_pass="edit_btn" 
                        onclick="return false;"></a>
                </div>
            </div>
            <!-- like and share -->


            <!--<button type="button" class="gdy_btn" id="button_reshare">Reshare</button>
            <button type="button" class="gdy_btn" id="button_maybe">Maybe</button>
            <button type="button" class="gdy_btn_mavi" id="button_join">
                <img src="<?= HOSTNAME ?>images/ti.png" width="17" height="18" class="gdy_btn_res" />Join</button>
            -->
        </div>
    </div>
    <div class="gdy_sag">
        <div class="sosyal_btn">
            <button id="fb_share_button" type="button" class="big-icon-f-share btn-sign-big-share fb facebook" ></button>
        </div>
        <div class="sosyal_btn">
            <button id="gg_share_button" type="button" class="big-icon-g-share btn-sign-big-share google"></button>
        </div>
        <div class="sosyal_btn">
            <button id="tw_share_button" type="button"  class="big-icon-t-share btn-sign-big-share tw twitter"></button>
        </div>
    </div>
    <div class="gdy_alt">
        <div class="gdy_satir" id="gdy_images_div_container" style="display: none;">
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
                <span style="font-family: Arial, Helvetica, sans-serif;font-size: 11px;color: #959595;"><?=  LanguageUtils::getText("LANG_PAGE_EVENT_DETAIL_JOINED_TEXT")?></span>
            </div>
            <div class="gdy_alt_orta" id="gdy_users_div">
            </div>
            <div class="gdy_alt_sag">
                <p id="gdy_users_count">0</p>
                <p><a href="#">
                        <img src="<?= HOSTNAME ?>images/bendedok.png" width="12" height="13" border="0" />
                    </a>
                </p>
            </div>
        </div>
        <div id="write_comment" class="gdy_satir" style="display: none;">
            <div class="gdy_alt_sol_yorum">
                <img src="<?= HOSTNAME ?>images/yz.png" width="22" height="23" align="middle" style="margin-top: -20px;">
            </div>
            <div class="gdy_alt_orta_yorum gdy_alt_orta_yorum_bggri_sendbtn bggri">
                <input name="" type="text" class="gdyorum" id="sendComment" eventid="" placeholder="<?=  LanguageUtils::getText("LANG_PAGE_EVENT_DETAIL_COMMENT_INPUT_PLACEHOLDER")?>">
                <button class="gdy_send" type="button" onclick="sendComment()"><?=  LanguageUtils::getText("LANG_PAGE_EVENT_DETAIL_COMMENT_BUTTON")?></button>
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
            <a href="#" id="tumyorumlar_text"><?=  LanguageUtils::getText("LANG_PAGE_EVENT_DETAIL_COMMENT_SEE_MORE")?></a>
        </div>
    </div>
</div>
