jQuery(document).ready(function(){
    setTooltip();
});


function setTooltip()
{
    jQuery(".likeshare").each(function(i,val){
        setTooltipLikeShareDiv(val);
    });
}

function setTooltipButton(button,text)
{
    if(button) {
        button=jQuery(button);
        button.tooltip('destroy');
        if(text)   {
            jQuery(button).attr("data-original-title",text);
            jQuery(button).tooltip();
        }
    }
}

function setTooltipLikeShareDiv(div)
{
    if(div){
        /*
         * Like button
         */
        var likeButton= jQuery(div).find(".like_btn");
        setTooltipButton(likeButton,getLanguageText("LANG_SOCIAL_LIKE"));
        /*
         * unlike button
         */
        var unLikeButton= jQuery(div).find(".like_btn_aktif");
        setTooltipButton(unLikeButton,getLanguageText("LANG_SOCIAL_UNLIKE"));
        
        /*
         * maybe button
         */
        var maybeButton= jQuery(div).find(".maybe_btn");
        setTooltipButton(maybeButton,getLanguageText("LANG_SOCIAL_MAYBE"));
        /*
         * decline button
         */
        var declinemaybeButton= jQuery(div).find(".maybe_btn_aktif");
        setTooltipButton(declinemaybeButton,getLanguageText("LANG_SOCIAL_DECLINE"));
        
        /*
         * reshare button
         */
        var reshareButton= jQuery(div).find(".share_btn");
        setTooltipButton(reshareButton,getLanguageText("LANG_SOCIAL_RESHARE"));
        /*
         * not share button
         */
        var notReshareButton= jQuery(div).find(".share_btn_aktif");
        setTooltipButton(notReshareButton,getLanguageText("LANG_SOCIAL_REVERT"));
        
    
        /*
         * maybe button
         */
        var joinButton= jQuery(div).find(".join_btn");
        setTooltipButton(joinButton,getLanguageText("LANG_SOCIAL_JOIN"));
        /*
         * decline button
         */
        var declineJoinButton= jQuery(div).find(".join_btn_aktif");
        setTooltipButton(declineJoinButton,getLanguageText("LANG_SOCIAL_DECLINE"));
        
        /*
         * edit button
         */
        var editButton= jQuery(div).find(".edit_btn");
        setTooltipButton(editButton,getLanguageText("LANG_SOCIAL_EDIT_EVENT"));
    }
}