<?php
/*
 * Dependencies
 */
require_once __DIR__.'/userFunctions.php';
require_once __DIR__.'/../config/dbconfig.php';



class DBUtils{

	public  static function getNextId($field)
	{
            if(!empty($field))
            {
                $field=  DBUtils::mysql_escape($field);
		$query = mysql_query("SELECT * FROM ".TBL_KEYGENERATOR." WHERE  PK_COLUMN = '".$field."'") or die(mysql_error());
		$result = mysql_fetch_array($query);
		if (empty($result)) {
			return DBUtils::setNextId($field);
		}else {
			$val= ($result['VALUE_COLUMN']+1);
			$sql="UPDATE  ".TBL_KEYGENERATOR." SET  VALUE_COLUMN=$val WHERE PK_COLUMN = '".$field."'";
			mysql_query($sql) or die(mysql_error());
			return $val;
		}
            } else
            {
                return -1;
            }
	}
        
        public static function  setNextId($field)
        {
            if(!empty($field))
            {
                $field=  DBUtils::mysql_escape($field);
                $id=rand(1000,10000000);
		mysql_query("INSERT INTO ".TBL_KEYGENERATOR." (PK_COLUMN,VALUE_COLUMN)  VALUES ('".$field."',".  $id.")") or die(mysql_error());
		return $id;
            } else
            {
                return -1;
            }
        }

        
	public static function getDate($datestr)
	{
		if(!empty($datestr))
		{
			$datestr=UtilFunctions::checkDate($datestr);
			return $datestr;
		}
		return "";
	}

	public static function get_uuid() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
				// 32 bits for "time_low"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

				// 16 bits for "time_mid"
				mt_rand( 0, 0xffff ),

				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 4
				mt_rand( 0, 0x0fff ) | 0x4000,

				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				mt_rand( 0, 0x3fff ) | 0x8000,

				// 48 bits for "node"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}


	public static function mysql_escape($str,$rtn=0)
	{
                if(!empty($str))
                {
                    $str=mysql_real_escape_string($str);
                }
		if($rtn=="1" && empty($str) && $str!="0")
		{
			return "null";
		}
		return $str;
	}
}
?>
