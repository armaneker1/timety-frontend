<?php

class LocationUtils {

    public static function getCityCountry($lat, $lng) {
        $res = LocationUtils::getCityCounrtyResponse($lat, $lng);
        $loc_country = null;
        $loc_city = null;
        $city_type = 0;
        if (!empty($res) && $res->status == "OK") {
            $results = $res->results;
            if (!empty($results) && is_array($results) && sizeof($results) > 0) {
                foreach ($results as $result) {
                    $address_components = $result->address_components;
                    if (!empty($address_components) && is_array($address_components) && sizeof($address_components) > 0) {
                        foreach ($address_components as $address_component) {
                            $types = $address_component->types;
                            if (!empty($types) && is_array($types) && sizeof($types) > 0) {
                                if (in_array("country", $types)) {
                                    $loc_country = $address_component->short_name;
                                    break;
                                } else if (in_array("city", $types) && $city_type < 4) {
                                    $city_type = 4;
                                    $loc_city = $address_component->long_name;
                                    break;
                                } else if (in_array("administrative_area_level_1", $types) && $city_type < 3) {
                                    $loc_city = $address_component->long_name;
                                    $city_type = 3;
                                } else if (in_array("administrative_area_level_2", $types) && $city_type < 2) {
                                    $loc_city = $address_component->long_name;
                                    $city_type = 2;
                                } else if (in_array("political", $types) && in_array("locality", $types) && $city_type < 1) {
                                    $loc_city = $address_component->long_name;
                                    $city_type = 1;
                                }
                            }
                        }
                    }

                    if (!empty($loc_city) && !empty($loc_country)) {
                        break;
                    }
                }
            }
        }
        return array(0 => $loc_country, 1 => $loc_city, "country" => $loc_country, "city" => $loc_city);
    }

    public static function getCityCounrtyResponse($lat, $lng) {
        if (!empty($lat) && !empty($lng)) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => 'http://maps.googleapis.com/maps/api/geocode/json?bounds=' . $lat . ',' . $lng . '|' . $lat . ',' . $lng . '&sensor=false',
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.4; en-US; rv:1.9.2.2) Gecko/20100316 Firefox/3.6.2'
            ));
            $resp = curl_exec($curl);
            curl_close($curl);
            return json_decode($resp);
        }
        return null;
    }

    public static function getCityName($id) {
        if (!empty($id)) {
            $SQL = "SELECT * FROM " . TBL_CITY_MAP . " WHERE city_id =" . $id;
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            if (!empty($result)) {
                $name = $result['city_name'];
                return $name;
            }
        }
        return "";
    }

    public static function getCityMaps() {
        $array = array();
        $SQL = "SELECT * FROM " . TBL_CITY_MAP;
        $result = mysql_query($SQL);
        if (!empty($result)) {
            $num = mysql_num_rows($result);
            if ($num > 1) {
                while ($db_field = mysql_fetch_assoc($result)) {
                    $obj = new stdClass();
                    $obj->id = $db_field['city_id'];
                    $obj->name = $db_field['city_name'];
                    array_push($array, $obj);
                }
            } else if ($num > 0) {
                $db_field = mysql_fetch_assoc($result);
                $obj = new stdClass();
                $obj->id = $db_field['city_id'];
                $obj->name = $db_field['city_id'];
                array_push($array, $obj);
            }
        }
        return $array;
    }

    public static function getCityIdNotAdd($city) {
        if (!empty($city)) {
            $id = null;
            $city = strtolower($city);
            $city = str_replace(' ', '', $city);
            if (preg_match('/^[0-9]+$/', $city)) {

                $SQL = "SELECT * FROM " . TBL_CITY_MAP . " WHERE city_id =" . $city;
                $query = mysql_query($SQL);
                $result = mysql_fetch_array($query);
                if (!empty($result)) {
                    $id = $result['city_id'];
                    return $id;
                }
            }

            $SQL = "SELECT * FROM " . TBL_CITY_MAP . " WHERE city_name ='" . $city . "'";
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            if (!empty($result)) {
                $id = $result['city_id'];
            }
            return $id;
        }
        return null;
    }

    public static function getCityId($city) {
        if (!empty($city)) {
            $id = null;
            $city = strtolower($city);
            $city = str_replace(' ', '', $city);

            if (preg_match('/^[0-9]+$/', $city)) {

                $SQL = "SELECT * FROM " . TBL_CITY_MAP . " WHERE city_id =" . $city;
                $query = mysql_query($SQL);
                $result = mysql_fetch_array($query);
                if (!empty($result)) {
                    $id = $result['city_id'];
                    return $id;
                }
            }

            $SQL = "SELECT * FROM " . TBL_CITY_MAP . " WHERE city_name ='" . $city . "'";
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            if (!empty($result)) {
                $id = $result['city_id'];
            }

            if (empty($id)) {
                $c_id = DBUtils::getNextId(CLM_CITY_ID);
                $SQL = "INSERT INTO " . TBL_CITY_MAP . " (city_name,city_id) VALUES ('$city',$c_id)";
                mysql_query($SQL);
                $id = $c_id;
            }
            return $id;
        }
        return null;
    }

    public static function getGeoLocationFromIP() {
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
            try {
                $data = file_get_contents(LOCATION_HOSTIP_API . $ip);
                $data = json_decode($data);
                $array = array();
                $array[0] = $data->latitude;
                $array[1] = $data->longitude;
                $array['latitude'] = $data->latitude;
                $array['longitude'] = $data->longitude;
                return $array;
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }
        }
        return null;
    }

}

?>
