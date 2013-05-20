<div class="genel_detay_yeni" id="media_panel" style="position: relative; display: none;padding-bottom: 45px;top: 50px;">
    <div class="gdy_sol">
        <div class="gdy_resim" style="position: relative;">
            <img id="media_big_image_header" src="<?= HOSTNAME ?>images/loader.gif" width="30" height="30" border="0" />
            <div id="meida_video_player" style="display: none;" ></div>
            <a  class="media_arrow media_arrow_left" onclick="prevMedia();"></a>
            <a  class="media_arrow media_arrow_right" onclick="nextMedia();"></a>
        </div>
        <div class="gdy_bgln" style="display: table;">
            <div class="bgln_rsm" style="margin-right: 5px;">
                <div class="gdy_creator_img gdy_bg_loader" id="media_image_creator" ></div>
            </div>
            <h1 id="media_name_creator" class="name_creator">Media Creator</h1>
            <p class="gdy_metin" style="font-size: 14px;font-style: normal;"   id="media_description">Media Description</p>
        </div>
    </div>
    <div class="gdy_sag">
        <div class="sosyal_btn">
            <button id="media_fb_share_button" type="button" class="big-icon-f-share btn-sign-big-share fb facebook" ></button>
        </div>
        <div class="sosyal_btn">
            <button id="media_gg_share_button" type="button" class="big-icon-g-share btn-sign-big-share google"></button>
        </div>
        <div class="sosyal_btn">
            <button id="media_tw_share_button" type="button"  class="big-icon-t-share btn-sign-big-share tw twitter"></button>
        </div>
    </div>
</div>
