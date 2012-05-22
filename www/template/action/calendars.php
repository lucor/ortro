<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Allows to add/handle the calendar defined in ortro.
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

$dbUtil = new DbUtil();
$dbh    = $dbUtil->dbOpenConnOrtro();

$redirect_to_view = false;

if (isset($_REQUEST['id_calendar'])) {
    $id_calendar = $_REQUEST['id_calendar'];
}
if (isset($_REQUEST['id_system'])) {
    $id_system = $_REQUEST['id_system'];
}
if (isset($_REQUEST['label'])) {
    $label = $_REQUEST['label'];
}
if (isset($_REQUEST['calendar'])) {
    $calendar = $_REQUEST['calendar'];
}

$shared_systems = 0;
if (isset($_REQUEST['id_shared_systems'])) {
    $shared_systems = '#' . implode('#', $_REQUEST['id_shared_systems']) . '#';
}

/* ERROR CHECK */

$error = false;

switch ($_REQUEST['action']) {
case 'copy':
    break;
case 'add':
    //check for unique label
    $rows = $dbUtil->dbQuery($dbh, $dbUtil->checkExistsCalendar($label));
    if (isset($rows[0][0])) {
        //label
        $action_msg = MSG_ACTION_LABEL_ALREADY_USED;
        $type_msg   = 'warning';
        $error      = true;
    }
    break;
case 'edit':
    //check for unique label
    $rows   = $dbUtil->dbQuery($dbh, $dbUtil->checkExistsCalendar($label));
    $result = $rows[0][0];
    if ($result != $id_calendar && count($rows)>0) {
        //label
        $action_msg = MSG_ACTION_LABEL_ALREADY_USED;
        $type_msg   = 'warning';
        $error      = true;
    }
    break;
case 'delete':
    //check for associated notify 
    foreach ($_REQUEST['id_chk'] as $id_calendar => $label) {
        $rows = $dbUtil->dbQuery($dbh, 
                                  $dbUtil->checkJobCalendar($id_calendar),
                                  MDB2_FETCHMODE_ASSOC);
        if (count($rows) > 0) {
            foreach ($rows as $key) {
                $job_list .= '<br/> - ' . $key['label'];
            }
            $action_msg       = MSG_ACTION_REMOVE_CALENDAR_FIRST . $job_list;
            $type_msg         = 'warning';
            $error            = true;
            $redirect_to_view = true;
            break;
        }
    }
    break;
default:
    $action_msg       = MSG_ACTION_NOT_VALID;
    $type_msg         = 'warning';
    $error            = true;
    $redirect_to_view = true;
    break;
}

if (!$error) {
    // No error found !!!
    $redirect_to_view = true;
    
    switch ($_REQUEST['action']) {
    case 'add':
        /* ADD CALENDAR */
        //add a new host
        $dbUtil->dbExec($dbh, $dbUtil->setCalendar($label,
                                                   $calendar,
                                                   $id_system,
                                                   $shared_systems));
        $action_msg = MSG_ACTION_CALENDAR_ADDED;
        $type_msg   = 'success';
        break;
    case 'edit':
        /* EDIT CALENDAR */
        
            $dbUtil->dbExec($dbh, $dbUtil->updateCalendar($id_calendar,
                                                          $label,
                                                          $calendar,
                                                          $shared_systems));
        

        $action_msg = MSG_ACTION_CALENDAR_MODIFIED;
        $type_msg   = 'success';
        break;
    case 'delete':
        /* DELETE CALENDAR */
        foreach ($_REQUEST['id_chk'] as $id_calendar => $label) {
            $dbUtil->dbExec($dbh, $dbUtil->deleteCalendar($id_calendar));
        }
        $action_msg = MSG_ACTION_CALENDAR_DELETED;
        $type_msg   = 'success';
        break;
    case 'copy':
        foreach ($_REQUEST['id_chk'] as $id_calendar => $value) {
            $rows = $dbUtil->dbQuery($dbh,
                                     $dbUtil->getCalendarById($id_calendar),
                                     MDB2_FETCHMODE_ASSOC);

            $label     = $rows[0]['label'];
            $max_limit = true;
            for ($index = 1; $index < 100; $index++) {
                //check for existing copy_label
                $new_label = $label . '_copy_' . $index;

                $rows = $dbUtil->dbQuery($dbh, $dbUtil->checkExistsCalendar($new_label));
                if (!isset($rows[0][0])) {
                    $max_limit = false;
                    break;
                }
                unset($rows);
            }
            if ($max_limit) {
                $action_msg = MSG_ACTION_MAX_NUM_OF_COPY;
                $type_msg   = 'warning';
                break;
            } else {
                $dbUtil->dbExec($dbh, $dbUtil->copyCalendar($id_calendar,
                                                            $new_label));
                
                $action_msg = MSG_ACTION_CALENDAR_COPIED;
                $type_msg   = 'success';
            }
        }
        break;
    }
}

$dbh = $dbUtil->dbCloseConn($dbh);
unset($dbh);

$_SESSION['action_msg'] = $action_msg;
$_SESSION['type_msg']   = $type_msg;

if ($redirect_to_view) {
    unset($_REQUEST);
    header('location:?cat=calendars&mode=view');
    exit;    
}
?>