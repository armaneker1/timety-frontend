<?php

class LocationUtils {

    public static function getCityCounrty($lat, $lng) {
        if (!empty($lat) && !empty($lng)) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => 'http://maps.googleapis.com/maps/api/geocode/json?bounds=' . $lat . ',' . $lng . '|' . $lat . ',' . $lng . '&sensor=false',
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.4; en-US; rv:1.9.2.2) Gecko/20100316 Firefox/3.6.2'
            ));
            $resp = curl_exec($curl);
            print_r($resp);
            curl_close($curl);
        }
        return null;
    }

    public static function getCityId($city) {
        if (!empty($city)) {
            $id = null;
            $city = strtolower($city);
            $city = str_replace(' ', '', $city);
            $SQL = "SELECT * FROM " . TBL_CITY_MAP . " WHERE city_name ='" . $city . "'";
            $query = mysql_query($SQL) or die(mysql_error());
            $result = mysql_fetch_array($query);
            if (!empty($result)) {
                $id = $result['city_id'];
            }

            if (empty($id)) {
                $c_id = DBUtils::getNextId(CLM_CITY_ID);
                $SQL = "INSERT INTO " . TBL_CITY_MAP . " (city_name,city_id) VALUES ('$city',$c_id)";
                mysql_query($SQL) or die(mysql_error());
                $id = $c_id;
            }

            return $id;
        }
        return null;
    }

}

?>
