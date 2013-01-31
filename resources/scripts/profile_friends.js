function openFollowing(userId,type)
{
    if(userId!=null && userId>0)
    { 
        /*
         * clear screen
         */
        jQuery("#profile_friends_fb_button").hide();
        jQuery("#profile_friends_tw_button").hide();
        jQuery("#profile_friends_gg_button").hide();
        jQuery("#profile_friends_fq_button").hide();
        jQuery("#profile_friends_ul_list").hide();
    
        /*
         * clear screen
         */
    
        if(type==1 || type==2)
        {
            var url="";
            var term="";
            if(type==1)
            {
                //following
                //set header 
                jQuery("#profile_friends_header").text("Following"); 
                url=TIMETY_PAGE_AJAX_GETFRIENDS;
            } else if(type==2) {

                //follower 
                //set header 
                jQuery("#profile_friends_header").text("Followers");
                url=TIMETY_PAGE_AJAX_GET_FOLLOWERS;
            }
            
            if(url){
                /*
                 * get data 
                 */
                jQuery.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        'userId':userId,
                        'term':'?-2'
                    },
                    success: function(data){
                        try{
                            if(typeof data == "string"){
                                data= jQuery.parseJSON(data);
                            }
                        }catch(e) {
                            console.log(e);
                            console.log(data);
                        }
                        
                        if(!data.error) {   
                            fillFriendsUL(userId,data,type);    
                        }
                        jQuery('#spinner').hide();
                    }
                });
            }
        }else if(type==3)
        {
            //find friends
            //set header 
            jQuery("#profile_friends_header").text("Find Friends");
            /*
             * social buttons
             */
            jQuery("#profile_friends_fb_button").show();
            jQuery("#profile_friends_tw_button").show();
            jQuery("#profile_friends_gg_button").show();
            jQuery("#profile_friends_fq_button").show();
        }else
        {
            return false;
        }
    
        /*
         * show Backgrund
         */
        var friendsBackground = document.getElementById('div_follow_trans');
        jQuery(friendsBackground).unbind('click');
        jQuery(friendsBackground).bind('click',function(e){
            if(e && e.target && e.target.id && e.target.id == "div_follow_trans")
            {
                closeFollowing();
            }
        });
        jQuery(friendsBackground).show();
        document.body.style.overflow = "hidden";
        /*
         * show Backgrund
         */
    
        /*
         * show spinner
         */
        jQuery('#spinner').show();
    
        /*
         * show Panel
         */
        jQuery("#profile_following").show();
        
    /*
         * show Panel
         */
    }
    
}

function closeFollowing()
{
    /*
     * hide Panel
     */
    jQuery("#profile_following").hide();
    /*
     * hide Panel
     */
    
    
    /*
     * hide Backgrund
     */
    var friendsBackground = document.getElementById('div_follow_trans');
    jQuery(friendsBackground).unbind('click');
    jQuery(friendsBackground).bind('click',function(){
        return false;
    });
    jQuery(friendsBackground).hide();
    document.body.style.overflow = "scroll";
/*
     * hide Backgrund
     */
}


function fillFriendsUL(userId,data,type){
    if(userId && data && data.length>0 && (type==1 || type==2 || type==3))
    {
        var list=jQuery("#profile_friends_ul_list");
        var template=jQuery(list).find("#profile_friends_li_template");
        /*
         * clear list
         */
        list.children().not(template).remove();
        for(var i=0;i<data.length;i++)
        {
            if(data[i] && data[i].id)
            {
                var item=template.clone();
                /*
                 * fill item
                 */
                var img=jQuery(item).find("img");
                if(img && img.length>0) {
                    jQuery(img).attr('src',data[i].userPicture);
                }
                var span=jQuery(item).find("span");
                if(span && span.length>0) {
                    var text=data[i].fullName+" ("+data[i].username+")";
                    if(text.length>30) {
                        text=text.substring(0, 30);
                    }
                    jQuery(span).text(text);
                }
                var button=jQuery(item).find("button");
                if(button && button.length>0) {
                    jQuery(button).attr('id','foll_'+data[i].id);
                    
                    if(type==1) {
                        setFollowButton(button,true,userId,data[i].id);
                    } else if(type==2) {
                        if(data[i].followed) {
                            setFollowButton(button,true,userId,data[i].id);
                        }else {
                            setFollowButton(button,false,userId,data[i].id);
                        }
                    }else if(type==3)  {
                        setFollowButton(button,false,userId,data[i].id);
                    }
                }
                /*
                 * add item
                 */
                jQuery(item).show();
                list.append(item);
            }
        }
        
        if(data.length>0)
        {
            jQuery(list).show();
        }
    }
}


   
function setFollowButton(button,type,userId,id)
{
    jQuery(button).removeAttr("onclick");
    if(type)
    {  
        jQuery(button).removeClass('follow_btn');
        jQuery(button).addClass('followed_btn');
        jQuery(button).text("unfollow");  
        jQuery(button).attr("onclick","unfollowUser("+userId+","+id+",this);");
    }else
    {
        jQuery(button).removeClass('followed_btn');
        jQuery(button).addClass('follow_btn');
        jQuery(button).text("follow");     
        jQuery(button).attr("onclick","followUser("+userId+","+id+",this);");
    }
}