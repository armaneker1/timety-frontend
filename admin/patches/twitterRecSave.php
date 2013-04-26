<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

session_start();
header("charset=utf8");

require_once __DIR__ . '/../../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

//Technology, Entrepreneurial, Startup
$tagId="96,147,136";
$array = array();
array_push($array, '657863');
array_push($array, '816653');
array_push($array, '20536157');
array_push($array, '972651');
array_push($array, '20');
array_push($array, '3829151');
array_push($array, '2384071');
array_push($array, '10350');
array_push($array, '13348');
array_push($array, '1000591');
array_push($array, '1344951');
array_push($array, '37570179');
array_push($array, '586');
array_push($array, '652193');
array_push($array, '5746452');
array_push($array, '36823');
array_push($array, '2172');
array_push($array, '862681');
array_push($array, '3080761');
array_push($array, '930061');
array_push($array, '1586501');
array_push($array, '11740902');
array_push($array, '3475');
array_push($array, '4641021');
array_push($array, '8453452');
array_push($array, '14372486');
array_push($array, '14413075');
array_push($array, '14600116');
array_push($array, '7846');
array_push($array, '18327902');
array_push($array, '717313');
array_push($array, '774096');
array_push($array, '817268');
array_push($array, '11666142');
array_push($array, '14372143');
array_push($array, '816214');
array_push($array, '1422311');
array_push($array, '14413715');
array_push($array, '12798452');
array_push($array, '79543');
array_push($array, '2735591');
array_push($array, '814253');
array_push($array, '1242511');
array_push($array, '734493');
array_push($array, '15934340');
array_push($array, '819999');
array_push($array, '1260231');
array_push($array, '3713811');
array_push($array, '15076390');
array_push($array, '46063');
array_push($array, '49793');
array_push($array, '611823');
array_push($array, '14885549');
array_push($array, '63842549');
array_push($array, '14789629');
array_push($array, '494518813');
array_push($array, '63578554');
array_push($array, '50393960');
array_push($array, '50090898');
array_push($array, '125485258');
array_push($array, '56505125');
array_push($array, '136293558');
array_push($array, '93711247');

foreach ($array as $value) {
    $tw =new TimeteTwitterRecommendation();
    $tw->setTagId($tagId);
    $tw->setTwId($value);
    $tw->insertIntoDatabase(DBUtils::getConnection());
}

//Soccer
$tagId="53,66,41,1017";
$array = array();
array_push($array,'155659213');
array_push($array,'36623013');
array_push($array,'60865434');
array_push($array,'51597791');
array_push($array,'140070953');
array_push($array,'38839266');
array_push($array,'10678292');
array_push($array,'19583545');
array_push($array,'16313045');
array_push($array,'1242346956');
array_push($array,'22236163');
array_push($array,'54626493');
array_push($array,'8159472');
array_push($array,'60776593');
array_push($array,'76968739');
array_push($array,'20044348');
array_push($array,'845422447');
array_push($array,'64884344');
array_push($array,'17744542');
array_push($array,'28153657');
array_push($array,'15356900');
array_push($array,'18362522');
array_push($array,'16902662');
array_push($array,'9499692');
array_push($array,'46086231');
array_push($array,'17224076');
array_push($array,'19273651');
array_push($array,'19393128');
array_push($array,'16899933');
array_push($array,'3141101');
array_push($array,'452155423');
array_push($array,'96951800');
array_push($array,'773069256');
array_push($array,'165699676');
array_push($array,'252753618');
array_push($array,'22910295');
array_push($array,'186386857');
array_push($array,'60865434');

foreach ($array as $value) {
    $tw =new TimeteTwitterRecommendation();
    $tw->setTagId($tagId);
    $tw->setTwId($value);
    $tw->insertIntoDatabase(DBUtils::getConnection());
}


//Business
$tagId="73";//?
$array = array();
array_push($array,'14886375');
array_push($array,'34713362');
array_push($array,'1652541');
array_push($array,'15935591');
array_push($array,'16184358');
array_push($array,'16827489');
array_push($array,'65466158');
array_push($array,'15568127');
array_push($array,'18992010');
array_push($array,'1797991');
array_push($array,'15485461');
array_push($array,'25709609');
array_push($array,'21312378');
array_push($array,'18904582');
array_push($array,'16397306');
array_push($array,'13565472');
array_push($array,'63353912');
array_push($array,'16400258');
array_push($array,'20888115');
array_push($array,'199640036');
array_push($array,'14924128');
array_push($array,'19049987');
array_push($array,'14250809');
array_push($array,'16429977');
array_push($array,'1118521');
array_push($array,'20186953');
array_push($array,'15281391');
array_push($array,'33370627');
array_push($array,'111441302');
array_push($array,'141783486');
array_push($array,'13073622');
array_push($array,'19997064');
array_push($array,'15897179');
array_push($array,'7985672');
array_push($array,'14078468');
array_push($array,'14935349');
array_push($array,'15101387');
array_push($array,'320524842');
array_push($array,'21391703');
array_push($array,'12884212');
array_push($array,'24512841');
array_push($array,'16373878');
array_push($array,'17221422');
array_push($array,'3108351');
array_push($array,'14800270');
array_push($array,'2735591');
array_push($array,'16896485');
array_push($array,'5120691');
array_push($array,'5768872');
array_push($array,'19407053');
array_push($array,'17522884');
array_push($array,'28140646');
array_push($array,'15308469');
array_push($array,'7712452');
array_push($array,'104237736');


foreach ($array as $value) {
    $tw =new TimeteTwitterRecommendation();
    $tw->setTagId($tagId);
    $tw->setTwId($value);
    $tw->insertIntoDatabase(DBUtils::getConnection());
}




//Music, Pop, Concert, festival
$tagId="31,33,125,109";//?
$array=array();

array_push($array,'24019308');
array_push($array,'79797834');
array_push($array,'821193');
array_push($array,'14497313');
array_push($array,'15291335');
array_push($array,'17446621');
array_push($array,'250831586');
array_push($array,'15687962');
array_push($array,'21904217');
array_push($array,'65289126');
array_push($array,'3291841');
array_push($array,'20727819');
array_push($array,'36947388');
array_push($array,'17992258');
array_push($array,'17852343');
array_push($array,'14780915');
array_push($array,'3646911');
array_push($array,'23560015');
array_push($array,'796742');
array_push($array,'18262639');
array_push($array,'15077962');
array_push($array,'26265033');
array_push($array,'16586811');
array_push($array,'14892220');
array_push($array,'19078594');
array_push($array,'7624112');
array_push($array,'16430887');
array_push($array,'15358891');
array_push($array,'1270041');
array_push($array,'15224719');
array_push($array,'14198241');
array_push($array,'62454048');
array_push($array,'31943671');
array_push($array,'18279333');
array_push($array,'22821175');
array_push($array,'24254832');
array_push($array,'15977633');
array_push($array,'16313225');
array_push($array,'12219272');
array_push($array,'14803953');
array_push($array,'16000318');
array_push($array,'21217034');
array_push($array,'12650332');
array_push($array,'18667907');
array_push($array,'93479887');


foreach ($array as $value) {
    $tw =new TimeteTwitterRecommendation();
    $tw->setTagId($tagId);
    $tw->setTwId($value);
    $tw->insertIntoDatabase(DBUtils::getConnection());
}




//Travel, Festival, Photography
$tagId="105,109,115";//?
$array=array();

array_push($array,'15066760');
array_push($array,'16365636');
array_push($array,'12397052');
array_push($array,'16211434');
array_push($array,'6449282');
array_push($array,'20414217');
array_push($array,'17219108');
array_push($array,'17400407');
array_push($array,'7212562');
array_push($array,'14363353');
array_push($array,'17173775');
array_push($array,'19491657');
array_push($array,'770759');
array_push($array,'19110632');
array_push($array,'19194198');
array_push($array,'16031536');
array_push($array,'9676212');
array_push($array,'16317694');
array_push($array,'15811314');
array_push($array,'23096405');
array_push($array,'14287191');
array_push($array,'10933662');
array_push($array,'14237001');
array_push($array,'14076231');
array_push($array,'15652066');
array_push($array,'17365848');
array_push($array,'7350962');
array_push($array,'19195914');
array_push($array,'16126957');
array_push($array,'19789299');
array_push($array,'14839646');
array_push($array,'1930311');
array_push($array,'14532620');
array_push($array,'10667662');
array_push($array,'15428715');
array_push($array,'20777337');
array_push($array,'16589148');
array_push($array,'32201563');
array_push($array,'18819574');
array_push($array,'16666847');
array_push($array,'18958468');
array_push($array,'26066384');
array_push($array,'28535982');
array_push($array,'16771854');
array_push($array,'17957392');

foreach ($array as $value) {
    $tw =new TimeteTwitterRecommendation();
    $tw->setTagId($tagId);
    $tw->setTwId($value);
    $tw->insertIntoDatabase(DBUtils::getConnection());
}
?>
