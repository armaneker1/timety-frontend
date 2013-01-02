<?php

class DBUtils{

	public  static function getNextId($field)
	{
		$query = mysql_query("SELECT * FROM ".TBL_KEYGENERATOR." WHERE  PK_COLUMN = '".$field."'") or die(mysql_error());
		$result = mysql_fetch_array($query);
		if (empty($result)) {
			return null;
		}else {
			$val= ($result['VALUE_COLUMN']+1);
			$sql="UPDATE  ".TBL_KEYGENERATOR." SET  VALUE_COLUMN=$val WHERE PK_COLUMN = '".$field."'";
			mysql_query($sql) or die(mysql_error());
			return $val;
		}
	}

	
	public static function getDate($datestr)
	{
		if(!empty($datestr))
		{
			$datestr=UserFuctions::checkDate($datestr);
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
