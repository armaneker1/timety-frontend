function openModalPanel(id) {
    var data = JSON.parse(localStorage.getItem('event_' + id));
    data.images=JSON.parse(data.images);
    if (!data) return;
    
    var detailModalPanelBackground = document.getElementById('div_follow_trans');
    jQuery(detailModalPanelBackground).attr('onclick','closeModalPanel()');
   
    var detailModalPanel = document.createElement("div");
    /////
    jQuery(detailModalPanel).attr('id', 'genel_detay_yeni');
    //stop click background when click this div
    jQuery(detailModalPanel).on('click',function(e){
        e.stopPropagation();
        e.preventDefault();
        return false;
    });
    jQuery(detailModalPanel).addClass('genel_detay_yeni');
    
    //gdy_sol
    var gdy_solDIV = document.createElement('div');
    jQuery(gdy_solDIV).addClass('gdy_sol');

    var gdySolH1 = document.createElement('h1');
    jQuery(gdySolH1).addClass('gdy_baslik');
    jQuery(gdySolH1).append(data.title);

    var gdySolH2 = document.createElement('h2');
    jQuery(gdySolH2).addClass('gdy_zaman');
    jQuery(gdySolH2).append(data.startDateTime);

    var gdySolP = document.createElement('p');
    jQuery(gdySolP).addClass('gdy_metin');
    jQuery(gdySolP).append(data.description);

    var gdySolP2 = document.createElement('p');
    var gdySolP2DIV=document.createElement('div');
    jQuery(gdySolP2DIV).attr('style', 'width:560px;height:295px;text-align:center;');
    var gdySolP2Img = document.createElement('img');
    if(data.headerImage && data.headerImage.url)
    {
         jQuery(gdySolP2Img).attr('src', data.headerImage.url);   
    }
    //jQuery(gdySolP2Img).attr('width', 560);
    //jQuery(gdySolP2Img).attr('height', 295);
    jQuery(gdySolP2Img).attr('style', 'position:relative;margin-left:auto;margin-right:auto;');

    jQuery(gdySolP2DIV).append(gdySolP2Img);
    jQuery(gdySolP2).append(gdySolP2DIV);
    jQuery(gdy_solDIV).append(gdySolH1);
    jQuery(gdy_solDIV).append(gdySolH2);
    jQuery(gdy_solDIV).append(gdySolP);
    jQuery(gdy_solDIV).append(gdySolP2);
    jQuery(detailModalPanel).append(gdy_solDIV);

    //gdy_sag
    var gdy_sagDIV = document.createElement('div');
    jQuery(gdy_sagDIV).addClass('gdy_sag');

    var socialDIV = document.createElement('div');
    jQuery(socialDIV).addClass('sosyal_btn');
    var socialDIVBtn = document.createElement('button');
    jQuery(socialDIVBtn).attr('type', 'button');
    jQuery(socialDIVBtn).addClass('back_btn sosyal_icon');
    jQuery(socialDIV).append(socialDIVBtn);
    var zmn = jQuery(socialDIV).clone();
    var face = jQuery(socialDIV).clone();
    var tweet = jQuery(socialDIV).clone();
    var gplus = jQuery(socialDIV).clone();
    jQuery(zmn.children()[0]).addClass('zmn');
    jQuery(face.children()[0]).addClass('face');
    jQuery(tweet.children()[0]).addClass('tweet');
    jQuery(gplus.children()[0]).addClass('googl_plus');

    jQuery(gdy_sagDIV).append(zmn);
    jQuery(gdy_sagDIV).append(face);
    jQuery(gdy_sagDIV).append(tweet);
    jQuery(gdy_sagDIV).append(gplus);

    jQuery(detailModalPanel).append(gdy_sagDIV);


    //gdy_alt
    var gdy_altDIV = document.createElement('div');
    jQuery(gdy_altDIV).addClass('gdy_alt');


    /*
     * Images
     */
    var gdy_satirDIV_images = document.createElement('div');
    jQuery(gdy_satirDIV_images).addClass('gdy_satir');

    //add gdy_satirAltSolDIV_images
    var gdy_satirAltSolDIV_images = document.createElement('div');
    jQuery(gdy_satirAltSolDIV_images).addClass('gdy_alt_sol');

    var gdy_satirAltSolDIVImg_images = document.createElement('img');
    jQuery(gdy_satirAltSolDIVImg_images).attr('src', 'images/rsm.png');
    jQuery(gdy_satirAltSolDIVImg_images).attr('width', 27);
    jQuery(gdy_satirAltSolDIVImg_images).attr('height', 24);
    jQuery(gdy_satirAltSolDIVImg_images).attr('align', 'middle');
    jQuery(gdy_satirAltSolDIV_images).append(gdy_satirAltSolDIVImg_images);
    
    jQuery(gdy_satirDIV_images).append(gdy_satirAltSolDIV_images);
    //add gdy_satirAltSolDIV_images

    //add gdy_altDIVOrta_images
    var gdy_altDIVOrta_images = document.createElement('div');
    jQuery(gdy_altDIVOrta_images).addClass('gdy_alt_orta');
   
    for(var i=0;i<data.images.length;i++)
    {
        var gdy_altDIVOrtaIMGDIV_images=document.createElement('div');
        jQuery(gdy_altDIVOrtaIMGDIV_images).attr('style', 'width:62px;height:52px;text-align:center;overflow:hidden;');
         
        var imgOrta_images = document.createElement('img');
        jQuery(imgOrta_images).attr('src',data.images[0].url);
        //jQuery(imgOrta).attr('width', 62);
        jQuery(imgOrta_images).attr('height', 52);
        jQuery(imgOrta_images).addClass('gdy_alt_rsm');
        
        jQuery(gdy_altDIVOrtaIMGDIV_images).append(imgOrta_images);
        jQuery(gdy_altDIVOrta_images).append(gdy_altDIVOrtaIMGDIV_images);
    }

    jQuery(gdy_satirDIV_images).append(gdy_altDIVOrta_images);
    //add gdy_altDIVOrta_images

    
    var gdy_altDIVSag_images = document.createElement('div');
    jQuery(gdy_altDIVSag_images).addClass('gdy_alt_sag');

    var gdy_altDIVSagP_images = document.createElement('p');
    jQuery(gdy_altDIVSagP_images).append(data.images.length);

    jQuery(gdy_altDIVSag_images).append(gdy_altDIVSagP_images);
    
    var gdy_altDIVSagP2_images = document.createElement('p');
    var gdy_altDIVSagP2A_images = document.createElement('a');
    jQuery(gdy_altDIVSagP2A_images).attr('href', '#');

    var gdy_altDIVSagP2AImg_images = document.createElement('img');
    jQuery(gdy_altDIVSagP2AImg_images).attr('src', 'images/bendedok.png');
    jQuery(gdy_altDIVSagP2AImg_images).attr('width', 12);
    jQuery(gdy_altDIVSagP2AImg_images).attr('height', 13);
    
    jQuery(gdy_altDIVSagP2_images).append(gdy_altDIVSagP2AImg_images);
    
    jQuery(gdy_altDIVSag_images).append(gdy_altDIVSagP2_images);
    
    jQuery(gdy_satirDIV_images).append(gdy_altDIVSag_images);
    /*
     * Images
     */
    
    
    /*
     * Users
     */
    var gdy_satirDIV_users= document.createElement('div');
    jQuery(gdy_satirDIV_users).addClass('gdy_satir');

    //add gdy_satirAltSolDIV_users
    var gdy_satirAltSolDIV_users = document.createElement('div');
    jQuery(gdy_satirAltSolDIV_users).addClass('gdy_alt_sol');

    var gdy_satirAltSolDIVImg_users = document.createElement('img');
    jQuery(gdy_satirAltSolDIVImg_users).attr('src', 'images/klnc.png');
    jQuery(gdy_satirAltSolDIVImg_users).attr('width', 27);
    jQuery(gdy_satirAltSolDIVImg_users).attr('height', 24);
    jQuery(gdy_satirAltSolDIVImg_users).attr('align', 'middle');
    jQuery(gdy_satirAltSolDIV_users).append(gdy_satirAltSolDIVImg_users);
    
    jQuery(gdy_satirDIV_users).append(gdy_satirAltSolDIV_users)
    //add gdy_satirAltSolDIV_users
    
    //add gdy_altDIVOrta_users
    var gdy_altDIVOrta_users = document.createElement('div');
    jQuery(gdy_altDIVOrta_users).addClass('gdy_alt_orta');
   
    for(var i=0;i<data.images.length;i++)
    {
        var gdy_altDIVOrtaIMGDIV_users=document.createElement('div');
        jQuery(gdy_altDIVOrtaIMGDIV_users).attr('style', 'width:62px;height:52px;text-align:center;overflow:hidden;');
         
        var imgOrta_users = document.createElement('img');
        jQuery(imgOrta_users).attr('src',data.images[0].url);
        //jQuery(imgOrta).attr('width', 62);
        jQuery(imgOrta_users).attr('height', 52);
        jQuery(imgOrta_users).addClass('gdy_alt_rsm');
        
        jQuery(gdy_altDIVOrtaIMGDIV_users).append(imgOrta_users);
        jQuery(gdy_altDIVOrta_users).append(gdy_altDIVOrtaIMGDIV_users);
    }

    jQuery(gdy_satirDIV_users).append(gdy_altDIVOrta_users);
    //add gdy_altDIVOrta_images

    var gdy_altDIVSag_users= document.createElement('div');
    jQuery(gdy_altDIVSag_users).addClass('gdy_alt_sag');

    var gdy_altDIVSagP_users = document.createElement('p');
    jQuery(gdy_altDIVSagP_users).append(data.images.length);

    jQuery(gdy_altDIVSag_users).append(gdy_altDIVSagP_users);

    var gdy_altDIVSagP2_users = document.createElement('p');
    var gdy_altDIVSagP2A_users = document.createElement('a');
    jQuery(gdy_altDIVSagP2A_users).attr('href', '#');
    
    var gdy_altDIVSagP2AImg_users = document.createElement('img');
    jQuery(gdy_altDIVSagP2AImg_users).attr('src', 'images/bendedok.png');
    jQuery(gdy_altDIVSagP2AImg_users).attr('width', 12);
    jQuery(gdy_altDIVSagP2AImg_users).attr('height', 13);
    
    jQuery(gdy_altDIVSagP2_users).append(gdy_altDIVSagP2AImg_users);
    jQuery(gdy_altDIVSag_users).append(gdy_altDIVSagP2_users);
    
     jQuery(gdy_satirDIV_users).append(gdy_altDIVSag_users);
    /*
     * Users
     */

     //add Users and Images
    jQuery(gdy_altDIV).append(gdy_satirDIV_images);
    jQuery(gdy_altDIV).append(gdy_satirDIV_users);
    
    //add loader
    var loader=jQuery('<div id="modal_loader" status="0" class="gdy_satir" style="width: 100%;"><divc class="gdy_alt_sol" style="width: 100%;text-align: center;"><img src="images/loader.gif" height="" class="" style="position:relative;margin-left:auto;margin-right:auto;"></divc></div>');
    jQuery(gdy_altDIV).append(loader);
    
    
    jQuery(detailModalPanel).append(gdy_altDIV);
    jQuery('#div_follow_trans').css('display','block');
    jQuery(detailModalPanel).insertAfter(jQuery('#div_follow_trans'));
    jQuery(detailModalPanelBackground).append(detailModalPanel);
    document.body.style.overflow = "hidden";
   
    ///////////////////////
    //
    //
    //  Get Comments
    //
    //
    //    
    jQuery.ajax({
        type: 'POST',
        url: 'getComments.php',
        data: {
            'eventId':id
        },
        success: function(data){
            data= JSON.parse(data); 
            var result = '';
            if(!data.error)
            {
                if(data.length > 3 )
                    result+= '<div class="tumyorumlar modal_invisable"><a href="#">See all '+(data.length-2)+' comment(s)...</a></div>'
                data = data.slice(data.length-2,data.length);
                jQuery.each(data,function(i,e)
                {
                    var commentHTMLe = "<div class=\"gdy_satir modal_invisable\" >"+
                    "<div class=\"gdy_alt_sol\"><img src=\""+e.userPic+"\" width=\"32\" height=\"31\" align=\"middle\"></div>" +
                    "<div class=\"gdy_alt_orta bggri\">" +
                    "<h1>"+e.userName+": </h1>" +
                    "<p>" + e.comment + "</p>"+
                    "</div>" +
                    "</div>";
                    result += commentHTMLe;
                });
                
            }
            var commentHTML = '<div class="gdy_satir modal_invisable">'+  
            '<div class="gdy_alt_sol"><img src="images/yz.png" width="22" height="23" align="middle"></div>'+
            '<div class="gdy_alt_orta bggri">'+
            '<input name="" type="text" class="gdyorum" id="sendComment" eventId="'+id+'" placeholder="Your message...">'+
            '<button type="button" onclick="sendComment()" class="gdy_send">Send</button>'+
            '</div>'+
            '</div>';
            result += commentHTML;
            jQuery(gdy_altDIV).append(result);
            loadGifHandler();
        }
    });
    //////////////////////////
    
    return false;
    
    /*
    
    var firstRow = jQuery(gdy_satirDIV_images).clone();
    jQuery(firstRow).append(gdy_satirAltSolDIV);
    jQuery(firstRow).append(gdy_altDIVOrta);
    jQuery(firstRow).append(gdy_altDIVSag);
    
    //TODO: users images
    var secondRow = jQuery(firstRow).clone();
    var secondSol = jQuery(secondRow).children()[0];
    var secondOrta = jQuery(secondRow).children()[1];
    var secondSag = jQuery(secondRow).children()[2];
    jQuery(jQuery(secondSol).children()[0]).attr('src','images/klnc.png');
    jQuery(jQuery(secondOrta).children()).attr('src','images/r7.png');
    jQuery(jQuery(secondSag).children()[0]).html(8);
    
    var thirdRow = jQuery(secondRow).clone();
    var thridSol = jQuery(thirdRow).children()[0];
    var thridOrta = jQuery(thirdRow).children()[1];
    jQuery(jQuery(thirdRow).children()[2]).remove();
    jQuery(jQuery(thridSol).children()[0]).attr('src','images/ekl.png');
    //jQuery(thridOrta).addClass('bggri');
    jQuery(thridOrta).html('');
    var ortaHTML = 
    '<h1>Me: </h1>' +
    '<p> Etiam ullamcorper. Supendisse a pellentesque dui, non felis. '+
    ' Maecenas malesuada elit lectus'+
    'malesuada ultricies. Lorem ipsum dolor sit amet </p>'+
    '</div>';
    jQuery(thridOrta).html(ortaHTML);
    
    
    var fourthRow = jQuery(thirdRow).clone();
  
    var sixthRow = jQuery(thirdRow).clone();
    jQuery(jQuery(sixthRow).children()[1]).html('<input name="" type="text" class="gdyorum" value="Your message...">'+
        '<button type="button" name="" value="" class="gdy_send"> Send</button>');
    
    
    jQuery(jQuery(thirdRow).children()[1]).addClass('bggri');

    jQuery(gdy_altDIV).append(firstRow);
    jQuery(gdy_altDIV).append(secondRow);*/
}

function loadGifHandler()
{
    var loader=document.getElementById("modal_loader");
    if(loader)
    {
        var status= parseInt(jQuery(loader).attr("status"));
        status++;
        if(status==1)
        {
            jQuery(loader).remove();
            jQuery(".modal_invisable").removeClass("modal_invisable");  
        }else
        {
            jQuery(loader).attr("status",status);
        }
    }else
    {
        jQuery(loader).remove();
        jQuery(".modal_invisable").removeClass("modal_invisable");
    }
}


function closeModalPanel() {
    var genelDetayYeni = document.getElementById('genel_detay_yeni');
    jQuery(genelDetayYeni).remove();
    var detailModalPanelBackground = document.getElementById('div_follow_trans');
    jQuery(detailModalPanelBackground).attr('onclick','return false;');
    jQuery(detailModalPanelBackground).css('display','none');
    document.body.style.overflow = "scroll";
    return false;
} 



function sendComment(){
    jQuery.sessionphp.get('id',function(id){
        var userId = id;
        var comment = jQuery("#sendComment").val();
        var eventId = jQuery("#sendComment").attr('eventId');
        jQuery.ajax({
            type: "POST",
            url: "addComment.php",
            data: {
                "eventId":eventId,
                "userId":userId,
                "comment":comment
            },
            success: function(data){
                var commentHTML = "<div class=\"gdy_satir\">"+
                "<div class=\"gdy_alt_sol\"><img src=\"images/ekl.png\" width=\"32\" height=\"31\" align=\"middle\"></div>" +
                "<div class=\"gdy_alt_orta bggri\">" +
                "  <h1>Me: </h1>" +
                "  <p>" + comment + "</p>"+
                "</div>" +
                "</div>";
                jQuery(commentHTML).insertBefore(jQuery('.gdy_satir').last());
                jQuery("#sendComment").val('');
            }
        });
    });
}
