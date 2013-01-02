<?php

class LostPassUtil{

	public static function getLostPassByGUID($guid)
	{
		$guid=DBUtils::mysql_escape($guid);
		$query = mysql_query("SELECT * FROM ".TBL_LOSTPASS." WHERE guid = '$guid'") or die(mysql_error());
		$result = mysql_fetch_array($query);
		if (empty($result)) {
			return null;
		}else {
			$lss=new LostPass();
			$lss->createFromSQL($result);
			return $lss;
		}
	}

	public static function getLostPassById($id)
	{
		$id=DBUtils::mysql_escape($id,1);
		$query = mysql_query("SELECT * FROM ".TBL_LOSTPASS." WHERE id = $id") or die(mysql_error());
		$result = mysql_fetch_array($query);
		if (empty($result)) {
			return null;
		}else {
			$lss=new LostPass();
			$lss->createFromSQL($result);
			return $lss;
		}
	}

	public static function getLostPass($id, $userId, $guid)
	{
		$id=DBUtils::mysql_escape($id,1);
		$userId=DBUtils::mysql_escape($userId,1);
		$guid=DBUtils::mysql_escape($guid);
		$query = mysql_query("SELECT * FROM ".TBL_LOSTPASS." WHERE id = $id and guid='$guid' and user_id=$userId and valid=1") or die(mysql_error());
		$result = mysql_fetch_array($query);
		if (empty($result)) {
			return null;
		}else {
			$lss=new LostPass();
			$lss->createFromSQL($result);
			return $lss;
		}
	}

	public static function insert(LostPass $lss){
		$query = mysql_query("INSERT INTO ".TBL_LOSTPASS." (user_id,guid,date,valid) VALUES (".DBUtils::mysql_escape($lss->userId,1).",'".DBUtils::mysql_escape($lss->guid)."','".DBUtils::mysql_escape($lss->date,1)."',".DBUtils::mysql_escape($lss->valid,1).")") or die(mysql_error());
		return LostPassUtil::getLostPassByGUID($lss->guid);
	}

	public static function invalidate($lssId){
		$lssId=DBUtils::mysql_escape($lssId,1);
		$sql="UPDATE ".TBL_LOSTPASS." SET valid=0 WHERE id=$lssId";
		$query = mysql_query($sql) or die(mysql_error());
		return LostPassUtil::getLostPassById($lssId)->valid;
	}

}

?>
