<?php
require 'utils/userFunctions.php';
session_start(); 


$msgs=array();
$email=null;

$color="red";
$msg="Something wrong ";

if (array_key_exists("guid", $_GET)) {
        $guid="";
	if(isset($_GET["guid"]))
	{
		$guid=$_GET["guid"];
	}
        $guid=base64_decode($_GET["guid"]);
        if(!empty($guid))
        {
            $array= explode(";", $guid);
            $userId=$array[0];
            $userName=$array[1];
            if(!empty($userId) && !empty($userName))
            {
                $uf=new UserFuctions();
                $user=$uf->getUserById($userId);
                if(!empty($user) && $user->userName==$userName)
                {
                    UserFuctions::confirmUser($userId);
                    $color="green";
                    $msg="Confirmation is completed";
                }
                else
                {
                     $msg="User doesn't exist ";
                }
            }else
            {
                $msg="User doesn't exist ";
            }
        }else
        {
            $msg="Parameters wrong ";
        }
}

header("Content-Type: text/html; charset=utf8");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include('layout/layout_header.php'); ?>
<script type="text/javascript" src="resources/scripts/validate.js"></script>
<script type="text/javascript">
    setTimeout(function() {window.location="<?=HOSTNAME?>index.php";}, 2000);
</script>
<title>Timete Confirm</title>
</head>

<body class="bg">
	<?php include('layout/layout_top.php'); ?>
    <div id="personel_info_h" style="width: 450px !important;">
		<div class="personel_info" style="max-height: 150px;">
		<div class="ts_box" style="font-size: 12px; margin-left: 0px;">
                    <div class="ts_box" style="font-size: 12px;">
			<span style="color: <?=$color?>; display: block;" id="msg">
                            <?=$msg?> you will redirect to main page. click <a href="<?=HOSTNAME?>index.php">here</a>
                        </span>
                    </div>
		</div>
	</div>
</body>
</html>
