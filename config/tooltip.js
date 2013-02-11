jQuery(document).ready(function(){
    setTooltip();
});


function setTooltip()
{
    jQuery(".like_btn").each(function(i,val){
        Tipped.hide(val);
        Tipped.create(val, "Like", {
            skin: 'tiny',
            hook:'bottommiddle'
        });
    }); 
    
    jQuery(".maybe_btn").each(function(i,val){
        Tipped.hide(val);
        Tipped.create(val, "Maybe",{
            skin: 'tiny',
            hook:'bottommiddle'
        });
    });
    
    jQuery(".share_btn").each(function(i,val){
        Tipped.hide(val);
        Tipped.create(val, "Reshare",{
            skin: 'tiny',
            hook:'bottommiddle'
        });
    });
    
    jQuery(".join_btn").each(function(i,val){
        Tipped.hide(val);
        Tipped.create(val, "Join",{
            skin: 'tiny',
            hook:'bottommiddle'
        });
    });
}