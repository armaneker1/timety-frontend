<?php 
require_once __DIR__.'/utils/Functions.php';
$result=new Result();
try {
	$query=$_POST["e"];
        $userId=$_POST["u"];
        if(!UtilFunctions::check_email_address($query))
        {
            $result->success=false;
            $result->error="Invalid email";
        }else if(!empty ($userId))
        {
            $uf=new UserUtils();
            $user=$uf->getUserById($userId);
            if(!empty($user))
            {
                if($user->email==$query)
                {
                    $result->success=false;
                    $result->error="You can't invite yourself";
                }else {
                    $result->success=false;
                    $res=MailUtil::sendEmail($user->firstName." ".$user->lastName." wants you to join timety. please click <a href='".PAGE_SIGNUP."'>here</a> ", "Timety invite",'{"email": "'.$query.'",  "name": "'.$query.' "}');
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