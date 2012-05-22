<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to view the plugins notification defined in Ortro
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
$jobType = $dbUtil->dbQuery($dbh, 
                             $dbUtil->getNotifyTypeList(), 
                             MDB2_FETCHMODE_ASSOC);
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

$table_page = new HTML_Table($table_attributes);
$checkbox   = $form->addElement('checkbox', 'id_chk_all', '', '');
$checkbox->updateAttributes(array('onclick' => 'checkAll(this.checked);'));
$f_chk_all = $checkbox->toHTML();
$table_page->addRow(array($f_chk_all,FIELD_PLUGIN,FIELD_DESCRIPTION,
                          FIELD_VERSION,FIELD_AUTHOR), 
                    'colspan=1 align=center', 'TH');

$role              = 'admin';
$plugin_type_count = 0;
foreach ($jobType as $key) {
    $plugin_type_count++;    
    $cfg_file = ORTRO_NOTIFICATION_PLUGINS . $key['label'] . '/configure.php';
    if (is_file($cfg_file)) {
        // Include the plugin language definition
        i18n('notification', $key["label"]);
        include_once $cfg_file;
        
        $checkbox = $form->addElement('checkbox', 
                                      "id_chk[" . $key['id_notify_type'] . "]", 
                                      '',
                                      '');
        $checkbox->updateAttributes(array('value' => $key['label']));
        $checkbox->updateAttributes(array('id' => 'id_chk'));
        $checkbox->updateAttributes(array('role' => $role));
                
        $f_chk = $checkbox->toHTML();
        
        $authors       = '';
        $authors_array = $plugin_field[$key['label']][0]['authors'];
        for ($index = 0; $index < sizeof($authors_array); $index++) {
            preg_match('/(.*)<(.*)>/', $authors_array[$index], $authors_infos);
            $author_name = trim($authors_infos[1]);
            $author_mail = trim($authors_infos[2]);
            $authors    .= $author_name . '<br/>';
        }
        
        $table_page->addRow(array($f_chk,
                                  $plugin_field[$key['label']][0]['title'],
                                  $plugin_field[$key['label']][0]['description'],
                                  '<center>' . 
                                  $plugin_field[$key['label']][0]['version'] . 
                                  '</center>' ,
                                  $authors), 
                             'class=c2 onmouseover=highlightRow(this)', 'TD', true);
    }
}

$table_page->updateColAttributes(0, 'valign=top width=1%');
$table_page->updateColAttributes(1, 'valign=top width=25%');
$table_page->updateColAttributes(2, 'valign=top width=59%');
$table_page->updateColAttributes(3, 'valign=top width=5%');
$table_page->updateColAttributes(4, 'valign=top width=15%');

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
<div id="ortro-title">
   <?php echo NOTIFICATION_PLUGIN_TOP; ?>
</div>
<p>
   <?php echo NOTIFICATION_PLUGIN_TITLE; ?>
</p>
<?php echo $formArray['javascript']; ?>    
<form  <?php echo $formArray['attributes']; ?> >
<?php echo $f_hidden; ?>
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
