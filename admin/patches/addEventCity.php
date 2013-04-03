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
HttpAuthUtils::checkHttpAuth();

$events = EventUtil::getAllEvents();

foreach ($events as $event) {
    if (!empty($event)) {
        $id = $event->id;
        $city = strtolower($event->location);
        if (!empty($city)) {
            $city = "++++++++" . $city;
            $c_id = null;
            if (strrpos($city, 'ankara')) {
                $c_id = LocationUtils::getCityId('ankara');
            }

            if (strrpos($city, 'antalya')) {
                $c_id = LocationUtils::getCityId('antalya');
            }

            if (strrpos($city, 'akhisar')) {
                $c_id = LocationUtils::getCityId('akhisar');
            }

            if (strrpos($city, 'kadıköy')) {
                $c_id = LocationUtils::getCityId('istanbul');
            }

            if (strrpos($city, 'istanbul')) {
                $c_id = LocationUtils::getCityId('istanbul');
            }

            if (strrpos($city, 'İstanbul')) {
                $c_id = LocationUtils::getCityId('istanbul');
            }

            if (strrpos($city, 'austin')) {
                $c_id = LocationUtils::getCityId('austin');
            }

            if (strrpos($city, 'cevahir')) {
                $c_id = LocationUtils::getCityId('istanbul');
            }

            if (strrpos($city, 'kadıkoy')) {
                $c_id = LocationUtils::getCityId('istanbul');
            }

            if (strrpos($city, 'zeytinburnu')) {
                $c_id = LocationUtils::getCityId('istanbul');
            }

            if (strrpos($city, 'bostancı')) {
                $c_id = LocationUtils::getCityId('istanbul');
            }

            if (strrpos($city, 'bostanci')) {
                $c_id = LocationUtils::getCityId('istanbul');
            }

            if (strrpos($city, 'bursa')) {
                $c_id = LocationUtils::getCityId('bursa');
            }

            if (strrpos($city, 'elazig')) {
                $c_id = LocationUtils::getCityId('elazig');
            }

            if (strrpos($city, 'elaziğ')) {
                $c_id = LocationUtils::getCityId('elazig');
            }

            if (strrpos($city, 'eskişehir')) {
                $c_id = LocationUtils::getCityId('eskişehir');
            }

            if (strrpos($city, 'eskisehir')) {
                $c_id = LocationUtils::getCityId('eskişehir');
            }

            if (strrpos($city, 'gaziantep')) {
                $c_id = LocationUtils::getCityId('gaziantep');
            }

            if (strrpos($city, 'kayseri')) {
                $c_id = LocationUtils::getCityId('kayseri');
            }

            if (strrpos($city, 'mersin')) {
                $c_id = LocationUtils::getCityId('mersin');
            }

            if (strrpos($city, 'ordu')) {
                $c_id = LocationUtils::getCityId('ordu');
            }

            if (strrpos($city, 'sivas')) {
                $c_id = LocationUtils::getCityId('sivas');
            }

            if (strrpos($city, 'trabzon')) {
                $c_id = LocationUtils::getCityId('trabzon');
            }

            if (strrpos($city, 'bochum')) {
                $c_id = LocationUtils::getCityId('bochum');
            }

            if (strrpos($city, 'cannes')) {
                $c_id = LocationUtils::getCityId('cannes');
            }

            if (strrpos($city, 'cannes')) {
                $c_id = LocationUtils::getCityId('cannes');
            }

            if (strrpos($city, 'küçükÇiftlik park')) {
                $c_id = LocationUtils::getCityId('istanbul');
            }

            if (strrpos($city, 'london')) {
                $c_id = LocationUtils::getCityId('london');
            }

            if (strrpos($city, 'new york')) {
                $c_id = LocationUtils::getCityId('newyork');
            }

            if (strrpos($city, 'chicago')) {
                $c_id = LocationUtils::getCityId('chicago');
            }

            if (strrpos($city, 'melbourne')) {
                $c_id = LocationUtils::getCityId('melbourne');
            }

            if (strrpos($city, 'shanghai')) {
                $c_id = LocationUtils::getCityId('shanghai');
            }

            if (strrpos($city, 'kuala lumpur')) {
                $c_id = LocationUtils::getCityId('kualalumpur');
            }

            if (strrpos($city, 'manama')) {
                $c_id = LocationUtils::getCityId('manama');
            }

            if (strrpos($city, 'barcelona')) {
                $c_id = LocationUtils::getCityId('barcelona');
            }

            if (strrpos($city, 'monaco')) {
                $c_id = LocationUtils::getCityId('monaco');
            }

            if (strrpos($city, 'montreal')) {
                $c_id = LocationUtils::getCityId('montreal');
            }

            if (strrpos($city, 'northampton')) {
                $c_id = LocationUtils::getCityId('northampton');
            }

            if (strrpos($city, 'beşiktaş')) {
                $c_id = LocationUtils::getCityId('istanbul');
            }

            if (strrpos($city, 'karabük')) {
                $c_id = LocationUtils::getCityId('karabük');
            }

            if (strrpos($city, 'sarıyer')) {
                $c_id = LocationUtils::getCityId('istanbul');
            }

            if (strrpos($city, 'esenyurt')) {
                $c_id = LocationUtils::getCityId('istanbul');
            }

            if (strrpos($city, 'san francisco')) {
                $c_id = LocationUtils::getCityId('sanfrancisco');
            }

            if (strrpos($city, 'hanover')) {
                $c_id = LocationUtils::getCityId('hanover');
            }

            if (strrpos($city, 'berlin')) {
                $c_id = LocationUtils::getCityId('berlin');
            }

            if (strrpos($city, 'manchester')) {
                $c_id = LocationUtils::getCityId('manchester');
            }

            if (strrpos($city, 'beirut')) {
                $c_id = LocationUtils::getCityId('beirut');
            }

            if (strrpos($city, 'sofia')) {
                $c_id = LocationUtils::getCityId('sofia');
            }

            if (strrpos($city, 'kraków')) {
                $c_id = LocationUtils::getCityId('kraków');
            }

            if (strrpos($city, 'tokholm')) {
                $c_id = LocationUtils::getCityId('tokholm');
            }

            if (strrpos($city, 'amsterdam')) {
                $c_id = LocationUtils::getCityId('amsterdam');
            }

            if (strrpos($city, 'bilbao')) {
                $c_id = LocationUtils::getCityId('bilbao');
            }

            if (strrpos($city, 'las vegas')) {
                $c_id = LocationUtils::getCityId('lasvegas');
            }

            if (strrpos($city, 'gateshead')) {
                $c_id = LocationUtils::getCityId('gateshead');
            }

            if (strrpos($city, 'belfast')) {
                $c_id = LocationUtils::getCityId('belfast');
            }

            if (strrpos($city, 'dublin')) {
                $c_id = LocationUtils::getCityId('dublin');
            }

            if (strrpos($city, 'western cape')) {
                $c_id = LocationUtils::getCityId('westerncape');
            }

            if (strrpos($city, 'vilnius')) {
                $c_id = LocationUtils::getCityId('vilnius');
            }

            if (strrpos($city, 'tallinn')) {
                $c_id = LocationUtils::getCityId('tallinn');
            }

            if (strrpos($city, 'worldwide')) {
                EventUtil::updateWorldWide($id, 1);
            }

            if (!empty($c_id)) {
                EventUtil::updateLocation($id, "TR", $c_id);
            } else {
                echo $city . "<p/>";

                EventUtil::updateLocation($id, "", "");
            }
        }
    }
}
?>
