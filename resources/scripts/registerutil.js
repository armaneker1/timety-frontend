var INTEREST_ADD_KEY = "timete_ineterests_add";
var INTEREST_CAT_ADD_KEY = "timete_ineterests_cat_add";
var USERIDS_ADD_KEY = "_";
var ELM_ADD_KEY="add_ineterest";
var isRed = 'rgb(33, 206, 0)';



// auto complete and add /remove interest
function insertItem(elementId, item) {
    item = item.item;
    addItem(item);
    var d=document.getElementById("a_interest_item_" + item.id);
    if(!d){
        var HTML1 = "<li id=\"a_interest_item_" + item.id + "\" title=\""
        + item.label + "\">";
        var HTML2 = "<a href=\"#\" onclick=\"removeItem('" + item.id
        + "','1');return false;\" class=\"add_like_btn\">";
        var HTML3 = item.label + "</a></li>";
        jQuery("#" + elementId).append(jQuery(HTML1 + HTML2 + HTML3));
    }
}

function addItem(item) {
    if (findItemAdd(item)<0) {
        var interests=getInterests();
        if (interests == null) {
            interests = new Array();
        }
        interests[interests.length] = item;
        saveIneterests(interests);
    }
}

function removeItem(item,rm) {
    var interests=getInterests();
    if (interests == null) {
        interests = new Array();
    }
    
    var res = findItemAddById(item);
    var indx = res.indx;
    item = res.obj;
    interests = removeByIndex(interests, indx);
    saveIneterests(interests);
    
    if((rm+'')=='1')
    {
        var element = document.getElementById("a_interest_item_" + item.id);
        element.parentNode.removeChild(element);
    }
}

function findItemAdd(item) {
    var interests=getInterests();
    if (interests != null) {
        for ( var i = 0; i < interests.length; i++) {
            if (interests[i] != null && interests[i]['id'] == item.id)
                return i;
        }
    }
    return -1;
}

function findItemAddById(itemId) {
    var interests=getInterests();
    if (interests != null) {
        for ( var i = 0; i < interests.length; i++) {
            if (interests[i] != null && (interests[i]['id']+'') == (itemId+'')) {
                var res = new Object();
                res.indx = i;
                res.obj = interests[i];
                return res;
            }
        }
    }
    return null;
}

function findCategoryAddById(itemId) {
    var cats=getInterestCats();
    if (cats != null) {
        for ( var i = 0; i < cats.length; i++) {
            if (cats[i] != null && (cats[i]['id']+'') == (itemId+'')) {
                var res = new Object();
                res.indx = i;
                res.obj = cats[i];
                return res;
            }
        }
    }
    return null;
}

function removeByIndex(array, index) {
    if (index >= 0)
        array.splice(index, 1);
    return array;
}

function registerIIBeforeSubmit() {
    var interests = getInterests();
    if(!(interests && interests.length>4)){
        getInfo(true, "Select at least 5 item.", "error", 4000);
        return false;
    }
    var values= new Array();
    var indx=0;
    var cats = getInterestCats();
    if(cats==null)
    {
        cats=new Array();
    }
    if(interests)
    {
        for ( var i = 0; i < interests.length; i++) {
            if (interests[i] != null && interests[i]['id']!=null) {
                var res=true;
                for(var j = 0; j < cats.length; j++)
                {	
                    if('checkbox_on_off_'+interests[i]['cat_id']==''+cats[j]['id'])
                    {
                        if(cats[j]['cat_id']+''=='false')
                        {
                            res=false;
                        }
                    } 
                }
                if(res)
                {
                    values[indx]=interests[i];
                    indx++;
                }
            }
        }
    }
    btnClickPersonelLikes();
    
    document.getElementById(ELM_ADD_KEY).value = values.toJSON();
    sessionStorage.clear();
}

function saveIneterests(interests)
{
    sessionStorage.setItem(INTEREST_ADD_KEY+USERIDS_ADD_KEY, interests.toJSON());
}

function getInterests()
{
    try{
        var interests = sessionStorage.getItem(INTEREST_ADD_KEY+USERIDS_ADD_KEY);
        interests = JSON.parse(interests);
        return interests;
    }catch(exp){
        console.log(exp);
    }
    return null;
}

function saveIneterestCats(cats)
{
    sessionStorage.setItem(INTEREST_CAT_ADD_KEY+USERIDS_ADD_KEY, cats.toJSON());
}

function getInterestCats()
{
    var cats = sessionStorage.getItem(INTEREST_CAT_ADD_KEY+USERIDS_ADD_KEY);
    cats = JSON.parse(cats);
    return cats;
}


/*
 * Images
 */

function selectItem(tile){
    if(tile.getAttribute('status')==='true')
    {
        tile.setAttribute('status','false');
        jQuery(tile).css('border-color','white');
        removeItem(tile.getAttribute('int_id'),'0');
    }
    else{
        tile.setAttribute('status','true');
        jQuery(tile).css('border-color',isRed);
        var item=new Object();
        item.id=tile.getAttribute('int_id');
        item.label=tile.title;
        item.image='1';
        item.cat=tile.getAttribute('cat_id');
        if (findItemAdd(item)) {
            addItem(item);
        }
    }
    trackItemCountChange();
    return false;
};

function selectItemSpan(span,tile){
    jQuery(span).css({
        opacity: 0
    });
    if(tile.getAttribute('status')==='true')
    {
        tile.setAttribute('status','false');
        jQuery(tile).css('border-color','white');
        removeItem(tile.getAttribute('int_id'),'0');
    }
    else{
        tile.setAttribute('status','true');
        jQuery(tile).css('border-color',isRed);
        var item=new Object();
        item.id=tile.getAttribute('int_id');
        item.label=tile.title;
        item.image='1';
        item.cat=tile.getAttribute('cat_id');
        if (findItemAdd(item)) {
            addItem(item);
        }
    }
    trackItemCountChange();
    return false;
};


/*
 * Genel
 */

function addDefaultsStorage(list,user_id){
    if(user_id)
    {
        USERIDS_ADD_KEY=user_id+"";
    }
    try{
        if(typeof list == "string")  {
            list= jQuery.parseJSON(list);
        }
        for(var i=0;i<list.length;i++){
            addItem(list[i]);
        }
    }catch(exp){
        console.log(exp);
    }
}

function checkSessionStorage(user_id)
{
    if(user_id)
    {
        USERIDS_ADD_KEY=user_id+"";
    }
    var interests=getInterests();
    if (interests != null) {
        for ( var i = 0; i < interests.length; i++) {
            if (interests[i] != null && interests[i]['image'] == '1')
            {
                try {
                    var element=document.getElementById('i_interest_item_'+interests[i]['id']);
                    if(element)
                    {
                        element.setAttribute('status','true');
                        jQuery(element).css('border-color',isRed);
                    }
                } catch (e) {
                    console.log(e);
                }
            }else{
                try {
                    //if(interests[i]['new_']=='1')
                    //{
                    //	removeItem(interests[i]['id'], '1');
                    //}else{
                    var item=new Object();
                    item.item=interests[i];
                    insertItem('add_like_ul',item);
                //}
                } catch (e) {
                    console.log(e);
                }
            }
        }
    }
    var cats=getInterestCats();
    if(cats!=null)
    {
        for ( var i = 0; i < cats.length; i++) {
            if(cats[i] !=null && cats[i]['id'] !=null && cats[i]['val'] !=null)
            {
                var element=null;
                element=document.getElementById(cats[i]['id']);
                if(element)
                {
                    if(cats[i]['val']+'' =='true')
                    {
                        element.checked=true;
                    }else
                    {
                        element.removeAttribute("checked");
                    }
                    changeCheckBoxStatus(cats[i]['id']);
                }
            }
        }
    }
    trackItemCountChange();
}

function addNewLike(field)
{
    if(field)
    {
        var f=document.getElementById(field);
        if(f)
        {
            if(f.value && f.value.trim()!="" && f.getAttribute('placeholder')!=f.value.trim())
            {
                //var d = new Date();
                //var n = d.getMilliseconds();
                var item=new Object();
                item.item=new Object();
                item.item.id='new_'+f.value.trim().toLowerCase();
                item.item.label=f.value.trim();
                item.item.new_='1';
                insertItem('add_like_ul',item);
            }
        }
        f.value="";
    }
}


function changeCheckBoxStatus(checkbox)
{
    var item=new Object();
    var element=null;
    var cats=null;
    element=document.getElementById(checkbox);
    if(element)
    {
        item.id=checkbox;
        item.val=element.checked;
        cats=getInterestCats();
        if(cats!=null)
        {
            item2=findCategoryAddById(checkbox);
            if(item2)
            {
                cats[item2.indx]=item;
            }else
            {
                cats[cats.length]=item;
            }
        }
        else
        {
            cats=new Array();
            cats[0]=item;
        }
        saveIneterestCats(cats);
    }
    var body=document.getElementById('add_like_span_body_div_'+element.getAttribute('cat_id'));
    var span=document.getElementById('add_like_span_div_'+element.getAttribute('cat_id'));
    if(body && span)
    {
        if(element.checked)
        {
            jQuery(body).css("opacity", "1");
            span.className="add_ktg_sag add_like_span_div_enable";
        }else
        {
            jQuery(body).css("opacity", "0.3");
            span.className="add_ktg_sag add_like_span_div_disable";
        }
    }
}


function trackItemCountChange(){
    var interests = getInterests();
    var initLabel=jQuery("#add_like_count_0");
    var countLabelSpan=jQuery("#add_like_count_");
    var countLabel=jQuery("#add_like_count");
    var welldoneLabel=jQuery("#add_like_done");
    var countLabelS=jQuery("#add_like_count_s");
    if(interests && interests.length>0){
        if(interests.length<5){
            initLabel.hide();
            welldoneLabel.hide();
            countLabel.text(5-interests.length);
            countLabelSpan.show();
            if(interests.length==4){
                countLabelS.hide();
            }else{
                countLabelS.show();
            }
        }else{
            initLabel.hide();
            countLabelSpan.hide();
            countLabel.text(4);
            welldoneLabel.show();
        }
    }else{
        initLabel.show();
        countLabelSpan.hide();
        welldoneLabel.hide();
    }
}