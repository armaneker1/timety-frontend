<?php

class LanguageUtils {

    public static function getBrowserLanguage() {
        $lang = "";
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        switch ($lang) {
            case "tr":
                $lang = LANG_TR_TR;
                break;
            case "en":
                $lang = LANG_EN_US;
                break;
            default:
                $lang = LANG_EN_US;
                break;
        }
        return $lang;
    }

    public static function setLocale($locale = null) {
        if (empty($locale) && isset($_SESSION["SITE_LANG"])) {
            $locale = $_SESSION["SITE_LANG"];
        }
        if (empty($locale)) {
            $locale = self::getBrowserLanguage();
        }
        $locale = strtolower($locale);
        if ($locale != strtolower(LANG_EN_US) && $locale != strtolower(LANG_TR_TR)) {
            $locale = strtolower(LANG_EN_US);
        }
        $_SESSION["SITE_LANG"] = $locale;
        $lang_file = __DIR__ . '/../language/lang.' . $locale . '.php';
        if (file_exists($lang_file)) {
            require_once $lang_file;
        } else {
            error_log("Language File not found " . $lang_file);
        }
        //set php locale
        if ($locale == strtolower(LANG_EN_US)) {
            setlocale(LC_TIME, LANG_EN_US . ".UTF-8");
        } else if ($locale == strtolower(LANG_TR_TR)) {
            setlocale(LC_TIME, LANG_TR_TR . ".UTF-8");
        }
        return;
    }

    public static function setUserLocale($user = null) {
        $locale = null;
        if (!empty($user)) {
            $locale = $user->language;
        }
        self::setLocale($locale);
    }

    public static function setLocaleJS($locale = null) {
        if (empty($locale)) {
            $locale = self::getBrowserLanguage();
        }
        $locale = strtolower($locale);
        if ($locale != strtolower(LANG_EN_US) && $locale != strtolower(LANG_TR_TR)) {
            $locale = strtolower(LANG_EN_US);
        }
        echo "<script language='javascript' src='" . HOSTNAME . "language/lang." . $locale . ".js?" . JS_CONSTANT_PARAM . "'></script>";
    }

    public static function setUserLocaleJS($user = null) {
        $locale = null;
        if (!empty($user)) {
            $locale = $user->language;
        }
        self::setLocaleJS($locale);
    }

    public static function setAJAXLocale() {
        $locale = null;
        if (isset($_SESSION["SITE_LANG"])) {
            $locale = $_SESSION["SITE_LANG"];
        }
        self::setLocale($locale);
    }

    public static function getText($key, $args = null) {
        $result = "";
        if (defined($key)) {
            $result = constant($key);
        }
        $args = func_get_args();
        if (!empty($args) && sizeof($args) > 1 && !empty($result)) {
            for ($i = 1; $i < sizeof($args); $i++) {
                $result = str_replace("{" . ($i - 1) . "}", $args[$i], $result);
            }
        }
        return $result;
    }

    public static function getLocale() {
        $locale = strtolower(LANG_EN_US);
        if (isset($_SESSION["SITE_LANG"])) {
            $locale = $_SESSION["SITE_LANG"];
        }
        return $locale;
    }

    public static function str_split_unicode($str, $l = 0) {
        if ($l > 0) {
            $ret = array();
            $len = mb_strlen($str, "UTF-8");
            for ($i = 0; $i < $len; $i += $l) {
                $ret[] = mb_substr($str, $i, $l, "UTF-8");
            }
            return $ret;
        }
        return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    }

    public static function uppercase($str) {
        if (self::getLocale() == strtolower(LANG_EN_US)) {
            $str = strtoupper($str);
        } else if (self::getLocale() == strtolower(LANG_TR_TR)) {
            $tr_low_letters = self::str_split_unicode("abcçdefgğhıijklmnoöpqrsştuüvwxyz");
            $tr_up_letters = self::str_split_unicode("ABCÇDEFGĞHIİJKLMNOÖPQRSŞTUÜVWXYZ");
            for ($i = 0; $i < sizeof($tr_low_letters); $i++) {
                $str = str_replace($tr_low_letters[$i], $tr_up_letters[$i], $str);
            }
        }
        return $str;
    }

    public static function lowercase($str) {
        if (self::getLocale() == strtolower(LANG_EN_US)) {
            $str = strtolower($str);
        } else if (self::getLocale() == strtolower(LANG_TR_TR)) {
            $tr_low_letters = self::str_split_unicode("abcçdefgğhıijklmnoöpqrsştuüvwxyz");
            $tr_up_letters = self::str_split_unicode("ABCÇDEFGĞHIİJKLMNOÖPQRSŞTUÜVWXYZ");
            for ($i = 0; $i < sizeof($tr_up_letters); $i++) {
                $str = str_replace($tr_up_letters[$i], $tr_low_letters[$i], $str);
            }
        }
        return $str;
    }

}

?>
