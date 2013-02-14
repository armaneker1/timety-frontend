function setTempMapLocation(result,status,res){
    if(status=="OK" && ce_loc==null) {
        ce_loc=res.geometry.location;
    }else{
        console.log(result);
    }
}

