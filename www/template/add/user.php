<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */


/**
 * Frontend page to add a user in Ortro
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

/* ACTION TOOLBAR */
$toolbar = createToolbar(array('backPage'=>'default'));
?>
<!-- start body -->
<div id="ortro-title">
    <?php echo USER_ADD_TOP; ?>
</div>

<p>
    <?php echo USER_ADD_TITLE; ?>
</p>
<div id="toolbar" class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
</div>
<br/>

<?php
if (is_file(ORTRO_CONF . 'configure_ldap.php')) {
    include_once ORTRO_CONF . 'configure_ldap.php';
}
if (isset ($_REQUEST['ldap_search'])) {
    include_once 'ldapUtil.php';
    switch ($_REQUEST['search_by']) {
    case '0':
        $filter = $GLOBALS['conf']['ldap']['search_attribute_cn'] . '=*' . 
                  $_REQUEST['searchField'] .'*';
        break;
    case '1':
        $filter = $GLOBALS['conf']['ldap']['search_attribute_uid'] . '=' . 
                  $_REQUEST['searchField'];
        break;
    case '2':
        $filter = $GLOBALS['conf']['ldap']['search_attribute_mail'] . '=*' . 
                  $_REQUEST['searchField'] .'*';
        break;
    }

    $ldapUtil              = new LdapUtil();
    $only_these_attributes = 
            array($GLOBALS['conf']['ldap']['search_attribute_uid'],
                  $GLOBALS['conf']['ldap']['search_attribute_cn'],
                  $GLOBALS['conf']['ldap']['search_attribute_mail']);
                  
    $results = $ldapUtil->ldapSearch($filter, $only_these_attributes);
    
    if ($results == 'error') {
        showMessage(MSG_LDAP_CONNECTION_FAILED, 'warning');
    } else if ($results["count"] == 0) {
        showMessage(MSG_LDAP_NO_MATCH_FOUND, 'warning');
    } else {    
        echo '<div class="ortro-table">';
        $table = new HTML_Table($table_attributes);
        $table->addRow(array(FIELD_USER,
                             FIELD_USER_NAME,
                             FIELD_USER_MAIL,
                             FIELD_USER_ACTION), 
                       'align=center', 
                       'TH');
        $search_attribute_uid  = $GLOBALS['conf']['ldap']['search_attribute_uid'];
        $search_attribute_cn   = $GLOBALS['conf']['ldap']['search_attribute_cn'];
        $search_attribute_mail = $GLOBALS['conf']['ldap']['search_attribute_mail'];
        for ($i = 0; $i < sizeof($results); $i++) {
            $uid  = '';
            $cn   = '';
            $mail = '';
            
            if (isset($results[$i][$search_attribute_uid][0])) {
                $uid = $results[$i][$search_attribute_uid][0];
            }
            if (isset($results[$i][$search_attribute_cn][0])) {
                $cn = $results[$i][$search_attribute_cn][0];
            }
            if (isset($results[$i][$search_attribute_mail][0])) {
                $mail = $results[$i][$search_attribute_mail][0];
            }
            
            if (strlen($uid)>0) {
                $form      = new HTML_QuickForm('frmAddUserLdap'. $i, 'post');
                $f_hidden  = $form->createElement('hidden', 
                                                  'mode', 
                                                  $_REQUEST['mode'])->toHTML();
                $f_hidden .= $form->createElement('hidden', 
                                                  'action', 
                                                  $_REQUEST['mode'])->toHTML();
                $f_hidden .= $form->createElement('hidden', 
                                                  'cat', 
                                                  $_REQUEST['cat'])->toHTML();
                $f_hidden .= $form->createElement('hidden', 
                                                  'search_by', 
                                                  $_REQUEST['search_by'])->toHTML();
                $f_hidden .= $form->createElement('hidden', 
                                                  'searchField', 
                                                $_REQUEST['searchField'])->toHTML();

                $f_html  = $form->addElement('hidden', 'type', 'LDAP')->toHTML();
                $f_html .= $form->addElement('hidden', 'username', $uid)->toHTML();
                $f_html .= $form->addElement('hidden', 'name', $cn)->toHTML();
                $f_html .= $form->addElement('hidden', 'mail', $mail)->toHTML();
                $f_html .= $form->addElement('image', 'Update', 'img/add.png', 
                                             'width=20px,height=20px')->toHTML();
                
                $formArray = $form->toArray();
                $temp_form = '<form ' . $formArray['attributes'] . '>' . $f_hidden . 
                             $f_html . '</form>';
                $table->addRow(array($uid, $cn, $mail, 
                                     '<center>' . $temp_form . '</center>'), 
                               'align=left valign=top class=c2 ' . 
                               'onmouseover=highlightRow(this)', 'TD', true);
            }
        }
        $table->display();
        echo '</div><br/>';
    }
} else {
    //Create the form
    $form       = new HTML_QuickForm('frm', 'post');
    $f_hidden   = $form->createElement('hidden', 
                                       'mode', 
                                       $_REQUEST['mode'])->toHTML();
    $f_hidden  .= $form->createElement('hidden', 
                                       'action', 
                                       $_REQUEST['mode'])->toHTML();
    $f_hidden  .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
    $f_username = $form->addElement('text', 'username', '', '')->toHTML();
    $form->addRule('username', MSG_USERNAME_REQUIRED, 'required', '', 'client');
    $f_password         = $form->addElement('password', 
                                            'password', 
                                            '', 
                                            '')->toHTML();
    $f_password_confirm = $form->addElement('password', 
                                            'password_confirm', 
                                            '', 
                                            '')->toHTML();
    $form->addRule('password', MSG_PASSWORD_REQUIRED, 'required', '', 'client');
    $form->addRule('password', MSG_PASSWORD_MIN_CHAR, 'minlength', 6, 'client');
    $form->addRule(array('password','password_confirm'), 
                   MSG_PASSWORD_NOT_MATCH, 'compare', null, 'client');
    $f_name = $form->addElement('text', 'name', '', '')->toHTML();
    $form->addRule('name', MSG_COMPLETE_NAME_REQUIRED, 'required', '', 'client');
    $f_mail = $form->addElement('text', 'mail', '', '')->toHTML();
    $form->addRule('mail', MSG_MAIL_REQUIRED, 'required', '', 'client');
    $form->addRule('mail', 
                   MSG_MAIL_NOT_VALID, 
                   'regex', 
                   '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+[a-zA-Z0-9]{2,4}$/', 
                   'client');
    $f_submit = $form->addElement('submit', 'Update', BUTTON_ADD)->toHTML();

    $formArray = $form->toArray(); 
    echo $formArray['javascript'];

    echo '<form ' . $formArray['attributes'] . '>';
    echo '<div class="ortro-table">';
    echo $f_hidden; //hidden field
    $table = new HTML_Table($table_attributes . ' class=c2');
    $table->addRow(array($f_username . '&nbsp;'.FIELD_USERNAME), 
                   'align=left valign=top class=c2', 'TD', false);
    $table->addRow(array($f_password . '&nbsp;'.FIELD_PASSWORD),
                   'align=left valign=top class=c2', 'TD', false);
    $table->addRow(array($f_password_confirm . '&nbsp;' . FIELD_CONFIRM_PASSWORD), 
                   'align=left valign=top class=c2', 'TD', false);
    $table->addRow(array($f_name . '&nbsp;' . FIELD_USER_COMPLETE_NAME), 
                   'align=left valign=top class=c2', 'TD', false);
    $table->addRow(array($f_mail . '&nbsp;' . FIELD_USER_MAIL), 
                   'align=left valign=top class=c2', 'TD', false);
    $table->addRow(array($f_submit), 
                   'align=left valign=top class=c2', 'TD', false);
    $table->display();
    echo '</div></form>';
}
    
if ($GLOBALS['conf']['auth']['default'] == 'LDAP') {
    $form_search    = new HTML_QuickForm('frm', 'post');
    $f_hidden       = $form_search->addElement('hidden', 
                                               'cat', 
                                               $_REQUEST['cat'])->toHTML();
    $f_hidden      .= $form_search->addElement('hidden', 
                                               'mode', 
                                               $_REQUEST['mode'])->toHTML();
    $f_search_field = $form_search->addElement('text', 
                                               'searchField', 
                                               '', 
                                               '')->toHTML();
    $form_search->addRule('searchField', 
                          MSG_SEARCH_FIELD_REQUIRED, 
                          'required', '', 'client');
    $search_type      = array(FIELD_USER_NAME, FIELD_USER_ID, FIELD_USER_MAIL);
    $f_search_type    = $form_search->addElement('select', 
                                                 'search_by', 
                                                 '', 
                                                 $search_type, 
                                                 '')->toHTML();
    $f_search_submit  = $form_search->addElement('submit', 
                                                 'ldap_search', 
                                                 BUTTON_SEARCH)->toHTML();
    $formArray_search = $form_search->toArray();

    echo '<form ' . $formArray_search['attributes'] . '>';
    echo '<div class="ortro-table">';
    echo $f_hidden;
    $table = new HTML_Table($table_attributes . ' class=c2');
    $table->addRow(array('<b>'.FIELD_USER_FIND_ON_LDAP.'</b>'), 
                   'align=left valign=top class=c2 colspan=2', 'TD', false);
    $table->addRow(array(FIELD_USER_SEARCH_FOR . ':&nbsp;' . $f_search_type), 
                   'align=left valign=top class=c2', 'TD', false);
    $table->addRow(array(FIELD_USER_SEARCH_STRING . ':&nbsp;' . $f_search_field), 
                   'align=left valign=top class=c2', 'TD', false);
    $table->addRow(array($f_search_submit), 
                   'align=left valign=top class=c2', 'TD', false);
    $table->display();
    echo '</div></form>';
}
?>