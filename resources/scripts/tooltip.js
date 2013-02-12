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
        Tipped.hide(button);
        if(text)   {
            Tipped.create(button, text, {
                skin: 'tiny',
                hook:'bottommiddle'
            });
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
        setTooltipButton(likeButton,"Like");
        /*
         * unlike button
         */
        var unLikeButton= jQuery(div).find(".like_btn_aktif");
        setTooltipButton(unLikeButton,"UnLike");
        
        /*
         * maybe button
         */
        var maybeButton= jQuery(div).find(".maybe_btn");
        setTooltipButton(maybeButton,"Maybe");
        /*
         * decline button
         */
        var declinemaybeButton= jQuery(div).find(".maybe_btn_aktif");
        setTooltipButton(declinemaybeButton,"Decline");
        
        /*
         * reshare button
         */
        var reshareButton= jQuery(div).find(".share_btn");
        setTooltipButton(reshareButton,"Reshare");
        /*
         * not share button
         */
        var notReshareButton= jQuery(div).find(".share_btn_aktif");
        setTooltipButton(notReshareButton,"Revert");
        
    
        /*
         * maybe button
         */
        var joinButton= jQuery(div).find(".join_btn");
        setTooltipButton(joinButton,"Join");
        /*
         * decline button
         */
        var declineJoinButton= jQuery(div).find(".join_btn_aktif");
        setTooltipButton(declineJoinButton,"Decline");
    }
}