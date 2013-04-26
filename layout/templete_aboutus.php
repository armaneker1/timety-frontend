<script>

    function toggleAboutUsPopup(){
        
        if(jQuery("#aboutus_div").is(":visible")){
            jQuery("#aboutus_div").fadeOut()
            .css({
                bottom:0
            })
            .animate({
                bottom:-250
            }, 100);
        }else{
            jQuery("#aboutus_div").fadeIn()
            .css({
                bottom:-250
            })
            .animate({
                bottom:0
            }, 100);
        }
    }

    jQuery(".about_timety_button").click(toggleAboutUsPopup);
</script>
<div class="aboutus_div" id="aboutus_div" >
    <div class="aboutus_div_row">
        <div class="aboutus_div_cell">
            <span class="aboutus_header"><?=  LanguageUtils::getText("LANG_PAGE_ABOUT_US_DISCOVER_EVENTS")?></span><br/>
            <span><?=  LanguageUtils::getText("LANG_PAGE_ABOUT_US_DISCOVER_EVENTS_TEXT")?></span><br/>
            <div class="aboutus_img aboutus_img_discover"></div>
        </div>
        <div class="aboutus_div_cell">
            <span class="aboutus_header"><?=  LanguageUtils::getText("LANG_PAGE_ABOUT_US_SHARE_EVENTS")?></span><br/>
            <span><?=  LanguageUtils::getText("LANG_PAGE_ABOUT_US_SHARE_EVENTS_TEXT")?></span><br/>
            <div class="aboutus_img aboutus_img_share"></div>
        </div>
        <div class="aboutus_div_cell">
            <span class="aboutus_header"><?=  LanguageUtils::getText("LANG_PAGE_ABOUT_US_SHARE_TRACK_PEOPLE")?></span><br/>
            <span><?=  LanguageUtils::getText("LANG_PAGE_ABOUT_US_SHARE_TRACK_PEOPLE_TEXT")?></span><br/>
            <div class="aboutus_img aboutus_img_track"></div>
        </div>
    </div>
</div>
