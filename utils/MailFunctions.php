<?php

/*
 * Dependencies
 */
require_once __DIR__ . '/../apis/Mail/Mandrill.php';
require_once __DIR__ . '/../apis/Mail/ses.php';

class MailUtil {

    public static function sendTemplateEmail($templateName, $param, $subject, $to) {
        if (!empty($param)) {
            $param = '"global_merge_vars": [' . $param . '],';
        } else {
            $param = "";
        }
        $request_json = '{
		"type":"messages",
		"call":"send-template",
		"key": "' . MANDRILL_API_KEY . '",
		"template_name": "' . $templateName . '",
		"template_content": [
		{
		"name": "fabelist",
		"content": "Fabelist Mandrill"
	}
	],
	"message": {
	"subject": "' . $subject . '",
	"from_email": "info@fabelist.com",
	"from_name": "Fabelist",
	"to": [
	' . $to . '
	],
	' . $param . '
	"track_opens": true,
	"track_clicks": true,
	"tags": [
	"Fabelist"
	]
	}
	}';
        try {
            $ret = Mandrill::call((array) json_decode($request_json));
        } catch (Exception $e) {
            throw($e);
            return $e;
        }
        return $ret;
    }

    public static function sendEmail($html, $subject, $to) {
        $html = str_replace("\"", "'", $html);
        $subject = str_replace("\"", "'", $subject);
        $param = "";
        $request_json = '{
		"type":"messages",
		"call":"send",
		"key": "' . MANDRILL_API_KEY . '",
		"message": {
		"html": "' . $html . '",
		"subject": "' . $subject . '",
		"from_email": "no-reply@timety.com",
		"from_name": "Timety",
		"to": [' . $to . '],
		' . $param . '
		"track_opens": true,
		"track_clicks": true,
		"tags": ["Timety"]
	}
	}';
        try {
            $ret = Mandrill::call((array) json_decode($request_json));
        } catch (Exception $e) {
            throw($e);
            return $e;
        }
        return $ret;
    }

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
                $m->setMessageFromString(null, $template);
                return $ses->sendEmail($m);
            } else {
                throw new Exception(LanguageUtils::getText("LANG_UTILS_MAIL_ERROR_EMAIL_EMPTY"));
            }
        } else {
            throw new Exception(LanguageUtils::getText("LANG_UTILS_MAIL_ERROR_FILE_NOT_FOUND"));
        }
        return false;
    }

    public static function sendSESFromHtml($html, $to, $subject) {

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
            $m->setMessageFromString(null, $html);
            return $ses->sendEmail($m);
        } else {
            throw new Exception(LanguageUtils::getText("LANG_UTILS_MAIL_ERROR_EMAIL_EMPTY"));
        }
        return false;
    }

}

?>
