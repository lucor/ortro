<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to view the notifications defined in Ortro
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category GUI
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

require_once 'cronUtil.php';

//handle filter values over session
if (isset($_REQUEST['filter_reset_x'])) {
    unset($_SESSION['filter_notify']);
} else {
    //define the filter fields to check
    $filter_array = array('filter_job_label',
                          'filter_system',
                          'filter_notify_type',
                          'filter_notify_on');
    //valorize the session value
    for ($j = 0; $j < sizeof($filter_array); $j++) {
        if (!isset($_SESSION['filter_notify'][$filter_array[$j]])) {
            $_SESSION['filter_notify'][$filter_array[$j]] = '';
        }
        if (isset($_REQUEST[$filter_array[$j]]) &&
        $_REQUEST[$filter_array[$j]] !=
        $_SESSION['filter_notify'][$filter_array[$j]]) {
            $_SESSION['filter_notify'][$filter_array[$j]] = 
                $_REQUEST[$filter_array[$j]];
        }
    }
}

$dbUtil           = new DbUtil();
$dbh              = $dbUtil->dbOpenConnOrtro();
$systems          = $dbUtil->dbQuery($dbh,
                                     $dbUtil->getSystems(),
                                     MDB2_FETCHMODE_ASSOC);
$systemJobsNotify = $dbUtil->dbQuery($dbh,
                                     $dbUtil->getSystemJobsNotify($_SESSION['filter_notify']['filter_job_label'],
                                                                  $_SESSION['filter_notify']['filter_system'],
                                                                  $_SESSION['filter_notify']['filter_notify_type'],
                                                                  $_SESSION['filter_notify']['filter_notify_on']),
                                                                  MDB2_FETCHMODE_ASSOC);
$notifyType       = $dbUtil->dbQuery($dbh,
                                     $dbUtil->getNotifyTypeList(),
                                     MDB2_FETCHMODE_ASSOC);
$dbh              = $dbUtil->dbCloseConn($dbh);
unset($dbh);

//Create the form
$form = new HTML_QuickForm('frm', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default',
                               'reload_page'=>'default',
                               'add'=>'default_admin',
                               'details'=>'guest',
                               'edit'=>'admin',
                               'copy'=>'admin',
                               'delete'=>'admin'));

// The toolbar javascript is used below $toolbar['javascript'];

/* FILTER BOX */
$system_name           = '';
$is_admin_for_a_system = false;

$admin_for_systems = array();
$guest_for_systems = array();

$select_filter_system['*'] = FILTER_ALL;

foreach ($systems as $key) {
    if (array_key_exists('ADMIN', $_policy) ||
    in_array($key['id_system'],
    explode(',', $_policy['SYSTEM_ADMIN']))) {
        $admin_for_systems[$key['id_system']] = true;
        //Used to enable default actions for admin in the toolbar
        //(see hidden fields below)
        $is_admin_for_a_system = true;
    }

    if ($admin_for_systems[$key['id_system']] ||
    array_key_exists('GUEST', $_policy) ||
    in_array($key['id_system'],
    explode(',', $_policy['SYSTEM_GUEST']))) {
        $guest_for_systems[$key['id_system']]    = true;
        $select_filter_system[$key['id_system']] = $key['name'];
    }
}

// Filter form fields
$select_filter_notify_on['*']  = FILTER_ALL;
$select_filter_notify_on[3]    = FILTER_START;
$select_filter_notify_on[2]    = FILTER_END;
$select_filter_notify_on[1]    = FILTER_SUCCESS;
$select_filter_notify_on[0]    = FILTER_ERROR;

$select_filter_pluginType['*'] = FILTER_ALL;

foreach ($notifyType as $key) {
    $cfg_file = ORTRO_NOTIFICATION_PLUGINS . $key['label'] . DS . 'configure.php';
    // Include the plugin language definition
    i18n('notification', $key['label']);
    include_once $cfg_file; //Now are available the $plugin_field array
    $select_filter_pluginType[$key['id_notify_type']] =
    $plugin_field[$key['label']][0]['title'];
}

$f_filter_system_obj = $form->addElement('select', 
                                         'filter_system', 
                                         '', 
                                         $select_filter_system, 
                                         'onchange="document.frm.submit();"');
$f_filter_system_obj->setSelected($_SESSION['filter_notify']['filter_system']);
$f_filter_job_label_obj = $form->addElement('text', 
                                            'filter_job_label', 
                                            '', 
                                            'onchange="document.frm.submit();"');
$f_filter_job_label_obj->setValue($_SESSION['filter_notify']['filter_job_label']);
$f_filter_notify_type_obj = $form->addElement('select', 
                                              'filter_notify_type', 
                                              '', 
                                              $select_filter_pluginType, 
                                              'onchange="document.frm.submit();"');
$f_filter_notify_type_obj->setSelected($_SESSION['filter_notify']
                                                ['filter_notify_type']);
$f_filter_notify_on_obj = $form->addElement('select', 
                                            'filter_notify_on', 
                                            '', 
                                            $select_filter_notify_on, 
                                            'onchange="document.frm.submit();"');
$f_filter_notify_on_obj->setSelected($_SESSION['filter_notify']['filter_notify_on']);
$f_filter_reset_obj = $form->addElement('image', 
                                        'filter_reset', 
                                        'img/undo.png', 
                                        'title="' . FILTER_RESET_TITLE . 
                                        '" onchange="document.frm.submit();"');

// Filter table
$table_filter = new HTML_Table($table_attributes);
$table_filter->addRow(array(FILTER), '', 'TH');
$table_filter->addRow(array(FILTER_JOB . $f_filter_job_label_obj->toHTML() .
                            '&nbsp; ' . FILTER_SYSTEM . 
                            $f_filter_system_obj->toHTML() . 
                            '&nbsp; ' . FILTER_NOTIFY_TYPE . 
                            $f_filter_notify_type_obj->toHTML() . 
                            '&nbsp; ' . FILTER_NOTIFY_ON . 
                            $f_filter_notify_on_obj->toHTML() .
                            '&nbsp;&nbsp;' .$f_filter_reset_obj->toHTML()),
                       'align=left valign=top', 'TD', false);

/* NOTIFICATION TABLE */
$table_page = new HTML_Table($table_attributes);
$checkbox   = $form->addElement('checkbox', 'id_chk_all', '', '');
$checkbox->updateAttributes(array('onclick' => 'checkAll(this.checked);'));
$f_chk_all = $checkbox->toHTML();
$table_page->addRow(array($f_chk_all, 
                          FIELD_SYSTEM, 
                          FIELD_JOB, 
                          FIELD_NOTIFY_TYPE, 
                          FIELD_NOTIFY_SEND_ON,
                          TOOLTIP_NOTIFICATION_DETAILS), 
                    'colspan=1 align=center', 'TH');

foreach ($systemJobsNotify as $key) {
    $is_admin_for_system = false;
    $is_guest_for_system = false;
    
    $role = '';
    if (array_key_exists($key['id_system'], $admin_for_systems)) {
        $is_admin_for_system = true;
        $role                = 'admin';
    }

    if (array_key_exists($key['id_system'], $guest_for_systems)) {
        $is_guest_for_system = true;
    }

    if ($is_guest_for_system) {
        if (isset($system_name) && 
            ($system_name == '' || $system_name != $key['system_name'])) {
            $system_name = $key['system_name'];
            $table_page->addRow(array('&nbsp;', $system_name), 
                                'class=c3', 
                                'TD', 
                                true);
        }

        $notify_on = explode('-', $key['notify_on']);
        array_shift($notify_on);
        array_pop($notify_on);
        
        $f_notify_on = '';
        
        for ($i = 0; $i < sizeof($notify_on); $i++) {
            switch ($notify_on[$i]) {
                case '0':
                    $f_notify_on .= FIELD_NOTIFY_JOB_ERROR;
                break;
                case '1':
                    $f_notify_on .= FIELD_NOTIFY_JOB_SUCCESS;
                break;
                case '2':
                    $f_notify_on .= FIELD_NOTIFY_JOB_END;
                break;
                case '3':
                    $f_notify_on .= FIELD_NOTIFY_JOB_START;
                break;
            }
            if ($i < (sizeof($notify_on) - 1)) {
                $f_notify_on .= ', ';
            }
        }

        $notifyView = $dbUtil -> dbUnserialize($key['parameters']);
        
        
        $notifyString = '';
        
        if (isset($notifyView['mail_to'])) {
            $notifyString = PLUGIN_MAIL_TO_DESCRIPTION . ' ' .
                            $notifyView['mail_to'] . '<br/><br/> ' .
                            PLUGIN_MAIL_SUBJECT_DESCRIPTION . ' ' .
                            $notifyView['mail_subject'];
        } else if (isset($notifyView['jabber_to'])) {
            $notifyString = PLUGIN_JABBER_TO_DESCRIPTION_DETAIL . ' ' .
                            $notifyView['jabber_to'] . '<br/><br/> ' .
                            PLUGIN_JABBER_MESSAGE_DESCRIPTION . ' ' .
                            $notifyView['jabber_message'];
        } else if (isset($notifyView['sms_ftp_dir'])) {
            $notifyString = PLUGIN_SMS_FTP_REMOTE_DIR_DESCRIPTION_DETAIL . ' ' .
                            $notifyView['sms_ftp_dir'] . '<br/><br/> ' .
                            PLUGIN_SMS_FTP_MESSAGE_DESCRIPTION . ' ' .
                            $notifyView['sms_ftp_message'];
        }
        
        $f_notify_details = '';
        if($notifyString != '') {
            $f_notify_details = '<center><img class="tooltip" width="12" height="12" src="img/details.png" border="0" 
                                       alt="details" onmouseover="return overlib(\'' .
                                      '<div class=ovfl>' .
                                      str_replace('%0A', '%3Cbr%3E', $notifyString) .
                                      '</div>' .
                                      '\',DECODE,CLOSETEXT,\'X\',STICKY,MOUSEOFF,TIMEOUT,'.
                                      '5000,DELAY,500,MIDX,0,RELY,0,CAPTION,\'' .
                                      TOOLTIP_NOTIFICATION_DETAILS .
                                      '\');" onmouseout="return nd();"/></center>';
        }
        
        $checkbox = $form->addElement('checkbox', 
                                      'id_chk[' . $key['id_notify'] . ']', '', '');
        $checkbox->updateAttributes(array('value' => $key['id_system']));
        $checkbox->updateAttributes(array('id' => 'id_chk'));
        $checkbox->updateAttributes(array('role' => $role));
        //required only for show notification details, used in js
        $checkbox->updateAttributes(array('system_name' => $key['system_name']));
        $checkbox->updateAttributes(array('notify_label' => $key['notify_label']));
        $checkbox->updateAttributes(array('job_label' => $key['job_label']));
        $f_chk = $checkbox->toHTML();

        $table_page->addRow(array($f_chk,
                                  '&nbsp;',
                                  $key['job_label'], 
                                  $plugin_field[$key['notify_label']][0]['title'], 
                                  $f_notify_on,
                                  $f_notify_details), 
                                  'class=c2 onmouseover=highlightRow(this) valign=top', 'TD', true);
    }
}
$table_page->updateColAttributes(0, 'width=1%');

/* HIDDEN FIELDS */
$action = '';
if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
}
$f_hidden  = $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'action', $action)->toHTML();
 //setted via js when notification details is requested
$f_hidden .= $form->createElement('hidden', 'notify_label', '')->toHTML();
$f_hidden .= $form->createElement('hidden', 'job_label', '')->toHTML();
$f_hidden .= $form->createElement('hidden', 'system_name', '')->toHTML();
$f_hidden .= $form->createElement('hidden', 
                                  'is_admin_for_a_system', 
                                  $is_admin_for_a_system)->toHTML();
//convert form in array for extact js and attributes
$formArray = $form->toArray(); 
?>
<div id="ortro-title"><?php echo NOTIFICATION_TOP; ?></div>
<p><?php echo NOTIFICATION_TITLE; ?></p>
<?php echo $formArray['javascript']; ?>
<form <?php echo $formArray['attributes']; ?>><?php echo $f_hidden; ?>
<div class="ortro-table"><?php $table_filter->display(); ?></div>
<br />
<div id="toolbar" class="ortro-table"><?php echo $toolbar['javascript']; ?>
<?php echo $toolbar['header']; ?></div>
<br />
<div class="ortro-table"><?php $table_page->display(); ?></div>
<div id="toolbar_menu" class="ortro-table"><?php echo $toolbar['menu']; ?>
</div>
</form>
