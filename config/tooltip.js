jQuery(document).ready(function(){
    setTooltip();
});


function setTooltip()
{
    jQuery(".likeshare").each(function(i,val){
        setTooltipLikeShareDiv(val);
    });
}

function setTooltipLikeShareDiv(div)
{
    if(div){
        /*
         * Like button
         */
        var likeButton= jQuery(div).find(".like_btn");
        Tipped.hide(likeButton);
        Tipped.create(likeButton, "Like", {
            skin: 'tiny',
            hook:'bottommiddle'
        });
        /*
         * unlike button
         */
        var unLikeButton= jQuery(div).find(".like_btn_aktif");
        Tipped.hide(unLikeButton);
        Tipped.create(unLikeButton, "UnLike", {
            skin: 'tiny',
            hook:'bottommiddle'
        });
        
        /*
         * maybe button
         */
        var maybeButton= jQuery(div).find(".maybe_btn");
        Tipped.hide(maybeButton);
        Tipped.create(maybeButton, "Maybe", {
            skin: 'tiny',
            hook:'bottommiddle'
        });
        /*
         * decline button
         */
        var declinemaybeButton= jQuery(div).find(".maybe_btn_aktif");
        Tipped.hide(declinemaybeButton);
        Tipped.create(declinemaybeButton, "Decline", {
            skin: 'tiny',
            hook:'bottommiddle'
        });
        
        /*
         * reshare button
         */
        var reshareButton= jQuery(div).find(".share_btn");
        Tipped.hide(reshareButton);
        Tipped.create(reshareButton, "Reshare", {
            skin: 'tiny',
            hook:'bottommiddle'
        });
        /*
         * not share button
         */
        var notReshareButton= jQuery(div).find(".share_btn_aktif");
        Tipped.hide(notReshareButton);
        Tipped.create(notReshareButton, "Revert", {
            skin: 'tiny',
            hook:'bottommiddle'
        });
        
    
        /*
         * maybe button
         */
        var joinButton= jQuery(div).find(".join_btn");
        Tipped.hide(joinButton);
        Tipped.create(joinButton, "Join", {
            skin: 'tiny',
            hook:'bottommiddle'
        });
        /*
         * decline button
         */
        var declineJoinButton= jQuery(div).find(".join_btn_aktif");
        Tipped.hide(declineJoinButton);
        Tipped.create(declineJoinButton, "Decline", {
            skin: 'tiny',
            hook:'bottommiddle'
        });
    }
}