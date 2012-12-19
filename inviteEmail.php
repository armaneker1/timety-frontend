<?php 
require 'utils/userFunctions.php'; 
$result=new Result();
try {
	$query=$_POST["e"];
        $userId=$_POST["u"];
        if(!UserFuctions::check_email_address($query))
        {
            $result->success=false;
            $result->error="Invalid email";
        }else if(!empty ($userId))
        {
            $uf=new UserFuctions();
            $user=$uf->getUserById($userId);
            if(!empty($user))
            {
                if($user->email==$query)
                {
                    $result->success=false;
                    $result->error="You can't invite yourself";
                }else {
                    $result->success=false;
                    $res=UserFuctions::sendEmail($user->firstName." ".$user->lastName." wants you to join timety. please click <a href='http://fabelist.com/timete/web/createaccount.php'>here</a> ", "Timety invite",'{"email": "'.$query.'",  "name": "Hasan "}');
                    if($res[0]->status=="sent")
                    {
                            $result->success=true;
                    }
                    $result->param=$res;
                }
            }else
            {
                $result->success=false;
                $result->error="Invalid user";
            }
        }
} catch (Exception $e) {
	$result->success=false;
	$result->error=$e->getMessage();
}
$json_response = json_encode($result);
echo $json_response;
?>