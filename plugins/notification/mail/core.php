<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Mail Notification Plugin.
 * Sends a notification using email.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category Plugins
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

require_once 'logUtil.php';
set_include_path(ini_get("include_path") . ":" .
                 ORTRO_NOTIFICATION_PLUGINS.
                 "mail/lib/Pear/"
                 );
require_once 'Mail.php';
@require_once 'Mail/mime.php';

global $conf;
require_once ORTRO_CONF_PLUGINS . 'notification_mail.php';

/**
 * Sends a notification using email.
 *
 * @param array $elements    The user form values
 * @param array $attachments The files to attach
 *
 * @return void
 */
function mailNotify($elements, $attachments)
{
    $logger     = new LogUtil('mail_notification');
    $crlf       = "\n";
    $recipients = array();

    $headers = array('From'    => $GLOBALS['conf']['mail']['from'],
                     'Subject' => $elements['mail_subject']);

    if (isset($GLOBALS['conf']['mail']['reply_to']) &&
    $GLOBALS['conf']['mail']['reply_to'] != '') {
        $headers['Reply-To'] = $GLOBALS['conf']['mail']['reply_to'];
    }

    $logger->trace('DEBUG', "Sending mail with subject: " .
    $elements['mail_subject']);

    if (isset($elements['mail_to'])) {
        $headers['To']    = $elements['mail_to'];
        $recipients['To'] = $elements['mail_to'];
    }

    if (isset($elements['mail_cc'])) {
        $headers['Cc']    = $elements['mail_cc'];
        $recipients['Cc'] = $elements['mail_cc'];
    }

    if (isset($elements['mail_bcc'])) {
        $recipients['Bcc'] = $elements['mail_bcc'];
    }

    $mime = new Mail_Mime($crlf);

    $txt_body  = '';
    $html_body = '';

    if (isset($elements['mail_text'])) {
        $txt_body = $elements['mail_text'];
    }

    if (isset($elements['mail_html'])) {
        //fix FCKEditor strange behavior
        $html_body = str_replace('\\\\\\\'',
                                 "'",
                                 $elements['mail_html']); 
    }

    if ($elements['mail_attach_result']=='1') {
        if (array_key_exists('html', $attachments)) {
            $html_body .= '<br/><br/>' . $attachments['html'];
        }

        if (array_key_exists('file', $attachments)) {
            foreach ($attachments['file'] as $file) {
                $mime->addAttachment($file);
            }
        }
    }

    if ($elements['mail_attach_timestamp']=='1') {
        $txt_body  .= "\n\n" . 'Date: ' . date('Y-m-d H:i');
        $html_body .= '<br/><br/>Date: ' . date('Y-m-d H:i');
    }

    //Add signature
    $html_body .= $GLOBALS['conf']['mail']['signature'];

    $mime->setTxtBody($txt_body);
    $mime->setHtmlBody($html_body);

    $body = $mime->get();
     
    $hdrs = $mime->headers($headers);

    switch ($GLOBALS['conf']['mail']['type']) {
    case 'sendmail':
        $mail = &Mail::factory('sendmail', $GLOBALS['conf']['mail']);
        break;
        
    case 'smtp':
        switch ($GLOBALS['conf']['mail']['auth']) {
        case '1':
            $GLOBALS['conf']['mail']['auth'] = true;
            break;

        case '0':
            $GLOBALS['conf']['mail']['auth'] = false;
            break;
            
        default:
            break;
            
        }
        
        if (isset($GLOBALS['conf']['mail']['timeout']) && 
            $GLOBALS['conf']['mail']['timeout'] == '') {
            $GLOBALS['conf']['mail']['timeout'] = null;
        }
        
        $mail = &Mail::factory('smtp', $GLOBALS['conf']['mail']);
        break;
        
    default:
        $mail = &Mail::factory('mail');
        break;
    }

    $ret = $mail->send($recipients, $hdrs, $body);
    if (PEAR::isError($ret)) {
        $logger->trace('DEBUG', "Delivery mail failed:" . $ret->getMessage());
    }
    $logger->trace('DEBUG', 'Done.');
}
?>