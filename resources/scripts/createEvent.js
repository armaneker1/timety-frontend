function setEventPublic(checkBox) {
    if (checkBox.checked) {
        document.getElementById("te_evet_sees").style.display = "block";
        document.getElementById("public_spacer").style.display = "block";
        document.getElementById("public_header").style.display = "block";
        document.getElementById("sees_storage_element").style.display = "block";

    } else {
        document.getElementById("te_evet_sees").style.display = "none";
        document.getElementById("public_spacer").style.display = "none";
        document.getElementById("public_header").style.display = "none";
        document.getElementById("sees_storage_element").style.display = "none";
    }
}
var STORAGE = "_storage_key";
var VISUAL = "_storage_element";
var ELEMENT = "_storage_input";

function addItem(item, type) {
    item = item.item;
    if (checkItem(item.id, type)) {
        var people = new Array();
        people = sessionStorage.getItem(type + STORAGE);
        people = JSON.parse(people);
        if (people == null || people.length < 1) {
            people = new Array();
        }
        people[people.length] = item.id;
        sessionStorage.setItem(type + STORAGE, JSON.stringify(people));
        var elm = "<p id=\"" + type + "_item_" + item.id + "\">" + item.label
        + " <a href=\"#\" onclick=\"remItem(" + item.id + ",'" + type
        + "');return false;\">Sil</a></p>";
        document.getElementById(type + VISUAL).innerHTML = document
        .getElementById(type + VISUAL).innerHTML
        + elm;
    }
}

function remItem(item, type) {
    var i = findItem(item, type);
    if (i >= 0) {
        var people = new Array();
        people = sessionStorage.getItem(type + STORAGE);
        people = JSON.parse(people);
        if (people == null) {
            people = new Array();
        }
        people = removeByIndex(people, i);
        sessionStorage.setItem(type + STORAGE, JSON.stringify(people));
        var element = document.getElementById(type + "_item_" + item);
        element.parentNode.removeChild(element);
    }
}

function checkItem(item, type) {
    var people = new Array();
    people = sessionStorage.getItem(type + STORAGE);
    people = JSON.parse(people);
    if (people != null && people.length > 0) {
        for ( var i = 0; i < people.length; i++) {
            if (people[i] != null && people[i] == item)
                return false;
        }
    }
    return true;
}

function findItem(id, type) {
    var people = new Array();
    people = sessionStorage.getItem(type + STORAGE);
    people = JSON.parse(people);
    if (people != null) {
        for ( var i = 0; i < people.length; i++) {
            if (people[i] != null && people[i] == id)
                return i;
        }
    }
    return -1;
}

function removeByIndex(array, index) {
    if (index >= 0)
        array.splice(index, 1);
    return array;
}

function addGroupBeforeSubmit(type) {
    document.getElementById(type + ELEMENT).value = sessionStorage.getItem(type
        + STORAGE);
    clear(type);
}

function clear(type) {
    sessionStorage.setItem(type + STORAGE, null);
}


function fileUploadOnComplete(id, fileName, responseJSON,image_input,width,height)
{
    setUploadImage(id, fileName,width,height);
    var inpt=document.getElementById(image_input);
    if(inpt && responseJSON.success)
    {
        inpt.value=fileName;
    }else
    {
        if(inpt)
            inpt.value="0";
    }
    
    var uploadDiv=jQuery("#"+id+"_div");
    if(uploadDiv)
    {
        putDeleteButton(id, fileName, image_input,uploadDiv);
    /*var remDiv=jQuery("#"+id+"_rem");
        if(remDiv.length>0)
        {
            remDiv.remove();
        }
        
        remDiv=jQuery("<div id=\""+id+"_rem\" class=\"akare_kapat\"></div>");
        var remButton=jQuery("<span class=\"sil icon_bg\"></span>");
        jQuery(remButton).click(function(){
            removeUploadFile(id,fileName,image_input);
        });
        jQuery(remDiv).append(remButton);
        jQuery(uploadDiv).append(remDiv);*/
    }
}

function removeUploadFile(id,fileName,image_input)
{
    var inpt=document.getElementById(image_input);
    if(inpt)
    {
        inpt.value="0";
    }
    var imageDiv=jQuery("#"+id);
    imageDiv.children().remove();
    jQuery(imageDiv).append("<a href=\"#\">click here to add image</a>");
    var uploadDiv=jQuery("#"+id+"_rem");
    uploadDiv.remove();
    jQuery.post(TIMETY_PAGE_AJAX_REMOVE_TEMPFILE, { 
        'tempFile' : fileName
    }, function(data) {
        
        }, "json");
}

function putDeleteButton(id,fileName,image_input,uploadDiv)
{
    var remDiv=jQuery("#"+id+"_rem");
    if(remDiv.length>0)
    {
        remDiv.remove();
    }
        
    remDiv=jQuery("<div id=\""+id+"_rem\" class=\"akare_kapat\"></div>");
    var remButton=jQuery("<span class=\"sil icon_bg\"></span>");
    jQuery(remButton).click(function(){
        removeUploadFile(id,fileName,image_input);
    });
    jQuery(remDiv).append(remButton);
    jQuery(uploadDiv).append(remDiv);
}

function setUploadImage(id, fileName,mWidth,mHeight)
{
    if(!mWidth)
    {
        mWidth=100;   
    }
    if(!mHeight)
    {
        mHeight=100;   
    }
    var div=document.getElementById(id);
    if(div)
    {
        while (div.hasChildNodes()) {
            div.removeChild(div.lastChild);
        }
        var img=document.createElement("div");
        jQuery(img).attr("style", "width:"+mWidth+"px;height:"+mHeight+"px;background-repeat: no-repeat !important;background-position: center center !important;");
        var myUsrImage = new Image();
        myUsrImage.src=fileName;
        var param="";
        var width=0;
        var height=0;
        width=myUsrImage.width;
        height=myUsrImage.height;
                       
        if(width>height)
        {
            if(width>mWidth)
            {
                height=(mWidth/width)*height;
                width=mWidth;
            }
        }else
        {
            if(height>mHeight)
            {
                width=(mHeight/height)*width;
                height=mHeight;
            }
        }
        if(width==0)
        {
            width=mWidth;
        }
        if(height==0)
        {
            height=mHeight;
        }
                        
        param=param+"&h="+height;
        param=param+"&w="+width;
        param=param+"&clc=1&up="+(new Date()).getTime();  
        jQuery(img).css("background","url('"+ TIMETY_PAGE_GET_IMAGE_URL+myUsrImage.getAttribute("src")+param+"')");
        
        div.appendChild(img);
    }
}


/*
 * Add Social
 */

function addSocialButtonExport(){
    socialWindowButtonCliked=true;
    getLoader(false);
    jQuery(clickedPopupButton).removeAttr('onclick');
    jQuery(clickedPopupButton).attr('onclick','toogleSocialButton(this);');
    toogleSocialButton(clickedPopupButton);
    clickedPopupButton=null;
}


function toogleSocialButton(clickedPopupButton){
    var act=jQuery(clickedPopupButton).attr('act');
    var ty=jQuery(clickedPopupButton).attr('ty');
    var check_id="#big-icon-check-fb-id";
    var inputId="#te_event_addsocial_fb";
    if(ty=="fb"){
        check_id="#big-icon-check-fb-id";
        inputId="#te_event_addsocial_fb";
    }else if(ty=="out"){
        check_id="#big-icon-check-out-id";
        inputId="#te_event_addsocial_out";
    }else if(ty=="gg"){
        check_id="#big-icon-check-gg-id";
        inputId="#te_event_addsocial_gg";
    }
    if(act=="true"){
        jQuery(clickedPopupButton).attr('act','false');
        jQuery(check_id).hide();
        jQuery(inputId).val(false);
    }else{
        jQuery(clickedPopupButton).attr('act','true');
        jQuery(check_id).show();
        jQuery(inputId).val(true);
    }
}

/*
 * Add Social
 */