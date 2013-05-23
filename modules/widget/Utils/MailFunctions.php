<?php

/*
 * Dependencies
 */
require_once __DIR__ . '/../apis/Mail/ses.php';

class MailFunctions {

   public static function sendSESMailFromFile($fileName, $params, $to, $subject) {
        $file = EMAIL_TEMPLATE_FOLDER . $fileName;
        if (file_exists($file)) {
            $template = file_get_contents($file);
            if (!empty($params) && sizeof($params) > 0) {
                foreach ($params as $par) {
                    if (!empty($par) && sizeof($par) == 2 && !empty($par[0])) {
                        $template = str_replace("\${" . $par[0] . "}", $par[1], $template);
                    }
                }
            }
            $tos = array();
            if (!empty($to)) {
                $array = explode(";", $to);
                if (!empty($array) && sizeof($array) > 0) {
                    foreach ($array as $mail) {
                        if (!empty($mail)) {
                            array_push($tos, $mail);
                        } else {
                            error_log($mail . " not valid");
                        }
                    }
                }
            }

            if (!empty($tos) && sizeof($tos) > 0) {
                $ses = new SimpleEmailService(AWS_SES_API_KEY, AWS_SES_API_SECRET);
                $ses->enableVerifyPeer(false);
                $m = new SimpleEmailServiceMessage();
                $m->setSubjectCharset("UTF-8");
                $m->setMessageCharset("UTF-8");
                foreach ($tos as $mail) {
                    $m->addTo($mail);
                }
                $m->setFrom(AWS_SES_API_FROM);
                $m->setSubject($subject);
                $m->setMessageFromString(null,$template);
                return $ses->sendEmail($m);
            } else {
                throw new Exception(LanguageUtils::getText("LANG_UTILS_MAIL_ERROR_EMAIL_EMPTY"));
            }
        } else {
            throw new Exception(LanguageUtils::getText("LANG_UTILS_MAIL_ERROR_FILE_NOT_FOUND"));
        }
        return false;
    }
    
    public static function sendSESFromHtml($template, $to, $subject) {
        
        
            $tos = array();
            if (!empty($to)) {
                $array = explode(";", $to);
                if (!empty($array) && sizeof($array) > 0) {
                    foreach ($array as $mail) {
                        if (!empty($mail)) {
                            array_push($tos, $mail);
                        } else {
                            error_log($mail . " not valid");
                        }
                    }
                }
            }

            if (!empty($tos) && sizeof($tos) > 0) {
                $ses = new SimpleEmailService(AWS_SES_API_KEY, AWS_SES_API_SECRET);
                $ses->enableVerifyPeer(false);
                $m = new SimpleEmailServiceMessage();
                $m->setSubjectCharset("UTF-8");
                $m->setMessageCharset("UTF-8");
                foreach ($tos as $mail) {
                    $m->addTo($mail);
                }
                $m->setFrom(AWS_SES_API_FROM);
                $m->setSubject($subject);
                $m->setMessageFromString(null,$template);
                return $ses->sendEmail($m);
            } else {
                throw new Exception(LanguageUtils::getText("LANG_UTILS_MAIL_ERROR_EMAIL_EMPTY"));
            }
        return false;
    }

}

?>
