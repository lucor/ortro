<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to view the systems defined in Ortro
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

$dbUtil  = new DbUtil();
$dbh     = $dbUtil->dbOpenConnOrtro();
$systems = $dbUtil->dbQuery($dbh, $dbUtil->getSystems(), MDB2_FETCHMODE_ASSOC);
$dbh     = $dbUtil->dbCloseConn($dbh);
unset($dbh);

//Create the form
$form = new HTML_QuickForm('frm', 'post');
    
/* ACTION TOOLBAR */

$toolbar = createToolbar(array('backPage'=>'default',
                               'reload_page'=>'default',
                               'add'=>'default_admin',
                               'edit'=>'admin',
                               'lock'=>'admin',
                               'unlock'=>'admin',
                               'delete'=>'admin'));

// The toolbar javascript is used below $toolbar['javascript'];

/* HOSTS TABLE */

$table_page = new HTML_Table($table_attributes);
$checkbox   = $form->addElement('checkbox', 'id_chk_all', '', '');
$checkbox->updateAttributes(array('onclick' => 'checkAll(this.checked);'));
$f_chk_all = $checkbox->toHTML();
$table_page->addRow(array($f_chk_all, FIELD_JOB_STATUS, FIELD_SYSTEM), 
                    'colspan=1 align=center', 
                    'TH');

$admin_for_systems = array();
$guest_for_systems = array();

$id_system = '';

$admin_for_systems = array();
$guest_for_systems = array();

if (array_key_exists('ADMIN', $_policy)) {
    //Required for a fresh installation when no systems are defined
    $is_admin_for_a_system = true;
}

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
        $checkbox = $form->addElement('checkbox', 
                                      'id_chk[' . $key['id_system'] . ']', 
                                      '',
                                      '');
        $checkbox->updateAttributes(array('value' => $key['name']));
        $checkbox->updateAttributes(array('id' => 'id_chk'));
        $checkbox->updateAttributes(array('role' => $role));
        $f_chk = $checkbox->toHTML();
        
        $locked = false;
        switch ($key["status"]) {
        case 'L':
            $img_status = 'locked.png';
            $alt_status = TOOLTIP_LOCKED;
            $locked     = true;
            break;
        default:
            $img_status = 'success.png';
            $alt_status = '';
            break;
        }
        
        $table_page->addRow(array($f_chk, '<img src="img/' . $img_status  . '" border="0">', $key['name']), 
                            'class=c2 onmouseover=highlightRow(this)', 
                            'TD', 
                            true);
        $table_page->updateColAttributes(0, 'width=1%');
        $table_page->updateColAttributes(1, 'width=5% align=center');
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
$f_hidden .= $form->createElement('hidden', 
                                  'is_admin_for_a_system', 
                                  $is_admin_for_a_system)->toHTML();
//convert form in array for extact js and attributes
$formArray = $form->toArray();
?>
<div id="ortro-title">
  <?php echo SYSTEM_TOP; ?> 
</div>
<p>
<?php echo SYSTEM_TITLE; ?>
</p>
<?php echo $formArray['javascript']; ?>    
<form  <?php echo $formArray['attributes']; ?> >
<?php echo $f_hidden; ?>
<?php echo $toolbar['javascript']; ?>
<div id="toolbar" class="ortro-table">
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
