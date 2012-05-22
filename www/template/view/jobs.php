<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to view the jobs defined in Ortro
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
    unset($_SESSION['filter_job']);
} else {
    //define the filter fields to check
    $filter_array = array('filter_job_label',
                          'filter_system',
                          'filter_status',
                          'filter_result');
    //valorize the session value
    for ($j = 0; $j < sizeof($filter_array); $j++) {
        if (!isset($_SESSION['filter_job'][$filter_array[$j]])) {
            $_SESSION['filter_job'][$filter_array[$j]] = '';
        }
        if (isset($_REQUEST[$filter_array[$j]]) &&
        $_REQUEST[$filter_array[$j]] !=
        $_SESSION['filter_job'][$filter_array[$j]]) {
            $_SESSION['filter_job'][$filter_array[$j]] =
            $_REQUEST[$filter_array[$j]];
        }
    }
}

//define the order by method display
//default order by name and label desc

if (!isset($_REQUEST['orderby']) && !isset($_SESSION['orderby_other'])) {
    //default mode
    $_SESSION['orderby_system']      = 'name';
    $_SESSION['orderby_system_mode'] = 'asc';
    $_SESSION['orderby_other']       = 'label';
    $_SESSION['orderby_other_mode']  = 'asc';
} else {
    if (isset($_REQUEST['order'])) {
        //a sorting mode was request...
        switch ($_REQUEST['orderby']) {
        case $_SESSION['orderby_system']:
            //sorting for system
            if ($_SESSION['orderby_system_mode'] == 'asc') {
                $_SESSION['orderby_system_mode'] = 'desc';
            } else {
                $_SESSION['orderby_system_mode'] = 'asc';
            }
            break;
        default:
            //sorting for other fields
            if ($_REQUEST['orderby'] == $_SESSION['orderby_other']) {
                if ($_SESSION['orderby_other_mode'] == 'asc') {
                    $_SESSION['orderby_other_mode'] = 'desc';
                } else {
                    $_SESSION['orderby_other_mode'] = 'asc';
                }
            } else {
                $_SESSION['orderby_other_mode'] = 'asc';
            }
            $_SESSION['orderby_other'] = $_REQUEST['orderby'];
            break;
        }
    }
}

if ($_SESSION['orderby_system_mode'] == 'asc') {
    $img_arrow = '<img src=img/arrowdown.png border=0>';
} else {
    $img_arrow = '<img src=img/arrowup.png border=0>';
}

$img_name = $img_arrow;

if ($_SESSION['orderby_other_mode'] == 'asc') {
    $img_arrow = '<img src=img/arrowdown.png border=0>';
} else {
    $img_arrow = '<img src=img/arrowup.png border=0>';
}

$img_status      = '';
$img_status_exec = '';
$img_end_exec    = '';
$img_label       = '';
switch ($_SESSION['orderby_other']) {
case 'status':
    $img_status = $img_arrow;
    break;
case 'status_exec':
    $img_status_exec = $img_arrow;
    break;
case 'end_exec':
    $img_end_exec = $img_arrow;
    break;
default://label
    $img_label = $img_arrow;
    break;
}

$orderby = $_SESSION['orderby_system'] . ' ' . 
           $_SESSION['orderby_system_mode'] . ',' .
           $_SESSION['orderby_other'] . ' ' . 
           $_SESSION['orderby_other_mode'];

$dbUtil     = new DbUtil();
$dbh        = $dbUtil->dbOpenConnOrtro();
$systems    = $dbUtil->dbQuery($dbh, $dbUtil->getSystems(), MDB2_FETCHMODE_ASSOC);
$systemJobs = $dbUtil->dbQuery($dbh, 
                $dbUtil->getSystemJobs($_SESSION['filter_job']['filter_job_label'],
                                       $_SESSION['filter_job']['filter_system'],
                                       $_SESSION['filter_job']['filter_status'],
                                       $_SESSION['filter_job']['filter_result'],
                                       $orderby), 
                                MDB2_FETCHMODE_ASSOC);
$dbh        = $dbUtil->dbCloseConn($dbh);
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
$checkbox   = $form->addElement('checkbox', "id_chk_all", '', '');
$checkbox->updateAttributes(array('onclick' => 'checkAll(this.checked);'));
$f_chk_all                = $checkbox->toHTML();
$f_submit_system          = $form->addElement('submit', 
                                              'order', 
                                              FIELD_SYSTEM,
                                              'onclick=(document.frm.orderby.value'. 
                                              '=\'name\');')->toHTML() . $img_name;
$f_submit_job             = $form->addElement('submit', 
                                              'order', 
                                              FIELD_JOB,
                                              'onclick=(document.frm.orderby.value'.
                                              '=\'label\');')->toHTML() . $img_label;
$f_submit_job_status      = $form->addElement('submit', 
                                              'order',
                                              FIELD_JOB_STATUS,
                                              'onclick=(document.frm.orderby.value'.
                                              '=\'status\');')->toHTML() . 
                                              $img_status;
$f_submit_last_job_result = $form->addElement('submit', 
                                              'order', 
                                              FIELD_LAST_JOB_RESULT,
                                              'onclick=(document.frm.orderby.value'.
                                              '=\'status_exec\');')->toHTML() . 
                                              $img_status_exec;
$f_submit_last_exec_time  = $form->addElement('submit',
                                                'order',
                                              FIELD_LAST_EXEC_TIME,
                                              'onclick=(document.frm.orderby.value'.
                                              '=\'end_exec\');')->toHTML() . 
                                              $img_end_exec;
$table_page->addRow(array($f_chk_all, 
                          $f_submit_system, 
                          $f_submit_job, 
                          $f_submit_job_status, 
                          $f_submit_last_job_result, 
                          $f_submit_last_exec_time, 
                          FIELD_NEXT_EXEC_TIME), 
                    'colspan=1 align=center class=ortro-input', 
                    'TH');

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

foreach ($systemJobs as $key) {
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
        if ($system_name == '' || $system_name != $key["name"]) {
            $table_page->addRow(array('&nbsp;',
                                      $key["name"]), 
                                      'class=c3', 
                                      'TD', 
                                      true);
            $system_name = $key["name"];
        }

        $locked = false;
        switch ($key["status"]) {
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
        
        if ($key["status"] == 'R') {
            //show info icon
            $img_status_exec = 'info.png';
        } else if ($key["status_exec"] == '1') {
            $img_status_exec = 'success.png';
        } else {
            $img_status_exec = 'warning.png';
        }

        if ($key['msg_exec'] != '') {
            $msg_exec = '<div class=ovfl>' . 
                        str_replace('%0A', '%3Cbr%3E',
                                    $key['msg_exec']) . 
                        '</div>';

            $img_status_exec_html = '<img class="tooltip" src="img/' .
                                    $img_status_exec  .
                                    '" border="0" onmouseover="return overlib(\'' . 
                                    $msg_exec .
                                    '\',CLOSETEXT,\'X\',DECODE,STICKY,MOUSEOFF,' .
                                    'TIMEOUT,5000,DELAY,500,'.
                                    'MIDX,0,RELY,0,CAPTION,\''. 
                                    TOOLTIP_JOB_RESULT . 
                                    '\');" onmouseout="return nd();">';
        } else {
            $img_status_exec_html = '<img src="img/' . 
                                    $img_status_exec  . 
                                    '" border="0">';
        }

        if ($key['description'] != '') {
            $description = '<img class="tooltip" widht="12" height="12" ' . 
                           'src="img/details.png" onmouseover="return overlib(\'' .
                           '<div class=ovfl>' . 
                           str_replace('%0A', '%3Cbr%3E', $key['description']) . 
                           '</div>' .
                           '\',DECODE,CLOSETEXT,\'X\',STICKY,MOUSEOFF,TIMEOUT,'.
                           '5000,DELAY,500,MIDX,0,RELY,0,CAPTION,\'' . 
                           TOOLTIP_JOB_DESCRIPTION . 
                           '\');" onmouseout="return nd();">' . 
                           '&nbsp;' . $key['label'];     
        } else {
            $description = '<img widht=12 height=12 src="img/transparent.png">' .
                           '&nbsp;' . $key["label"];
        }

        $img_status_html = '<img src="img/' . $img_status  . 
                           '" border="0" title="' . $alt_status . 
                           '" alt="' . $alt_status . '">';

        if ($key["end_exec"] == '0' || $key["end_exec"] == '') {
            $end_exec = '-';
        } else {
            $end_exec = date('Y-m-d H:i', $key["end_exec"] . '');
        }

        if ($key["status"] == 'L') {
            $next_exec = '-';
        } elseif ($key["crontab_m"] == '-') {
            $next_exec = FIELD_JOB_SCHEDULE_MANUAL;
        } elseif ($key["schedule_type"] == 'T') {
            $next_exec = FIELD_WORKFLOW;
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
                                      "id_chk[" . $key['id_job'] . "]", 
                                      '',
                                      '');
        $checkbox->updateAttributes(array('value' => $key['id_system']));
        $checkbox->updateAttributes(array('id' => 'id_chk'));
        $checkbox->updateAttributes(array('role' => $role));

        $f_chk = $checkbox->toHTML();
        $table_page->addRow(array($f_chk,
                                  '&nbsp;',
                                  $description,
                                  '<center>' . $img_status_html .'</center>',
                                  '<center>' . $img_status_exec_html .'</center>',
                                  '<center>' . $end_exec .'</center>',
                                  '<center>' . $next_exec .'</center>'), 
                                  'class=c2 onmouseover=highlightRow(this)', 
                            'TD', 
                            true);
    }
}
$table_page->updateColAttributes(0, 'width=1%');

/* FILTER BOX */
// Filter form fields
$select_filter_status['*'] = FILTER_ALL;
$select_filter_status['L'] = FILTER_LOCKED;
$select_filter_status['R'] = FILTER_RUNNING;
$select_filter_status['W'] = FILTER_WAIT;

// Filter form fields
$select_filter_result['*'] = FILTER_ALL;
$select_filter_result[1]   = FILTER_SUCCESS;
$select_filter_result[0]   = FILTER_ERROR;

$f_filter_system_obj = $form->addElement('select', 
                                         'filter_system', 
                                         '', 
                                         $select_filter_system,
                                         'onchange="document.frm.submit();"');
$f_filter_system_obj->setSelected($_SESSION['filter_job']['filter_system']);
$f_filter_status_obj = $form->addElement('select', 
                                         'filter_status', 
                                         '', 
                                         $select_filter_status, 
                                         'onchange="document.frm.submit();"');
$f_filter_status_obj->setSelected($_SESSION['filter_job']['filter_status']);
$f_filter_result_obj = $form->addElement('select', 
                                         'filter_result', 
                                         '', 
                                         $select_filter_result, 
                                         'onchange="document.frm.submit();"');
$f_filter_result_obj->setSelected($_SESSION['filter_job']['filter_result']);
$f_filter_job_label_obj = $form->addElement('text', 
                                            'filter_job_label',
                                            '', 
                                            'onchange="document.frm.submit();"');
$f_filter_job_label_obj->setValue($_SESSION['filter_job']['filter_job_label']);
$f_filter_apply_obj = $form->addElement('image', 
                                        'filter', 
                                        'img/filter.png', 
                                        'title="' . FILTER_APPLY_FILTER_TITLE . 
                                        '" onchange="document.frm.submit();"');
$f_filter_reset_obj = $form->addElement('image', 
                                        'filter_reset', 
                                        'img/undo.png', 
                                        'title="' . FILTER_RESET_TITLE . 
                                        '" onchange="document.frm.submit();"');
// Filter table
$table_filter = new HTML_Table($table_attributes);
$table_filter->addRow(array(FILTER), '', 'TH');
$table_filter->addRow(array(FILTER_JOB . 
                            $f_filter_job_label_obj->toHTML() .
                            '&nbsp; ' . FILTER_SYSTEM . ' ' . 
                            $f_filter_system_obj->toHTML() . 
                            '&nbsp; ' . FILTER_STATUS . ' ' . 
                            $f_filter_status_obj->toHTML() . 
                            '&nbsp; ' . FILTER_RESULT . ' ' . 
                            $f_filter_result_obj->toHTML() .
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
$f_hidden .= $form->createElement('hidden', 'orderby', '')->toHTML();

$formArray = $form->toArray(); //convert form in array for extact js and attributes
?>
<div id="ortro-title"><?php echo JOB_TOP; ?></div>
<p><?php echo JOB_TITLE; ?></p>
<?php echo $formArray['javascript']; ?>
<form <?php echo $formArray['attributes']; ?>><?php echo $f_hidden; ?>
<div class="ortro-table"><?php $table_filter->display(); ?></div>
<br />

<div id="toolbar" class="ortro-table"><?php echo $toolbar['javascript']; ?>
<?php echo $toolbar['header']; ?></div>
<br />
<div class="ortro-table"><?php $table_page->display(); ?></div>
<div id="toolbar_menu" class="ortro-table"><?php  echo $toolbar['menu']; ?>
</div>
</form>
