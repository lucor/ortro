<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to edit the users - group settings
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

if (isset($_REQUEST['id_chk'])) {
    $id_user = key($_REQUEST['id_chk']);
} else {
    $id_user = $_REQUEST['id_user'];
}

$dbUtil         = new DbUtil();
$dbh            = $dbUtil->dbOpenConnOrtro();
$userInGroup    = $dbUtil->dbQuery($dbh, 
                                    $dbUtil->getGroupUser($id_user, 'IN'), 
                                    MDB2_FETCHMODE_ASSOC);
$userNotInGroup = $dbUtil->dbQuery($dbh, 
                                    $dbUtil->getGroupUser($id_user, 'NOT IN'), 
                                    MDB2_FETCHMODE_ASSOC);
$dbh            = $dbUtil->dbCloseConn($dbh);

//Create the form Add
$formAdd   = new HTML_QuickForm('frmUserGroupAdd', 'post');
$f_hidden  = $formAdd->createElement('hidden', 'mode', $_REQUEST['mode'])->toHTML();
$f_hidden .= $formAdd->createElement('hidden', 'cat', 'user')->toHTML();
$f_hidden .= $formAdd->createElement('hidden', 'action', 'addToGroup')->toHTML();
$f_hidden .= $formAdd->createElement('hidden', 'id_user', $id_user)->toHTML();

foreach ($userNotInGroup as $key) {
    if ($key["id_group"] != '1') {
        $select_userNotInGroup[$key["id_group"]] = $key["groupname"];
    }
}

$f_userNotInGroup_obj = $formAdd->addElement('select', 
                                             'id_groups', 
                                             '', 
                                             $select_userNotInGroup, 
                                             'multiple row=10 id=addGroup');
$f_userNotInGroup     = $f_userNotInGroup_obj->toHTML();

$f_submit = $formAdd->addElement('submit', 'Update', BUTTON_ADD_IN_GROUP)->toHTML();
    
$formAddArray = $formAdd->toArray();
$formAddHtml  =  $formAddArray['javascript'] . 
                 '<form' . $formAddArray['attributes'] . 
                 ' onsubmit="return checkMultiSelectNormal(\'addGroup\',\'' . 
                 MSG_SELECT_A_VALUE_FOR_GROUP . '\');">';
$formAddHtml .= $f_hidden;

$table = new HTML_Table($table_attributes . 'align=center class=c2');
$table->addRow(array($f_userNotInGroup), 'align=center class=c2', 'TD', false);
$table->addRow(array($f_submit ), 'align=center class=c2', 'TD', false);
$formAddHtml .= $table->toHTML();
$formAddHtml .= '</form>';

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));

//Create the form remove
$formRemove = new HTML_QuickForm('frmUserGroupDel', 'post');
$f_hidden   = $formRemove->createElement('hidden', 
                                         'mode', 
                                         $_REQUEST['mode'])->toHTML();
$f_hidden  .= $formRemove->createElement('hidden', 'cat', 'user')->toHTML();
$f_hidden  .= $formRemove->createElement('hidden', 
                                         'action', 'delFromGroup')->toHTML();
$f_hidden  .= $formRemove->createElement('hidden', 'id_user', $id_user)->toHTML();

$select_userInGroup = array();
foreach ($userInGroup as $key) {
    if ($key["id_group"] != '1') {
        $select_userInGroup[$key["id_group"]] = $key["groupname"];
    }
}

$f_userInGroup_obj = $formRemove->addElement('select', 
                                             'id_groups', 
                                             '', 
                                             $select_userInGroup, 
                                             'multiple row=10  id=delGroup');
$f_userInGroup     = $f_userInGroup_obj->toHTML();
$f_submit          = $formRemove->addElement('submit', 
                                             'Update', 
                                             BUTTON_REMOVE_FROM_GROUP)->toHTML();    
$formRemoveArray   = $formRemove->toArray();
$formRemoveHtml    = '<form' . $formRemoveArray['attributes'] . 
                     ' onsubmit="return checkMultiSelectNormal(\'delGroup\',\'' . 
                     MSG_SELECT_A_VALUE_FOR_GROUP . '\');">'; 
$formRemoveHtml   .= $f_hidden;

$table = new HTML_Table($table_attributes . 'align=center class=c2');
$table->addRow(array ($f_userInGroup), 'align=center class=c2', 'TD', false);
$table->addRow(array ($f_submit ), 'align=center class=c2', 'TD', false);

$formRemoveHtml .= $table->toHTML();
$formRemoveHtml .= '</form>';
?>         
<!-- start body -->
<div id="ortro-title">
<?php echo USER_GROUP_EDIT_TOP; ?>
</div>    
<p>
<?php echo USER_GROUP_EDIT_TITLE; ?>
</p>
<div id="toolbar" class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
</div>
<br/>
<div class="ortro-table">
<?php 
 $table = new HTML_Table($table_attributes . 'align=center valign=top class=c2');
 $table->addRow(array('<center>' . FIELD_USER_AVAILABLE_GROUPS . '</center>', 
                      '<center>' . FIELD_USER_MEMBER_GROUPS . '</center>'), 
                "align=center", 'TH');
 $table->addRow(array($formAddHtml, $formRemoveHtml), 
                'align=center valign=top class=c2', 'TD', false);
 $table->display();
?>
</div>
</form>