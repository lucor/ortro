<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to view the groups defined in Ortro
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
$groups  = $dbUtil->dbQuery($dbh, $dbUtil->getGroups(), MDB2_FETCHMODE_ASSOC);
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
                               'delete'=>'admin'));
// The toolbar javascript is used below $toolbar['javascript'];

/* GROUP TABLE */
$table_page = new HTML_Table($table_attributes);
$checkbox   = $form->addElement('checkbox', 'id_chk_all', '', '');
$checkbox->updateAttributes(array('onclick' => 'checkAll(this.checked);'));
$f_chk_all = $checkbox->toHTML();
$table_page->addRow(array($f_chk_all, FIELD_GROUP, FIELD_ROLE, FIELD_SYSTEMS),
                    'colspan=1 align=center', 
                    'TH');

$systems_array = array();
foreach ($systems as $sys) {
    $systems_array[$sys['id_system']] = $sys['name'];
}

foreach ($groups as $key) {

    if ($key['id_systems'] == '*') {
        $system_list = '*';
    } else {
        $id_system_array = explode(',', $key['id_systems']);
        $system_list     = '';
        foreach ($id_system_array as $id) {
            $system_list .= $systems_array[$id] . '<br/>';
        }
    }

    $checkbox = $form->addElement('checkbox',
                                  "id_chk[" . $key['id_group'] . "]", 
                                  '',
                                  '');
    $checkbox->updateAttributes(array('id' => 'id_chk'));
    $checkbox->updateAttributes(array('role' => 'admin'));
    $f_chk = $checkbox->toHTML();

    $table_page->addRow(array($f_chk,
                              $key['groupname'],
                              $key['label'],
                              '&nbsp;'), 
                        'class=c3  onmouseover=highlightRow(this)', 
                        'TD', 
                        true);
    $table_page->addRow(array('&nbsp;', '&nbsp;', '&nbsp;', $system_list),
                        'class=c2', 
                        'TD', 
                        true);
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
$f_hidden .= $form->createElement('hidden', 'is_admin_for_a_system', '1')->toHTML();

//convert form in array for extact js and attributes
$formArray = $form->toArray();

?>
<div id="ortro-title"><?php echo GROUP_TOP; ?></div>
<p><?php echo GROUP_TITLE; ?></p>
<?php echo $formArray['javascript']; ?>
<form <?php echo $formArray['attributes']; ?>><?php echo $f_hidden; ?>
<div id="toolbar" class="ortro-table"><?php echo $toolbar['javascript']; ?>
<?php echo $toolbar['header']; ?></div>
<br />
<div class="ortro-table"><?php $table_page->display(); ?></div>
<div id="toolbar_menu" class="ortro-table"><?php echo $toolbar['menu']; ?>
</div>
</form>
