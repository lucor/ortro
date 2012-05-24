<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to view the users defined in Ortro
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
 
unset($_SESSION['filter_user']);
//handle filter values over session 
if (isset($_REQUEST['filter_reset_x'])) {
    unset($_SESSION['filter_user']);
} else {
    //define the filter fields to check
    $filter_array = array('filter_search', 'filter_text', 'filter_group');
    //valorize the session value
    for ($j = 0; $j < sizeof($filter_array); $j++) {
        if (!isset($_SESSION['filter_user'][$filter_array[$j]])) {
            $_SESSION['filter_user'][$filter_array[$j]] = '';
        }
        if (isset($_REQUEST[$filter_array[$j]]) && 
            $_REQUEST[$filter_array[$j]] != 
                $_SESSION['filter_user'][$filter_array[$j]]) {
            $_SESSION['filter_user'][$filter_array[$j]] = 
                $_REQUEST[$filter_array[$j]];
        }
    }
}

$dbUtil = new DbUtil();
$dbh    = $dbUtil->dbOpenConnOrtro();
$groups = $dbUtil->dbQuery($dbh, $dbUtil->getGroups(), MDB2_FETCHMODE_ASSOC);
$users  = $dbUtil->dbQuery($dbh, 
             $dbUtil->getUsers($_SESSION['filter_user']['filter_search'],
                               $_SESSION['filter_user']['filter_text'],
                               $_SESSION['filter_user']['filter_group']), 
                            MDB2_FETCHMODE_ASSOC);
$dbh    = $dbUtil->dbCloseConn($dbh);
unset($dbh);

//Create the form
$form = new HTML_QuickForm('frm', 'post');

$select_filter_group['*'] = FILTER_ALL;

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default',
                               'reload_page'=>'default',
                               'add'=>'default_admin',
                               'edit'=>'admin',
                               'userGroup'=>'admin',
                               'delete'=>'admin'));

// The toolbar javascript is used below $toolbar['javascript'];

/* USER TABLE */        
$table_page = new HTML_Table($table_attributes);
$checkbox   = $form->addElement('checkbox', 'id_chk_all', '', '');
$checkbox->updateAttributes(array('onclick' => 'checkAll(this.checked);'));
$f_chk_all = $checkbox->toHTML();
$table_page->addRow(array($f_chk_all, FIELD_USER, FIELD_USER_NAME,
                          FIELD_USER_MAIL, FIELD_USER_GROUP), 
                    'colspan=1 align=center', 'TH');
            
foreach ($groups as $key) {
    $select_filter_group[$key['groupname']] = $key['groupname'];
}
        
$id_user = '';
foreach ($users as $key) {
    
    if ($id_user == '' || $id_user != $key['id_user']) {
    
        $checkbox = $form->addElement('checkbox',
                                      'id_chk[' . $key['id_user'] . ']', 
                                      '',
                                      '');
        $checkbox->updateAttributes(array('value' => $key['type']));
        $checkbox->updateAttributes(array('id' => 'id_chk'));
        $checkbox->updateAttributes(array('role' => 'admin'));
        $f_chk = $checkbox->toHTML();
        
        $table_page->addRow(array($f_chk, $key['username'], 
                                  rawurldecode($key['name']), 
                                  $key['mail'], '&nbsp;'), 
                            'class=c3  onmouseover=highlightRow(this)', 'TD', true);
        $id_user = $key['id_user'];
        
    }
    if ($key['groupname'] != '' && $key['groupname'] != 'none') {
        $table_page->addRow(array('&nbsp;', '&nbsp;', '&nbsp;', '&nbsp;',
                                  $key['groupname']), 
                            'class=c2', 'TD', true);
    }        
}
$table_page->updateColAttributes(0, 'width=1%');


/* FILTER BOX */
// Filter form fields
$select_filter_search['username'] = FILTER_USERNAME;
$select_filter_search['name']     = FILTER_NAME;
$select_filter_search['mail']     = FILTER_MAIL;

$f_filter_search_obj = $form->addElement('select', 'filter_search', '', 
                                         $select_filter_search, '');
$f_filter_search_obj->setSelected($_SESSION['filter_user']['filter_search']);
$f_filter_groups_obj = $form->addElement('select', 'filter_group', '', 
                                         $select_filter_group, 
                                         'onchange="document.frm.submit();"');
$f_filter_groups_obj->setSelected($_SESSION['filter_user']['filter_group']);
$f_filter_text_obj = $form->addElement('text', 'filter_text', '', 
                                       'onchange="document.frm.submit();"');
$f_filter_text_obj->setValue($_SESSION['filter_user']['filter_text']);
$f_filter_apply_obj = $form->addElement('image', 'filter', 'img/filter.png', 
                                        'title="' . FILTER_APPLY_FILTER_TITLE . 
                                        '" onchange="document.frm.submit();"');
$f_filter_reset_obj = $form->addElement('image', 'filter_reset', 'img/undo.png', 
                                        'title="' . FILTER_RESET_TITLE . 
                                        '" onchange="document.frm.submit();"');

// Filter table
$table_filter = new HTML_Table($table_attributes);
$table_filter->addRow(array(FILTER), '', 'TH');        
$table_filter->addRow(array($f_filter_search_obj->toHTML() . 
                            $f_filter_text_obj->toHTML() . 
                            '&nbsp; ' . FILTER_GROUP . 
                            $f_filter_groups_obj->toHTML() .
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
$f_hidden .= $form->createElement('hidden', 'is_admin_for_a_system', '1')->toHTML();

$formArray = $form->toArray(); //convert form in array for extact js and attributes
?>
    <div id="ortro-title">
      <?php echo USER_TOP; ?>
    </div>
    <p>
      <?php echo USER_TITLE; ?>
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