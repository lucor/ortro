<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to view the workflows defined in Ortro
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
$cronUtil = new CronUtil();

//handle filter values over session
if (isset($_REQUEST['filter_reset_x'])) {
    unset($_SESSION['filter_workflow']);
} else {
    //define the filter fields to check
    $filter_array = array('filter_workflow_label', 'filter_workflow_status');
    //valorize the session value
    for ($j = 0; $j < sizeof($filter_array); $j++) {
        if (!isset($_SESSION['filter_workflow'][$filter_array[$j]])) {
            $_SESSION['filter_workflow'][$filter_array[$j]] = '';
        }
        if (isset($_REQUEST[$filter_array[$j]]) &&
        $_REQUEST[$filter_array[$j]] !=
        $_SESSION['filter_workflow'][$filter_array[$j]]) {
            $_SESSION['filter_workflow'][$filter_array[$j]] =
            $_REQUEST[$filter_array[$j]];
        }
    }
}

$dbUtil    = new DbUtil();
$dbh       = $dbUtil->dbOpenConnOrtro();
$systems   = $dbUtil->dbQuery($dbh, $dbUtil->getSystems(), MDB2_FETCHMODE_ASSOC);
$workflows = $dbUtil->dbQuery($dbh,
$dbUtil->getWorkflows($_SESSION['filter_workflow']['filter_workflow_label'],
                      $_SESSION['filter_workflow']['filter_workflow_status']),
                      MDB2_FETCHMODE_ASSOC);
$dbh       = $dbUtil->dbCloseConn($dbh);
unset($dbh);
//Create the form
$form = new HTML_QuickForm('frm', 'post');

/* ACTION TOOLBAR */

$f_refresh_html = $form->addElement('select',
                                    'refresh', 
                                    '', 
                                    array('none'=>REFRESH_MANUAL,
                                    '15'=>REFRESH_EVERY_15,
                                    '30'=>REFRESH_EVERY_30,
                                    '60'=>REFRESH_EVERY_60,
                                    '180'=>REFRESH_EVERY_180),
                                    'onchange=\'refreshPage();\'')->toHTML();

$toolbar = createToolbar(array('backPage'=>'default',
                               'reload_page'=>'default',
                               'add'=>'default_admin',
                               'details'=>'guest',
                               'run'=>'admin',
                               'lock'=>'admin',
                               'unlock'=>'admin',
                               'edit'=>'admin',
                               'copy'=>'admin',
                               'delete'=>'admin',
                               'kill'=>'admin',
                               'auto_refresh'=>$f_refresh_html));

// The toolbar javascript is used below $toolbar['javascript'];

/* JOB TABLE */
$table_page = new HTML_Table($table_attributes);
$checkbox   = $form->addElement('checkbox', 'id_chk_all', '', '');
$checkbox->updateAttributes(array('onclick' => 'checkAll(this.checked);'));
$f_chk_all = $checkbox->toHTML();
$table_page->addRow(array($f_chk_all, FIELD_WORKFLOW, FIELD_WORKFLOW_STATUS,
                          FIELD_LAST_EXEC_TIME, FIELD_NEXT_EXEC_TIME),
                    'colspan=1 align=center', 'TH');

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

foreach ($workflows as $key) {
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

        $locked = false;
        switch ($key['status']) {
        case 'W':
            $img_status = 'wait.png';
            $alt_status = TOOLTIP_WAIT_FOR_EXEC;
            break;
        case 'R':
            $img_status = 'running.png';
            $alt_status = TOOLTIP_RUNNING;
            break;
        case 'L':
            $img_status = 'locked.png';
            $alt_status = TOOLTIP_LOCKED;
            $locked     = true;
            break;
        }

        // add images
        $open_span  = '';
        $close_span = '';


        if ($key['description'] != '') {
            $description = '<img class="tooltip" width="12px" height="12px" ' . 
                           'alt="" src="img/details.png" ' . 
                           'onmouseover="return overlib(\'' .
                           '&lt;div class=ovfl&gt;' . 
                           str_replace('%0A', '%3Cbr%3E', $key['description']) . 
                           '&lt;/div&gt;' .
                           '\',DECODE,CLOSETEXT,\'X\',STICKY,MOUSEOFF,' . 
                           'TIMEOUT,5000,DELAY,500,MIDX,0,RELY,0,CAPTION,\''.
                           TOOLTIP_WORKFLOW_DESCRIPTION . 
                           '\');" onmouseout="return nd();"/>' . 
                           '&nbsp;' . $key['label'];     
        } else {
            $description = '<img width="12px" height="12px" alt="" ' . 
                           'src="img/transparent.png"/>&nbsp;' . $key["label"];
        }

        $img_status_html = '<img src="img/' . $img_status  . 
                           '" border="0" alt="' . $alt_status . '"/>';

        if ($key["end_exec"] == '0' || $key["end_exec"] == '') {
            $end_exec = '-';
        } else {
            $end_exec = date('Y-m-d H:i', $key["end_exec"] . '');
        }

        if ($key["status"] == 'L' || $key["status"] == 'R' ) {
            $next_exec = '-';
        } elseif ($key["crontab_m"] == '-') {
            $next_exec = FIELD_JOB_SCHEDULE_MANUAL;
        } else {
            if ($key['calendars'] == '---') {
                //No calendar to filter
                $next_exec_timestamp = $cronUtil->calcNextCronDate($key["crontab_m"],
                                                               $key["crontab_h"],
                                                               $key["crontab_dom"],
                                                               $key["crontab_mon"],
                                                               $key["crontab_dow"]);
                $next_exec = date('Y-m-d H:i', $next_exec_timestamp);
            } else {
                function getGreater($el){
                    global $today;
                    return $el >= $today;
                }

                $today = mktime(0,0,0);
                $calendars = array_filter(explode('#',$key['calendars']), 'getGreater');

                foreach ($calendars as $available_date) {
                    $date_for_calc = $available_date;
                    if ($available_date == $today) {
                        $date_for_calc = time();
                    }
                    $next_exec = 'No execution available with this calendar'; //The
                    $next_exec_timestamp = $cronUtil->calcNextCronDate($key["crontab_m"],
                                                                   $key["crontab_h"],
                                                                   $key["crontab_dom"],
                                                                   $key["crontab_mon"],
                                                                   $key["crontab_dow"],
                                                                   $date_for_calc);

                    $test_date = mktime(0,0,0,date("n", $next_exec_timestamp),
                                              date("j", $next_exec_timestamp),
                                              date("Y", $next_exec_timestamp));
                    if ($available_date == $test_date) {
                        $next_exec = date('Y-m-d H:i', $next_exec_timestamp);
                        break;
                    }
                }
            }
        }
        $checkbox = $form->addElement('checkbox', 
                                      "id_chk[" . $key['id_workflow'] . "]", 
                                      '', '');
        $checkbox->updateAttributes(array('value' => $key['id_system']));
        $checkbox->updateAttributes(array('id' => 'id_chk'));
        $checkbox->updateAttributes(array('role' => $role));


        $f_chk = $checkbox->toHTML();
        $table_page->addRow(array($f_chk,
                                  $description,
                                  '<center>' . $img_status_html .'</center>',
                                  '<center>' . $end_exec .'</center>',
                                  '<center>' . $next_exec .'</center>'), 
                            'class=c2 onmouseover=highlightRow(this)', 'TD', true);
    }
}
$table_page->updateColAttributes(0, 'width=1%');
$table_page->updateColAttributes(2, 'width=10%');
$table_page->updateColAttributes(3, 'width=20%');
$table_page->updateColAttributes(4, 'width=20%');

/* FILTER BOX */
// Filter form fields
$select_filter_workflow_status['*'] = FILTER_ALL;
$select_filter_workflow_status['L'] = FILTER_LOCKED;
$select_filter_workflow_status['R'] = FILTER_RUNNING;
$select_filter_workflow_status['W'] = FILTER_WAIT;

// Filter form fields
$select_filter_result['*'] = FILTER_ALL;
$select_filter_result[1]   = FILTER_SUCCESS;
$select_filter_result[0]   = FILTER_ERROR;

$f_filter_workflow_status_obj = $form->addElement('select', 
                                                  'filter_workflow_status',
                                                  '', 
                                                  $select_filter_workflow_status, 
                                              'onchange="document.frm.submit();"');
$f_filter_workflow_status_obj->setSelected($_SESSION['filter_workflow']
                                                    ['filter_workflow_status']);
$f_filter_workflow_label_obj = $form->addElement('text', 
                                                 'filter_workflow_label', 
                                                 '', 
                                               'onchange="document.frm.submit();"');
$f_filter_workflow_label_obj->setValue($_SESSION['filter_workflow']
                                                ['filter_workflow_label']);
$f_filter_apply_obj = $form->addElement('image', 'filter', 'img/filter.png', 
                                        'title="' . FILTER_APPLY_FILTER_TITLE . 
                                        '" onchange="document.frm.submit();"');
$f_filter_reset_obj = $form->addElement('image', 'filter_reset', 'img/undo.png', 
                                        'title="' . FILTER_RESET_TITLE . 
                                        '" onchange="document.frm.submit();"');

// Filter table
$table_filter = new HTML_Table($table_attributes);
$table_filter->addRow(array(FILTER), '', 'TH');
$table_filter->addRow(array(FILTER_WORKFLOW . 
                            $f_filter_workflow_label_obj->toHTML() .
                            '&nbsp; ' . FILTER_STATUS . ' ' . 
                            $f_filter_workflow_status_obj->toHTML() . 
                            '&nbsp;&nbsp;' .$f_filter_apply_obj->toHTML() . 
                            '&nbsp;&nbsp;' .$f_filter_reset_obj->toHTML()),
                      'align=left valign=top', 'TD', false);    

/* HIDDEN FIELDS */
$action = '';
if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
}
$f_hidden  = $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'action', $action)->toHTML();
$f_hidden .= $form->createElement('hidden', 
                                  'is_admin_for_a_system', 
                                  $is_admin_for_a_system)->toHTML();
//convert form in array for extact js and attributes
$formArray = $form->toArray(); 
?>
<div id="ortro-title"><?php echo WORKFLOW_TOP; ?></div>
<p><?php echo WORKFLOW_TITLE; ?></p>
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
