<script>
    jQuery(document).ready(function(){
        //set share buttons
        jQuery("#fb_share_button").click(shareThisFacebook);
        jQuery("#tw_share_button").click(shareThisTwitter);
        jQuery("#gg_share_button").click(shareThisGoogle);
    });
</script>

<div class="genel_detay_yeni" id="genel_detay_yeni" style="display: none;">
    <div class="gdy_sol">
        <h1 class="gdy_baslik" id="gdy_event_title">Event Title</h1>
        <h2 class="gdy_zaman"  id="gdy_event_date">Event Date</h2>
        <p class="gdy_metin"   id="gdy_event_description">Event Description</p>
        <div class="gdy_resim">
            <img id="big_image_header" src="<?=HOSTNAME?>images/loader.gif" width="30" height="30" border="0" />
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
                <img src="images/ti.png" width="17" height="18" class="gdy_btn_res" />Join</button>
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
                <img src="<?=HOSTNAME?>images/rsm.png" width="27" height="24" align="middle" />
            </div>
            <div class="gdy_alt_orta" id="gdy_images_div">
                <img class="gdy_alt_rsm" src="images/r6.png" width="62" height="52" />
                <img class="gdy_alt_rsm" src="images/r6.png" width="62" height="52" />
                <img class="gdy_alt_rsm" src="images/r6.png" width="62" height="52" />
                <img class="gdy_alt_rsm" src="images/r6.png" width="62" height="52" />
                <img class="gdy_alt_rsm" src="images/r6.png" width="62" height="52" />
                <img class="gdy_alt_rsm" src="images/r6.png" width="62" height="52" />
                <img class="gdy_alt_rsm" src="images/r6.png" width="62" height="52" />
            </div>
            <div class="gdy_alt_sag">
                <p id="gdy_images_count">5</p>
                <p><a href="#">
                        <img src="images/bendedok.png" width="12" height="13" border="0" />
                    </a>
                </p>
            </div>
        </div>
        <div class="gdy_satir">
            <div class="gdy_alt_sol">
                <img src="images/klnc.png" width="22" height="20" align="middle" />
            </div>
            <div class="gdy_alt_orta">
                <img class="gdy_alt_rsm" src="images/r7.png" width="62" height="52" />
                <img class="gdy_alt_rsm" src="images/r7.png" width="62" height="52" />
                <img class="gdy_alt_rsm" src="images/r7.png" width="62" height="52" />
                <img class="gdy_alt_rsm" src="images/r7.png" width="62" height="52" />
                <img class="gdy_alt_rsm" src="images/r7.png" width="62" height="52" />
                <img class="gdy_alt_rsm" src="images/r7.png" width="62" height="52" />
                <img class="gdy_alt_rsm" src="images/r7.png" width="62" height="52" />
            </div>
            <div class="gdy_alt_sag">
                <p>8</p>
                <p><a href="#">
                        <img src="images/bendedok.png" width="12" height="13" border="0" />
                    </a>
                </p>
            </div>
        </div>
        <div class="gdy_satir">
            <div class="gdy_alt_sol">
                <img src="images/ekl.png" width="32" height="31" align="middle" />
            </div>
            <div class="gdy_alt_orta">
                <h1>Me: </h1>
                <p> Etiam ullamcorper. Supendisse a pellentesque dui, non felis. 
                    Maecenas malesuada elit lectus
                    malesuada ultricies. Lorem ipsum dolor sit amet </p>
            </div>

        </div>
        <div class="gdy_satir">
            <div class="gdy_alt_sol">
                <img src="images/ekl.png" width="32" height="31" align="middle" />
            </div>
            <div class="gdy_alt_orta bggri">
                <h1>Me: </h1>
                <p> Etiam ullamcorper. Supendisse a pellentesque dui, non felis. 
                    Maecenas malesuada elit lectus
                    malesuada ultricies. Lorem ipsum dolor sit amet </p>
            </div>

        </div>
        <div class="tumyorumlar">
            <a href="#">See all 4 comments...</a>
        </div>
        <div class="gdy_satir">
            <div class="gdy_alt_sol">
                <img src="images/yz.png" width="22" height="23" align="middle" />
            </div>
            <div class="gdy_alt_orta bggri">
                <input name="" type="text" class="gdyorum" value="Your message..." />
                <button type="button" name="" value="" class="gdy_send"> Send</button>

            </div>

        </div>
    </div>
</div>
