<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to view the database defined in Ortro
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
 
//handle filter values over session 
if (isset($_REQUEST['filter_reset_x'])) {
    unset($_SESSION['filter_db']);
} else {
    //define the filter fields to check
    $filter_array = array('filter_system');
    //valorize the session value
    for ($j = 0; $j < sizeof($filter_array); $j++) {
        if (!isset($_SESSION['filter_db'][$filter_array[$j]])) {
            $_SESSION['filter_db'][$filter_array[$j]] = '';
        }
        if (isset($_REQUEST[$filter_array[$j]]) && 
            $_REQUEST[$filter_array[$j]] != 
                $_SESSION['filter_db'][$filter_array[$j]]) {
            $_SESSION['filter_db'][$filter_array[$j]] = $_REQUEST[$filter_array[$j]];
        }
    }
}

$dbUtil       = new DbUtil();
$dbh          = $dbUtil->dbOpenConnOrtro();
$systems      = $dbUtil->dbQuery($dbh, $dbUtil->getSystems(), MDB2_FETCHMODE_ASSOC);
$systemHostDb = $dbUtil->dbQuery($dbh, 
                                  $dbUtil->getSystemHostDb($_SESSION['filter_db']
                                                           ['filter_system']), 
                                  MDB2_FETCHMODE_ASSOC);
$dbh          = $dbUtil->dbCloseConn($dbh);
unset($dbh);

//Create the form
$form = new HTML_QuickForm('frm', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default',
                               'reload_page'=>'default',
                               'add'=>'default_admin',
                               'details'=>'guest',
                               'edit'=>'admin',
                               'delete'=>'admin'));

// The toolbar javascript is used below $toolbar['javascript'];

/* JOB TABLE */        
$table_page = new HTML_Table($table_attributes);
$checkbox   = $form->addElement('checkbox', "id_chk_all", '', '');
$checkbox->updateAttributes(array('onclick' => 'checkAll(this.checked);'));
$f_chk_all = $checkbox->toHTML();
$table_page->addRow(array($f_chk_all,
                          FIELD_SYSTEM, 
                          FIELD_DATABASE, 
                          FIELD_HOSTNAME,
                          FIELD_IP), 
                    "colspan=1 align=center", 
                    'TH');
        
$system_name = '';

$admin_for_systems = array();
$guest_for_systems = array();

$select_filter_system['*'] = FILTER_ALL;
            
$id_system             = '';
$is_admin_for_a_system = false;

foreach ($systems as $key) {
    if (array_key_exists('ADMIN', $_policy) || 
        in_array($key['id_system'], 
                 explode(',', $_policy['SYSTEM_ADMIN']))
        ) {
        $admin_for_systems[$key['id_system']] = true;
        //Used to enable default actions for admin in the toolbar 
        //(see hidden fields below) 
        $is_admin_for_a_system = true;
    }
    if ($admin_for_systems[$key['id_system']] || 
        array_key_exists('GUEST', $_policy) || 
        in_array($key['id_system'], 
                 explode(',', $_policy['SYSTEM_GUEST']))
        ) {
        $guest_for_systems[$key['id_system']]    = true;
        $select_filter_system[$key['id_system']] = $key['name'];
    }
}

foreach ($systemHostDb as $key) {
    if ($key["id_db"] != 1) {
        $is_admin_for_system = false;
        $is_guest_for_system = false;
        $role                = '';

        if (array_key_exists($key['id_system'], $admin_for_systems)) {
            $is_admin_for_system = true;
            $role                = 'admin';
        }
        
        if (array_key_exists($key['id_system'], $guest_for_systems)) {
            $is_guest_for_system = true;
        }
                    
        if ($is_guest_for_system) {

            if ($id_system == '' || $id_system != $key['id_system']) {
                $table_page->addRow(array('&nbsp;', $key['name']), 
                                    'class=c3', 
                                    'TD', 
                                    true);
                $id_system = $key["id_system"];
            }
            $checkbox = $form->addElement('checkbox', 
                                          "id_chk[" . $key['id_db'] . "]", 
                                          '',
                                          '');
            $checkbox->updateAttributes(array('value' => $key['id_system']));
            $checkbox->updateAttributes(array('id' => 'id_chk'));
            $checkbox->updateAttributes(array('role' => $role));
            
            $f_chk = $checkbox->toHTML();
            
            $table_page->addRow(array($f_chk, 
                                      '&nbsp;',
                                      $key['db_label'], 
                                      $key['hostname'],
                                      $key['ip']), 
                                'class=c2 onmouseover=highlightRow(this)', 
                                'TD', 
                                true);
        }
    }
}
$table_page->updateColAttributes(0, 'width=1%');
        
/* FILTER BOX */
// Filter form fields
$f_filter_system_obj = $form->addElement('select', 
                                         'filter_system', 
                                         '', 
                                         $select_filter_system,
                                         'onchange="document.frm.submit();"');
$f_filter_system_obj->setSelected($_SESSION['filter_db']['filter_system']);
$f_filter_reset_obj = $form->addElement('image', 
                                        'filter_reset', 
                                        'img/undo.png', 
                                        'title="' . FILTER_RESET_TITLE . 
                                        '" onchange="document.frm.mode.value' . 
                                        '=\'\';document.frm.submit();"');

// Filter table
$table_filter = new HTML_Table($table_attributes);
$table_filter->addRow(array(FILTER), '', 'TH');
$table_filter->addRow(array(FILTER_SYSTEM . $f_filter_system_obj->toHTML() .
                            '&nbsp;&nbsp;' .$f_filter_reset_obj->toHTML()),
                      "align=left valign=top",
                      'TD', 
                      false);

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

$formArray = $form->toArray(); //convert form in array for extact js and attributes
?>
<div id="ortro-title">
   <?php echo DATABASES_TOP; ?>
</div>
<p>
<?php echo DATABASES_TITLE; ?>
</p>
<?php echo $formArray['javascript']; ?>    
<form  <?php echo $formArray['attributes']; ?> >
<?php echo $f_hidden; ?>
<div class="ortro-table">
    <?php $table_filter->display(); ?>
</div>
<br/>
<div id="toolbar" class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
</div>
<br/>
<div class="ortro-table">
    <?php $table_page->display(); ?>
</div>
<div id="toolbar_menu" class="ortro-table">
    <?php echo $toolbar['menu']; ?>
</div>
</form>