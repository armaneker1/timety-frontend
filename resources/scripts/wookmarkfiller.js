function wookmarkFiller(options,clear)
{
    var pager = 15;
    var page = clear ? 0 :(Math.floor(jQuery('.main_event .main_event_box').length / pager)+1);
    var userId = -1;
    var channel = jQuery('.top_menu_ul_li_a_selected').attr('channelId') || 1;
    var searchText = jQuery('#hiddenSearch').val() || '';
    var dateSelected = null;
    jQuery.sessionphp.get('id',function(data){
        if(data) userId =data;
        jQuery.ajax({
            type: 'GET',
            url: 'getEvents.php',
            data: {
                'userId':userId,
                'pageNumber':page,
                'pageItemCount':pager,
                'date':dateSelected,
                'query':searchText,
                'type':channel
            },
            success: function(data){
                jQuery('#hiddenSearch').val('');
                var dataJSON = jQuery.parseJSON(data);
                if(clear) jQuery('.main_event').html('');
                wookmarkHTML(dataJSON);
                //function tm()
                //{
                    if(handler) handler.wookmarkClear();
                    handler = jQuery('.main_event .main_event_box');
                    handler.wookmark(options);
                //}
                
                //setTimeout(tm,100);
            }
        });
    });
}

function wookmarkHTML(dataArray)
{
    if(!dataArray.error)
    {
        jQuery.each(dataArray, function(i, data) { 
            //whole html    
            var result = document.createElement('div');
            jQuery(result).addClass('main_event_box');
            jQuery(result).attr('date',data.endDateTime);
            // img DIV
            var imgDiv = document.createElement('div');
            jQuery(imgDiv).addClass('m_e_img');    
            jQuery(imgDiv).attr('onclick','return openModalPanel('+data.id+');');

            //IMG tag
            var img = document.createElement('img');
            jQuery(img).attr('src',data.headerImage.url);
            if(data.headerImage.width && data.headerImage.width!=0)
                jQuery(img).attr('width',data.headerImage.width);
            else
                jQuery(img).attr('width',186);
            if(data.headerImage.height && data.headerImage.height!=0)
                jQuery(img).attr('height',data.headerImage.height);
            //jQuery(img).attr('heigh',219);
            jQuery(img).addClass('main_draggable');

            //binding DIV with Image
            jQuery(imgDiv).append(img);
            jQuery(result).append(imgDiv);

            //content DIV
            var contentDIV = document.createElement('div');
            jQuery(contentDIV).addClass('m_e_metin');


            //title
            var titleDIV = document.createElement('div');
            jQuery(titleDIV).addClass('m_e_baslik');
            jQuery(titleDIV).append(data.title);
            jQuery(contentDIV).append(titleDIV);
            
            
            
            //description
            var descriptionDIV = document.createElement('div');
            jQuery(descriptionDIV).addClass('m_e_ackl');
            jQuery(descriptionDIV).append(data.description);
            jQuery(contentDIV).append(descriptionDIV);

         
            
            

            //durum
            var durumDIV = document.createElement('div');
            jQuery(durumDIV).addClass('m_e_drm');
            var durumUL = document.createElement('ul');

            //li mavi
            var liMavi = document.createElement('li');
            var liMaviA = document.createElement('a');
            var liMaviAImg = document.createElement('img');
            jQuery(liMaviAImg).attr('src','images/usr.png');
            jQuery(liMaviAImg).attr('width',18);
            jQuery(liMaviAImg).attr('heigh',18);
            jQuery(liMaviAImg).attr('align','absmiddle');
            jQuery(liMaviAImg).attr('border',0);
            jQuery(liMaviA).attr('href','#');
            jQuery(liMaviA).addClass('mavi_link');
            jQuery(liMavi).addClass('m_e_cizgi');
            jQuery(liMaviA).append(liMaviAImg);
            jQuery(liMaviA).append(data.attendancecount);
            jQuery(liMavi).append(liMaviA);
            jQuery(durumUL).append(liMavi);

            //li turuncu
            var liTuruncu = document.createElement('li');
            var liTuruncuA = document.createElement('a');
            var liTuruncuAImg = document.createElement('img');
            jQuery(liTuruncuAImg).attr('src','images/comm.png');
            jQuery(liTuruncuAImg).attr('width',18);
            jQuery(liTuruncuAImg).attr('heigh',18);
            jQuery(liTuruncuAImg).attr('align','absmiddle');
            jQuery(liTuruncuAImg).attr('border',0);
            jQuery(liTuruncuA).attr('href','#');
            jQuery(liTuruncuA).addClass('turuncu_link');
            jQuery(liTuruncu).addClass('m_e_cizgi');
            jQuery(liTuruncuA).append(liTuruncuAImg);
            jQuery(liTuruncuA).append(data.commentCount);
            jQuery(liTuruncu).append(liTuruncuA);
            jQuery(durumUL).append(liTuruncu);

            //li yesil
            var liYesil = document.createElement('li');
            var liYesilA = document.createElement('a');
            var liYesilAImg = document.createElement('img');
            jQuery(liYesilAImg).attr('src','images/zmn.png');
            jQuery(liYesilAImg).attr('width',18);
            jQuery(liYesilAImg).attr('heigh',18);
            jQuery(liYesilAImg).attr('align','absmiddle');
            jQuery(liYesilAImg).attr('border',0);
            jQuery(liYesilA).attr('href','#');
            jQuery(liYesilA).addClass('yesil_link');
            jQuery(liYesilA).append(liYesilAImg);
            jQuery(liYesilA).append(data.remainingtime);
            jQuery(liYesil).append(liYesilA);
            jQuery(durumUL).append(liYesil);

            if(!!(data.location)){
                var durumAlt = document.createElement('div');
                jQuery(durumAlt).addClass('m_e_alt');
                jQuery(durumAlt).append(data.location);
            }

            jQuery(durumDIV).append(durumUL);
            jQuery(contentDIV).append(durumDIV);
            jQuery(result).append(contentDIV);
            //jQuery(result).append(durumAlt);    
            localStorage.setItem('event_' + data.id,JSON.stringify(data));
            jQuery('.main_event').append(result);
        });
    }
}
