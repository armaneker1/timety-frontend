/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function btnClickCreateAccount(){
    mixpanel.track("create_account");
}

function btnClickPersonelInfo(birthday, gender, location){
    mixpanel.track("personel_info", {
        'birthday': birthday,
        'gender': gender,
        'location': location
    });
}

function btnClickPersonelLikes(){
    mixpanel.track("add_like");
}

function btnClickFollowPeople(){
    mixpanel.track("follow_people");
}

function btnClickStartAddEvent(){
    mixpanel.track("start_addevent");
}

function btnClickFinishAddEvent(){
    mixpanel.track("finish_addevent");
}