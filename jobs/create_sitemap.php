<?php

session_start();
session_write_close();
header("Content-type: text/xml;charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();

$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xml = $xml . "<urlset  xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"
		xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" 
		xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">";

/*
 * Static Pages
 */
$xml = $xml . "<url><loc>" . HOSTNAME . "</loc><priority>1.0000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_BUSINESS_CREATE . "</loc><priority>0.5000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_SIGNUP . "</loc><priority>0.5000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_LOGIN . "</loc><priority>0.5000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_LOGOUT . "</loc><priority>0.5000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_ABOUT_YOU . "?new" . "</loc><priority>0.5000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_FORGOT_PASSWORD . "</loc><priority>0.5000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_NEW_PASSWORD . "</loc><priority>0.5000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_CONFIRM . "</loc><priority>0.5000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_EDIT_EVENT . "</loc><priority>0.5000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_EVENT . "</loc><priority>0.5000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_USER . "</loc><priority>0.5000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_UPDATE_PROFILE . "</loc><priority>0.5000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_UPDATE_EVENT . "</loc><priority>0.5000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_ERROR_PAGE . "</loc><priority>0.5000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_FB_LOGIN . "</loc><priority>0.5000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_FQ_LOGIN . "</loc><priority>0.5000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_TW_LOGIN . "</loc><priority>0.5000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_GG_LOGIN . "</loc><priority>0.5000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_ABOUT_YOU . "</loc><priority>0.2000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . PAGE_WHO_TO_FOLLOW . "</loc><priority>0.2000</priority><changefreq>daily</changefreq></url>";


/*
 * Static Pages
 */

/*
 * Categories
 */
$cats_ids = array();
try {
    $cats_en = MenuUtils::getCategories(LANG_EN_US);
    if (!empty($cats_en) && is_array($cats_en)) {
        foreach ($cats_en as $cat) {
            if (!empty($cat) && !empty($cat->id) && !in_array($cat->id, $cats_ids)) {
                array_push($cats_ids, $cat->id);
            }
        }
    }
    unset($cats_en);
    unset($cat);
} catch (Exception $exc) {
    echo $exc->getTraceAsString();
}

try {
    $cats_tr = MenuUtils::getCategories(LANG_TR_TR);
    if (!empty($cats_tr) && is_array($cats_tr)) {
        foreach ($cats_tr as $cat) {
            if (!empty($cat) && !empty($cat->id) && !in_array($cat->id, $cats_ids)) {
                array_push($cats_ids, $cat->id);
            }
        }
    }
    unset($cats_tr);
    unset($cat);
} catch (Exception $exc) {
    echo $exc->getTraceAsString();
}

if (!empty($cats_ids) && is_array($cats_ids)) {
    foreach ($cats_ids as $catId) {
        if (!empty($catId))
            $xml = $xml . "<url><loc>" . HOSTNAME . "category/" . $catId . "</loc><priority>1.0000</priority><changefreq>daily</changefreq></url>";
    }
}
unset($cats_ids);
/*
 * Categories
 */

/*
 * foryou all today vs..
 */

$xml = $xml . "<url><loc>" . HOSTNAME . "foryou</loc><priority>1.0000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . HOSTNAME . "following</loc><priority>1.0000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . HOSTNAME . "all</loc><priority>1.0000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . HOSTNAME . "today</loc><priority>1.0000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . HOSTNAME . "tomorrow</loc><priority>1.0000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . HOSTNAME . "thisweekend</loc><priority>1.0000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . HOSTNAME . "next7days</loc><priority>1.0000</priority><changefreq>daily</changefreq></url>";
$xml = $xml . "<url><loc>" . HOSTNAME . "next30days</loc><priority>1.0000</priority><changefreq>daily</changefreq></url>";

/*
 * Users
 */
try {
    $users = UserUtils::getUserList(0, 100000);
    if (!empty($users) && is_array($users)) {
        foreach ($users as $user) {
            if (!empty($user) && !empty($user->userName))
                $xml = $xml . "<url><loc>" . PAGE_USER . $user->userName . "</loc><priority>1.0000</priority><changefreq>daily</changefreq></url>";
        }
    }
    unset($users);
    unset($user);
} catch (Exception $exc) {
    echo $exc->getTraceAsString();
}
/*
 * Users
 */


/*
 * Events
 */
try {
    $events = EventUtil::getAllEvents();
    if (!empty($events) && is_array($events)) {
        foreach ($events as $event) {
            if (!empty($event) && !empty($event->id))
                $xml = $xml . "<url><loc>" . PAGE_EVENT . $event->id . "</loc><priority>1.0000</priority><changefreq>daily</changefreq></url>";
        }
    }
    unset($events);
    unset($event);
} catch (Exception $exc) {
    echo $exc->getTraceAsString();
}
/*
 * Events
 */


$xml = $xml . "</urlset>";

echo $xml;

try {
    //$sitemap="C:\\sitemap.xml";
    $sitemap = "/var/www/timete/web/private/sitemap.xml";
    if (!file_exists($sitemap)) {
        $sitemapFile = fopen($sitemap, 'w');
        fclose($sitemapFile);
    }
    file_put_contents($sitemap, $xml);
} catch (Exception $exc) {
    echo $exc->getTraceAsString();
}
?>

