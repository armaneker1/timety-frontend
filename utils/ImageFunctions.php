<?php
class ImageUtil{
    
        public static function getAllHeaderImageList($idListString)
        {
            if(!empty($idListString))
            {
                $query=mysql_query("SELECT * from ".TBL_IMAGES." WHERE header=1 AND id IN (".$idListString.")") or die(mysql_error());
                $array=array();
                if(!empty($query))
                {
                    $num= mysql_num_rows($query);
                    if($num>1)
                    {
                        while ($db_field = mysql_fetch_assoc($query) ) {
                            $image=new Image();
                            $image->createFromSQL($db_field);
                            array_push($array, $image);
                        }
                    } else if($num>0)
                    {
                        $db_field = mysql_fetch_assoc($query);
                        $image=new Image();
                        $image->createFromSQL($db_field);
                        array_push($array, $image);
                    }
                    return $array;
                }
            }
        }


        public static function  getImageListByEvent($eventId)
	{
		$eventId=DBUtils::mysql_escape($eventId);
		$query=mysql_query("SELECT * from ".TBL_IMAGES." WHERE eventId=$eventId") or die(mysql_error());
		$array=array();
		if(!empty($query))
		{
			$num= mysql_num_rows($query);
			if($num>1)
			{
				while ($db_field = mysql_fetch_assoc($query) ) {
					$image=new Image();
					$image->createFromSQL($db_field);
					array_push($array, $image);
				}
			} else if($num>0)
			{
				$db_field = mysql_fetch_assoc($query);
				$image=new Image();
				$image->createFromSQL($db_field);
				array_push($array, $image);
			}
			return $array;
		}
	}

	public static function  getImageById($imageId)
	{
		$imageId=DBUtils::mysql_escape($imageId);
		$query = mysql_query("SELECT * FROM ".TBL_IMAGES." WHERE id = $imageId") or die(mysql_error());
		$result = mysql_fetch_array($query);
		if (empty($result)) {
			return null;
		}else {
			$image=new Image();
			$image->createFromSQL($result);
			return $image;
		}
	}

	public static function insert(Image $image){
                $imageId=DBUtils::getNextId(CLM_IMAGEID);
		$query = mysql_query("INSERT INTO ".TBL_IMAGES." (id,url,header,eventId,width,height) VALUES (".$imageId.",'".DBUtils::mysql_escape($image->url)."',".DBUtils::mysql_escape($image->header).",".DBUtils::mysql_escape($image->eventId).",$image->width,$image->height)") or die(mysql_error());
		return ImageUtil::getImageById($imageId);
	}

	public static function delete($imageId){
		$imageId=DBUtils::mysql_escape($imageId);
		$query = mysql_query("DELETE FROM ".TBL_IMAGES." WHERE id = $imageId") or die(mysql_error());
	}
        
        public static function getSize($imagePath)
        {
            $array=array();
            array_push($array,186);
            if(!empty($imagePath))
            {
                $size=getimagesize($imagePath);
                $val=$size[1]*186;
                $height=floor($val/$size[0]);
                array_push($array, $height);
                return $array;
            }
            array_push($array,0);
            var_dump($array);
            return $array;
        }
           
}
?>
