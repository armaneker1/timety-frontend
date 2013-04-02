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
            <span class="aboutus_header">Discover new events</span><br/>
            <span>We help you in finding events you will like</span><br/>
            <div class="aboutus_img aboutus_img_discover"></div>
        </div>
        <div class="aboutus_div_cell">
            <span class="aboutus_header">Share events</span><br/>
            <span>We will make sure everyone discovers your event</span><br/>
            <div class="aboutus_img aboutus_img_share"></div>
        </div>
        <div class="aboutus_div_cell">
            <span class="aboutus_header">Track people you love</span><br/>
            <span>Track people you love and see what they are up to</span><br/>
            <div class="aboutus_img aboutus_img_track"></div>
        </div>
    </div>
</div>
