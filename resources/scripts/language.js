function getLanguageText(key) {
    var result = "";
    if (lang_array && lang_array.hasOwnProperty(key)) {
        result = lang_array[key];
    }
    
    if (result && arguments && arguments.length>1) {
        for(var i=1;i<arguments.length;i++){
            result = result.replace("{" + (i-1) + "}", arguments[i]);
        }
    }
    return result;
}