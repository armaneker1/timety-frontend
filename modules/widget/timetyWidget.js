

var __pid;
var __width;
var __height;
var __backgroundType;
var __backgroundVal;
var __userID;

function getScriptWithAjax(filename, func)
{
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function()
    {
        if (xmlhttp.responseText != "")
        {
            try
            {
                eval(xmlhttp.responseText);
                //document.write(xmlhttp.responseText);
            }
            catch (ex)
            {
            }
        }
    };
    xmlhttp.open("GET", filename, true);
    xmlhttp.send();
}

function showWidget(_userID, lang, _width, _height, _backType, _backVal)
{
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function()
    {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
        {
            eval(xmlhttp.responseText);
            getScriptWithAjax("http://www.timety.com/widget/moment.min.js", null);
            if (document.getElementById("timetyWidget") === null)
            {
                __userID = _userID;
                __pid = 1;
                __width = _width;
                __height = _height;
                __backgroundType = _backType;
                __backgroundVal = _backVal;
                var e = document.createElement('div');
                e.id = "timetyWidget";
                e.style.width = __width;
                e.style.height = _height;
                e.style.overflow = "auto";
                e.style.backgroundImage = "url('http://timety.com/images/loader.gif')";
                e.style.backgroundPosition = "center center";
                e.style.backgroundRepeat = "no-repeat";
                if (__backgroundType == "c")
                {
                    e.style.backgroundColor = _backVal;
                }
                document.body.appendChild(e);
                fetchContent();
            }
        }
    };
    xmlhttp.open("GET", "http://www.timety.com/widget/lang." + lang + ".js", true);
    xmlhttp.send();
}
var jsonedData;
function fetchContent()
{
    var xmlhttp;
    if (window.XMLHttpRequest)
    {
        xmlhttp = new XMLHttpRequest();
    }
    else
    {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function()
    {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
        {
            var data = xmlhttp.responseText;
            if (data.toString().length === 0)
            {
                document.write("Timety Widget'ına bu site üzerinde çalışma izni verilmemiştir.");
            }
            else
            {
                var __cols = parseInt(parseInt(__width) / 194);
                var columns = [];
                for (x = 0; x < __cols; x++)
                {
                    columns[x] = "";
                }
                jsonedData = JSON.parse(data);
                var length = jsonedData.length, element = null;
                var html = "<div style='font-family: Arial;' ><table cellpadding='0' cellspacing='0' border='0' style='width:" + __cols * 194 + "px;'><tr>";
                for (var i = 0, c = 0; i < length; i++, c++) {
                    if (c + 1 > __cols)
                        c = 0;
                     
                    element = jsonedData[i];
                    var zamanColor = "#8fae53";
                    var zamanIcon = "http://timety.com/images/zmn.png";
                    var durum = calculateRemainingTime(element['baslangic']);
                    if(durum == "Geçmiş" || durum == "Past")
                        {
                            zamanColor = "#ba6d6d";
                            zamanIcon = "http://timety.com/images/zmn_k.png";
                        }
                    columns[c] += '<div class="main_event_box" style="min-height: 80px; width: 194px; border: 1px  #bcbcbc; background-color: #FFF; margin-right: 3px; margin-left: 4px; float: left; margin-top: 4px; margin-bottom: 4px; box-shadow: 0px 1px 3px rgba(34,25,25,0.4);">'
                            + '<div class="m_e_img" id="div_img_event_1000847" style="padding: 4px; cursor: pointer;">'
                            + '<div style="overflow: hidden; width: 186px; margin-bottom: 0px; margin-top: 0px; min-height: 10x;">'
                            + '<a target="_blank" href="' + element["internalUrl"] + '"><img eventid="1000847" width="186"  src="' + element["image"] + '" class="main_draggable" style="padding: 4px; margin: -4px;"></a>'
                            + '</div>'
                            + '</div>'
                            + '<div class="m_e_metin" style="background-image: url(http://timety.com/images/m_ebg.png); background-repeat: repeat; border-top-width: 1px; border-bottom-width: 1px; border-top-style: solid; border-bottom-style: solid; border-top-color: #d1d1d1; border-bottom-color: #e1e1e1;">'
                            + '<div class="m_e_baslik" style="font-size: 12px; font-weight: bold; color: #6a6a6a; text-align: center; padding: 10px 2px 10px 2px; background-image: url(http://timety.com/images/u_line.png); background-repeat: repeat-x; background-position: bottom;"><a style="font-size: 12px; font-weight: bold; color: #6a6a6a;  text-decoration:none;" target="_blank" href="' + element["internalUrl"] + '">' + element["etkinlikTitle"] + '</a></div>'
                            + '<div class="m_e_com" style="font-size: 11px; color: #6a6a6a; padding-top: 7px; padding-bottom: 7px; background-image: url(http://timety.com/images/u_line.png); background-repeat: repeat-x; background-position: bottom; padding-left: 5px; font-family: arial; min-height: 22px;">'
                            + '<div style="cursor: pointer; margin: 0px; padding: 0px;">'
                            + '<div style="float:left"><img src="' + element["creatorPic"] + '" width="22" height="22"/></div><a style="text-decoration:none; font-size: 11px;color: #6a6a6a;font-family: arial;float:left; margin-top:5px; margin-left:5px;" href="' + element["creatorLink"] + '" target="_blank">' + element["yaratan"] + '</a><div style="clear:both"></div>'
                            + '</div>' 
                            + '</div>'
                            + '<div class="m_e_ackl" style="font-size: 11px; color: #6a6a6a; padding: 5px; background-image: url(http://timety.com/images/u_line.png); background-repeat: repeat-x; background-position: bottom; word-wrap: break-word;">' + element["etkinlikDescription"] + '</div>'
                            + '<div class="m_e_drm" style="padding: 5px; height: 20px; text-align: center;">'
                            + '<ul style="list-style-type: none; margin: 0px; padding-top:2px; padding-left:0px; padding-right:0px; padding-bottom:0px;; display: inline-block;">'
                            + '<li class="m_e_cizgi" style="background-image: url(http://timety.com/images/m_e_czg.png); background-repeat: no-repeat; background-position: right; float: left; padding-right: 5px; padding-left: 5px;"><a href="#" onclick="return false;" class="mavi_link" style="color: #5d91a9; font-size: 14px; font-weight: bold; text-decoration: none;">'
                            + '<div style="float:left"><img src="http://www.timety.com/images/usr.png" width="18" heigh="18" border="0"/></div><div style="float:left;padding-left:2px;">' + element["attendance"] + '</div><div style="clear:both"></div></a></li>'
                            + '<li class="m_e_cizgi" style="background-image: url(http://timety.com/images/m_e_czg.png); background-repeat: no-repeat; background-position: right; float: left; padding-right: 5px; padding-left: 5px;"><a href="#" onclick="return false;" class="turuncu_link" style="color: #ba6d6d; font-size: 14px; font-weight: bold; text-decoration: none;">'
                            + '<div style="float:left"><img src="http://www.timety.com/images/comm.png" width="18" heigh="18" border="0"/></div><div style="float:left;padding-left:2px;">' + element["comment"] + '</div><div style="clear:both"></div></a></li>'
                            + '<li style="float: left; padding-right: 5px; padding-left: 5px;"><a href="#" onclick="return false;" class="yesil_link" style="color: ' + zamanColor + '; font-size: 14px; font-weight: bold; text-decoration: none;">'
                            + '<div style="float:left"><img src="' + zamanIcon + '" width="18" heigh="18" border="0"/></div><div style="float:left;padding-left:2px;">' + calculateRemainingTime(element['baslangic']) + '</div><div style="clear:both"></div></a></li>'
                            + '</ul>'
                            + '</div>'
                            + '</div>'
                            + '</div>';
    
                }
                for (x = 0; x < __cols; x++)
                {
                    columns[x] = "<td style='min-width:194px;width:auto !important; _width: 194px; ' valign='top'>" + columns[x] + "</td>";
                    html += columns[x];
                }

                html += "</tr></div>";
                document.getElementById("timetyWidget").style.backgroundImage = "";
                document.getElementById("timetyWidget").innerHTML = html;
                RemoveTimetyWidgetScript();
            }
        }
    };

    xmlhttp.open("GET", "http://www.timety.com/widget/widget.php?UserID=" + __userID + "&req=" + window.location.host , true);
    xmlhttp.send();
}
function RemoveTimetyWidgetScript()
{
    return(EObj = document.getElementById("timetyWidgetScript")) ? EObj.parentNode.removeChild(EObj) : false;
}
function nextPage()
{
    _pid++;
}



var scripts = document.getElementsByTagName("script");
eval(scripts[ scripts.length - 1 ].innerHTML);
