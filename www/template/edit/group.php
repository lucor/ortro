<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to edit the group settings
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
 
$id_group = key($_REQUEST['id_chk']);

$dbUtil     = new DbUtil();
$dbh        = $dbUtil->dbOpenConnOrtro();
$group_info = $dbUtil->dbQuery($dbh, 
                                $dbUtil->getGroupInfoById($id_group),
                                MDB2_FETCHMODE_ASSOC);
$roles      = $dbUtil->dbQuery($dbh,
                                $dbUtil->getRoles(),
                                MDB2_FETCHMODE_ASSOC);
$systems    = $dbUtil->dbQuery($dbh,
                                $dbUtil->getSystems(),
                                MDB2_FETCHMODE_ASSOC);
$dbh        = $dbUtil->dbCloseConn($dbh);

//Create the form
$form = new HTML_QuickForm('frm', 'post');

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

$f_groupname_obj = $form->addElement('text', 'groupname', '', '');
$f_groupname_obj->setValue($group_info[0]['groupname']);
$f_groupname = $f_groupname_obj->toHTML();
$form->addRule('groupname', MSG_GROUP_NAME_REQUIRED, 'required', '', 'client');

foreach ($roles as $key) {
    $select_role[$key["id_role"]] = $key["label"];
}

$f_role_obj = $form->addElement('select', 'id_role', '', 
                                $select_role, 
                                'onchange="disableMultiSelSystems(this.value);"');
$f_role_obj->setSelected($group_info[0]['id_role']);
$f_role = $f_role_obj->toHTML();

foreach ($systems as $key) {
    $select_system[$key["id_system"]] = $key["name"];
}


$f_system_obj = $form->addElement('select', 'id_systems', '', 
                                  $select_system, 'id=id_systems_sel multiple');
$f_system_obj->setSelected(explode(',', $group_info[0]['id_systems']));

if ($group_info[0]['id_role'] == '1' || $group_info[0]['id_role'] == '3') {
    //ADMIN or GUEST
    $f_system_obj->setAttribute('disabled', 'true');
}

$form->registerRule('checkMultiSelect', 'callback', 'checkMultiSelect');
$form->addRule('id_role', MSG_SELECT_A_VALUE_FOR_SYSTEM, 
               'checkMultiSelect', 'id_systems_sel', 'client');

$f_system = $f_system_obj->toHTML();

/* SUBMIT BUTTON */
$f_submit = $form->addElement('submit', 'Update', BUTTON_APPLY)->toHTML();

/* HIDDEN FIELDS */
$f_hidden  = $form->createElement('hidden', 'action', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
$f_hidden .= $form->createElement('hidden', 'id_group', $id_group)->toHTML();

?>
<!-- start body -->
<div id="ortro-title">
 <?php echo GROUP_EDIT_TOP; ?>
</div>    
<p>
 <?php echo GROUP_EDIT_TITLE; ?>
</p>

<?php 
    $formArray = $form->toArray();
    echo $formArray['javascript'];
?>
<form  <?php echo $formArray['attributes']; ?> >
<div id="toolbar" class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
</div>
<br/>
<div class="ortro-table">
<?php 
 echo $f_hidden; //hidden field
 
 $table = new HTML_Table($table_attributes . ' class=c2');
 $table->addRow(array ($f_groupname . '&nbsp;' . FIELD_GROUP_NAME), 
                       'align=left valign=top class=c2', 'TD', false);
 $table->addRow(array ($f_role . '&nbsp;' . FIELD_ROLE), 
                       'align=left valign=top class=c2', 'TD', false);
 $table->addRow(array ($f_system . '&nbsp;' . FIELD_SYSTEMS), 
                       'align=left valign=top class=c2', 'TD', false);
 $table->addRow(array($f_submit), 'align=left valign=top class=c2', 'TD', false);
 $table->display();
?>
</div>
</form>