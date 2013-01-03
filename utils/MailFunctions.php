<?php

/*
 * Dependencies
 */
require_once __DIR__ . '/../apis/Mail/Mandrill.php';

class MailUtil {
    /*
     * Mail
     */

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

    /*
     * Mail
     */
}

?>
