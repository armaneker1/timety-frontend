function openModalPanel(id) {
    var data = JSON.parse(localStorage.getItem('event_' + id));
    var data = {
        "id": "1000050",
        "title": "asdasdsadsadi\u015faisd",
        "location": "asdsadasd",
        "description": "asdasdasdsad",
        "startDateTime": "2012-12-04 12:45:00",
        "endDateTime": "2012-12-26 12:45:00",
        "reminderType": null,
        "reminderUnit": null,
        "reminderValue": null,
        "allday": null,
        "repeat": null,
        "privacy": 0,
        "addsocial_fb": null,
        "addsocial_gg": null,
        "addsocial_fq": null,
        "addsocial_tw": null,
        "attendance": [],
        "categories": [],
        "images": [],
        "commentcount": 124,
        "peoplecount": 15,
        "remaingtime": "46min"
    };
    if (!data) return;
    
    var detailModalPanelBackground = document.getElementById('div_follow_trans');
    jQuery(detailModalPanelBackground).attr('onclick','closeModalPanel()');
   
    var detailModalPanelContainer=document.createElement("div");
    var detailModalPanel = document.createElement("div");
    /////
    jQuery(detailModalPanel).attr('id', 'genel_detay_yeni');
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
    var gdySolP2Img = document.createElement('img');
    jQuery(gdySolP2Img).attr('src', 'images/kroki.png');
    jQuery(gdySolP2Img).attr('width', 560);
    jQuery(gdySolP2Img).attr('height', 295);

    jQuery(gdySolP2).append(gdySolP2Img);
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

    var gdy_satirDIV = document.createElement('div');
    jQuery(gdy_satirDIV).addClass('gdy_satir');

    var gdy_satirAltSolDIV = document.createElement('div');
    jQuery(gdy_satirAltSolDIV).addClass('gdy_alt_sol');

    var gdy_satirAltSolDIVImg = document.createElement('img');
    jQuery(gdy_satirAltSolDIVImg).attr('src', 'images/rsm.png');
    jQuery(gdy_satirAltSolDIVImg).attr('width', 27);
    jQuery(gdy_satirAltSolDIVImg).attr('height', 24);
    jQuery(gdy_satirAltSolDIVImg).attr('align', 'middle');
    jQuery(gdy_satirAltSolDIV).append(gdy_satirAltSolDIVImg);

    var gdy_altDIVOrta = document.createElement('div');
    jQuery(gdy_altDIVOrta).addClass('gdy_alt_orta');

    var imgOrta = document.createElement('img');
    jQuery(imgOrta).attr('src', 'images/r6.png');
    jQuery(imgOrta).attr('width', 62);
    jQuery(imgOrta).attr('height', 52);
    jQuery(imgOrta).addClass('gdy_alt_rsm');
    jQuery(gdy_altDIVOrta).append(imgOrta.clone());
    jQuery(gdy_altDIVOrta).append(imgOrta.clone());
    jQuery(gdy_altDIVOrta).append(imgOrta.clone());
    jQuery(gdy_altDIVOrta).append(imgOrta.clone());
    jQuery(gdy_altDIVOrta).append(imgOrta.clone());
    jQuery(gdy_altDIVOrta).append(imgOrta.clone());
    jQuery(gdy_altDIVOrta).append(imgOrta.clone());


    var gdy_altDIVSag = document.createElement('div');
    jQuery(gdy_altDIVSag).addClass('gdy_alt_sag');

    var gdy_altDIVSagP = document.createElement('p');
    jQuery(gdy_altDIVSagP).append(5);

    var gdy_altDIVSagP2 = document.createElement('p');
    var gdy_altDIVSagP2A = document.createElement('a');
    jQuery(gdy_altDIVSagP2A).attr('href', '#');

    var gdy_altDIVSagP2AImg = document.createElement('img');
    jQuery(gdy_altDIVSagP2AImg).attr('src', 'images/bendedok.png');
    jQuery(gdy_altDIVSagP2AImg).attr('width', 12);
    jQuery(gdy_altDIVSagP2AImg).attr('height', 13);
    
    
    jQuery(gdy_altDIVSag).append(gdy_altDIVSagP);
    jQuery(gdy_altDIVSagP2).append(gdy_altDIVSagP2AImg);
    jQuery(gdy_altDIVSag).append(gdy_altDIVSagP2);


    var firstRow = jQuery(gdy_satirDIV).clone();
    jQuery(firstRow).append(gdy_satirAltSolDIV);
    jQuery(firstRow).append(gdy_altDIVOrta);
    jQuery(firstRow).append(gdy_altDIVSag);
    
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
    var fifthRow = '<div class="tumyorumlar"><a href="#">See all 4 comments...</a></div>' 
    var sixthRow = jQuery(thirdRow).clone();
    jQuery(jQuery(sixthRow).children()[1]).html('<input name="" type="text" class="gdyorum" value="Your message...">'+
        '<button type="button" name="" value="" class="gdy_send"> Send</button>');
    
    
    jQuery(jQuery(thirdRow).children()[1]).addClass('bggri');

    jQuery(gdy_altDIV).append(firstRow);
    jQuery(gdy_altDIV).append(secondRow);
    jQuery(gdy_altDIV).append(thirdRow);
    jQuery(gdy_altDIV).append(fourthRow);
    jQuery(gdy_altDIV).append(fifthRow);
    jQuery(gdy_altDIV).append(sixthRow);
    jQuery(detailModalPanel).append(gdy_altDIV);
    

    jQuery('#div_follow_trans').css('display','block');
    jQuery(detailModalPanel).insertAfter(jQuery('#div_follow_trans'));
    jQuery(detailModalPanelBackground).append(detailModalPanel);
    document.body.style.overflow = "hidden";
    return false;
}

function closeModalPanel() {
    var genelDetayYeni = document.getElementById('genel_detay_yeni');
    jQuery(genelDetayYeni).remove();
    var detailModalPanelBackground = document.getElementById('div_follow_trans');
    jQuery(detailModalPanelBackground).css('display','none');
    document.body.style.overflow = "scroll";
    return false;
} 