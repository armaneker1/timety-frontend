var TIMETY_SUBFOLDER='/timety/';
var TIMETY_HOSTNAME='http://'+window.location.hostname+TIMETY_SUBFOLDER;
var TIMETY_HOSTNAME_WWW='http://'+window.location.hostname+TIMETY_SUBFOLDER;

var TIMETY_PAGE_AJAX_SESSION=TIMETY_HOSTNAME+'session.php';


var TIMETY_PAGE_ABOUT_YOU=TIMETY_HOSTNAME+'about-you';
var TIMETY_PAGE_EVENT_DETAIL=TIMETY_HOSTNAME+'event/';
var TIMETY_PAGE_USER_DETAIL=TIMETY_HOSTNAME+'user/';
var TIMETY_PAGE_GET_IMAGE_URL=TIMETY_HOSTNAME+'getImage.php?zc=0&src=';
var TIMETY_PAGE_UPDATE_PROFILE=TIMETY_HOSTNAME+'profile';
var TIMETY_PAGE_UPDATE_EVENT=TIMETY_HOSTNAME+'updateevent/';
var TIMETY_PAGE_SIGNUP=TIMETY_HOSTNAME+'signup/';

var TIMETY_PAGE_SOCIAL_FB_LOGIN=TIMETY_HOSTNAME+'login-facebook.php';
var TIMETY_PAGE_SOCIAL_FQ_LOGIN=TIMETY_HOSTNAME+'login-foursquare.php';
var TIMETY_PAGE_SOCIAL_TW_LOGIN=TIMETY_HOSTNAME+'login-twitter.php';
var TIMETY_PAGE_SOCIAL_GG_LOGIN=TIMETY_HOSTNAME+'login-google.php';

var TIMETY_AJAX_FOLDER=TIMETY_HOSTNAME+'ajax/';
var TIMETY_PAGE_AJAX_CHECKUSERNAME=TIMETY_AJAX_FOLDER+'checkUserName.php';
var TIMETY_PAGE_AJAX_CHECKEMAIL=TIMETY_AJAX_FOLDER+'checkEmail.php';
var TIMETY_PAGE_AJAX_GETCATEGORYTOKEN=TIMETY_AJAX_FOLDER+'getCategoryToken.php';
var TIMETY_PAGE_AJAX_UNFOLLOWUSER=TIMETY_AJAX_FOLDER+'unfollowUser.php';
var TIMETY_PAGE_AJAX_FOLLOWUSER=TIMETY_AJAX_FOLDER+'followUser.php';
var TIMETY_PAGE_AJAX_CHECKINTERESTREADY=TIMETY_AJAX_FOLDER+'checkInterestReady.php';
var TIMETY_PAGE_AJAX_INVITEEMAIL=TIMETY_AJAX_FOLDER+'inviteEmail.php';
var TIMETY_PAGE_AJAX_CHECKGROUPNAME=TIMETY_AJAX_FOLDER+'checkGroupName.php';
var TIMETY_PAGE_AJAX_RESPONSETOGROUPINVITES=TIMETY_AJAX_FOLDER+'responseToGroupInvites.php';
var TIMETY_PAGE_AJAX_JOINEVENT=TIMETY_AJAX_FOLDER+'joinEvent.php';
var TIMETY_PAGE_AJAX_RESPONSETOEVENTINVITES=TIMETY_AJAX_FOLDER+'responseToEventInvites.php';
var TIMETY_PAGE_AJAX_GETEVENTATTENDANCES=TIMETY_AJAX_FOLDER+'getEventAttendances.php';
var TIMETY_PAGE_AJAX_GETCOMMENTS=TIMETY_AJAX_FOLDER+'getComments.php';
var TIMETY_PAGE_AJAX_ADDCOMMENTS=TIMETY_AJAX_FOLDER+'addComment.php';
var TIMETY_PAGE_AJAX_GETEVENTS=TIMETY_AJAX_FOLDER+'getEvents.php';
var TIMETY_PAGE_AJAX_UPLOADIMAGE=TIMETY_AJAX_FOLDER+'uploadImage.php';
var TIMETY_PAGE_AJAX_GETCATEGORY=TIMETY_AJAX_FOLDER+'getCategory.php';
var TIMETY_PAGE_AJAX_GETTAG=TIMETY_AJAX_FOLDER+'getTag.php';
var TIMETY_PAGE_AJAX_GET_TIMETY_TAG=TIMETY_AJAX_FOLDER+'getTimetyTag.php';
var TIMETY_PAGE_AJAX_GETPEOPLEORGROUP=TIMETY_AJAX_FOLDER+'getPeopleOrGroup.php';
var TIMETY_PAGE_AJAX_GETEVENT=TIMETY_AJAX_FOLDER+'getEvent.php';
var TIMETY_PAGE_AJAX_GETNOTFCOUNT=TIMETY_AJAX_FOLDER+'getNotificationsCount.php';
var TIMETY_PAGE_AJAX_GETNOTF=TIMETY_AJAX_FOLDER+'getNotifications.php';
var TIMETY_PAGE_AJAX_GETUSERCATSUBSCRIBES=TIMETY_AJAX_FOLDER+'getUserCategorySubscribes.php';
var TIMETY_PAGE_AJAX_SUBSCRIBEUSERCAT=TIMETY_AJAX_FOLDER+'subscribeUserCategory.php';
var TIMETY_PAGE_AJAX_UNSUBSCRIBEUSERCAT=TIMETY_AJAX_FOLDER+'unsubscribeUserCategory.php';
var TIMETY_PAGE_AJAX_GETFRIENDS=TIMETY_AJAX_FOLDER+'getFriends.php';
var TIMETY_PAGE_AJAX_SUBSCRIBEUSERFRIEND=TIMETY_AJAX_FOLDER+'subscribeUserFriend.php';
var TIMETY_PAGE_AJAX_UNSUBSCRIBEUSERFRIEND=TIMETY_AJAX_FOLDER+'unsubscribeUserFriend.php';
var TIMETY_PAGE_AJAX_GETEVENTIMAGES=TIMETY_AJAX_FOLDER+'getEventImages.php';
var TIMETY_PAGE_AJAX_REMOVE_TEMPFILE=TIMETY_AJAX_FOLDER+'removeTempFile.php';
var TIMETY_PAGE_AJAX_GET_USER_INFO=TIMETY_AJAX_FOLDER+'getUserInfo.php';
var TIMETY_PAGE_AJAX_RESHARE_EVENT=TIMETY_AJAX_FOLDER+'reshareEvent.php';
var TIMETY_PAGE_AJAX_LIKE_EVENT=TIMETY_AJAX_FOLDER+'likeEvent.php';
var TIMETY_PAGE_AJAX_GET_FOLLOWERS=TIMETY_AJAX_FOLDER+'getFollowers.php';
var TIMETY_PAGE_AJAX_GET_FOLLOWINGS=TIMETY_AJAX_FOLDER+'getFollowing.php';
var TIMETY_PAGE_AJAX_GET_USER_SOCAIL_PROVIDERS=TIMETY_AJAX_FOLDER+'getUserSocialProviders.php';
var TIMETY_PAGE_AJAX_GET_USER_FRIEND_RECOMMENDATIONS=TIMETY_AJAX_FOLDER+'getUserFriendRecommendations.php';
var TIMETY_PAGE_AJAX_GET_USER_SOCIAL_FRIENDS=TIMETY_AJAX_FOLDER+'getUserSocialFriends.php';
var TIMETY_PAGE_AJAX_GET_SOCIAL_PIC=TIMETY_AJAX_FOLDER+'getUserSocialPicture.php';
var TIMETY_PAGE_AJAX_GET_EVENT_USER_RELATION=TIMETY_AJAX_FOLDER+'getEventUserRelation.php';
var TIMETY_PAGE_AJAX_UPDATE_USER_INFO=TIMETY_AJAX_FOLDER+'updateUserInfo.php';
var TIMETY_PAGE_AJAX_CREATE_QUICK_EVENT=TIMETY_AJAX_FOLDER+'createQuickEvent.php';
var TIMETY_PAGE_AJAX_INIT_USER_REDIS=TIMETY_AJAX_FOLDER+'initUserRecommendation.php';
var TIMETY_PAGE_AJAX_MARK_NOTF_READ=TIMETY_AJAX_FOLDER+'markNotificationsRead.php';
var TIMETY_PAGE_AJAX_UPDATE_USER_STATISTICS=TIMETY_AJAX_FOLDER+'updateUserStatistics.php';
var TIMETY_PAGE_AJAX_CHECK_USER_FOLLOW_STATUS=TIMETY_AJAX_FOLDER+'checkUserFollowStatus.php';
var TIMETY_PAGE_AJAX_TWITTER_USER_INTEREST=TIMETY_AJAX_FOLDER+'twiiterUserInterest.php';
var TIMETY_PAGE_AJAX_FACEBOOK_USER_INTEREST=TIMETY_AJAX_FOLDER+'facebookUserInterest.php';
var TIMETY_PAGE_AJAX_GET_CITY_MAPS=TIMETY_AJAX_FOLDER+'getCityMaps.php';
var TIMETY_PAGE_AJAX_GET_CITY_ID=TIMETY_AJAX_FOLDER+'getCityId.php';
var TIMETY_PAGE_AJAX_IMG_UPLOAD=TIMETY_AJAX_FOLDER+'image_handling.php';
var TIMETY_PAGE_AJAX_SET_USER_TIMEZONE=TIMETY_AJAX_FOLDER+'setUserTimeZone.php';
var TIMETY_PAGE_AJAX_GET_USER_EVENTS=TIMETY_AJAX_FOLDER+'getUserEvents.php';


var TIMETY_GOOGLE_MAPS_API_KEY="AIzaSyBEqRYW2dtiN3V6n2MLaP58MiZkoGG__Ek";
var TIMETY_GOOGLE_ANALYTICS="UA-37815681-1_1";


var FACEBOOK_TEXT='facebook';
var FOURSQUARE_TEXT='foursquare';
var GOOGLE_PLUS_TEXT='google_plus';
var TWITTER_TEXT='twitter';


var TIMETY_MAIN_IMAGE_DEFAULT_WIDTH=236;
var TIMETY_MAIN_IMAGE_DEFAULT_HEIGHT=236;
var TIMETY_POPUP_HEADER_IMAGE_DEFAULT_WIDTH=500;
var TIMETY_POPUP_HEADER_IMAGE_DEFAULT_HEIGHT=342;


var TIMETY_NOTIFICATION_CHECK_TIME=10000;

function getLocalTime(time){
    try{
        return moment.utc(time).add({
            hours:parseInt(moment().format('ZZ').substring(0,3))
        });
    }catch(exp){
        console.log(exp);
    }
}

function getUserFullName(user){
    if(typeof user != 'undefined'){
        if(user.business_user && user.business_user+""=="1"){
            if(user.business_name && user.business_name.length){
                return user.business_name;
            }
        }
        var name="";
        if(user.firstName){
            name=user.firstName+" ";
        }
        if(user.lastName){
            name=name+user.lastName;
        }
        return name;
    }
    return "";
}

function s46786() {
    return Math.floor((1 + Math.random()) * 0x10000)
    .toString(16)
    .substring(1);
};

function generate_guid() {
    return s46786() + s46786() + '-' + s46786() + '-' + s46786() + '-' +
    s46786() + '-' + s46786() + s46786() + s46786();
}
