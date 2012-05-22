<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page allows to upload files in in Ortro
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

//Checks if file upload is enabled.
if (!ini_get('file_uploads')) {
    showMessage(UPLOAD_DISABLED, 'warning');
} else {

    $dbUtil  = new DbUtil();
    $dbh     = $dbUtil->dbOpenConnOrtro();
    $systems = $dbUtil->dbQuery($dbh, $dbUtil->getSystems(), MDB2_FETCHMODE_ASSOC);
    $dbh     = $dbUtil->dbCloseConn($dbh);
    unset($dbh);

    //Create the form
    $form = new HTML_QuickForm('frm', 'post');
    
    $form_file_upload = new HTML_QuickForm('frm_file_upload', 'post');
    
    /* ACTION TOOLBAR */
    $toolbar = createToolbar(array('backPage'=>'default'));
    
    // The toolbar javascript is used below $toolbar['javascript'];
    
    /* SYSTEMS */
    //Create the select list for System
    $select_system['0'] = '---';
    foreach ($systems as $key) {
        if (array_key_exists('ADMIN', $_policy) ||  
            in_array($key['id_system'], 
                     explode(',', $_policy['SYSTEM_ADMIN']))
            ) {
            $select_system[$key["id_system"]] = $key["name"];
        }
    }
            
    $f_select_system = 
        $form->addElement('select', 
                          'id_system', 
                          '', 
                          $select_system, 
                          'onchange="document.frm_file_upload.id_system.value' .
                          '=this.value;"')->toHTML();
    $form->addRule('id_system', MSG_SELECT_A_SYSTEM, 'nonzero', null, 'client');
    
    $file =& $form_file_upload->addElement('file', 'filename', '', 'size=60');
    $form_file_upload->addRule('filename', 
                               MSG_SELECT_A_FILE, 
                               'required', 
                               '', 
                               'client');
    
    /* SUBMIT BUTTON */
    $f_submit_file_upload = $form_file_upload->addElement('submit', 
                                                          'Update', 
                                                          BUTTON_ADD)->toHTML();
    $f_submit             = $form->addElement('submit', 
                                              'file_upload', 
                                              FIELD_FILE_MANAGER)->toHTML();
    
    /* HIDDEN FIELDS */
    $f_hidden  = $form->createElement('hidden', 'action', '')->toHTML();
    $f_hidden .= $form->createElement('hidden', 'mode', 'edit')->toHTML();
    $f_hidden .= $form->createElement('hidden', 'system_name', '')->toHTML();
    $f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
    
    $f_id_system_obj = $form_file_upload->addElement('hidden', 'id_system', '0');
    $form_file_upload->addRule('id_system', 
                               MSG_SELECT_A_SYSTEM,
                               'nonzero',
                               null,
                               'client');
    
    $f_hidden_file_upload = 
        $f_id_system_obj->toHTML() .
        $form_file_upload->createElement('hidden', 
                                         'action', 
                                         'add')->toHTML() .
        $form_file_upload->createElement('hidden',
                                         'mode',
                                         $_REQUEST['mode'])->toHTML() .
        $form_file_upload->createElement('hidden',
                                         'cat',
                                         $_REQUEST['cat'])->toHTML();
    
    //convert form in array for extract js and attributes
    $formArray             = $form->toArray();
    $formArray_file_upload = $form_file_upload->toArray();
    echo $formArray['javascript'];
    echo $formArray_file_upload['javascript'];
    ?>
    <div id="ortro-title">
    <?php echo FILE_MANAGER_TOP; ?>
    </div>
    <p>
    <?php echo FILE_MANAGER_TITLE; ?>
    </p>

    <form  <?php  echo $formArray['attributes']; ?> >
    <?php echo $f_hidden; ?>
    <div id="toolbar" class="ortro-table">
        <?php echo $toolbar['javascript']; ?>
        <?php echo $toolbar['header']; ?>
    </div>
    <br/>
    <div class="ortro-table">
        <?php 
             $table = new HTML_Table($table_attributes);
             $table->addRow(array(FIELD_SYSTEM), '', 'TH');
             $table->addRow(array($f_select_system . '&nbsp;&nbsp;' . $f_submit), 
                                  '', 
                                  'TD', 
                                  false);
             $table->display();
        ?>
    </div>
    </form>
    
    <form  <?php echo $formArray_file_upload['attributes']; ?> >
    <?php echo $f_hidden_file_upload; ?>
    <br/>
    <div class="ortro-table">
        <?php 
             $table = new HTML_Table($table_attributes);
             $table->addRow(array(FIELD_UPLOAD_FILE), '', 'TH');
             $table->addRow(array($file->toHtml() . 
                                  '<br/><br/>' . 
                                  $f_submit_file_upload),
                            '', 
                            'TD', 
                            false);
             $table->display();
        ?>
    </div>
    </form>
    
    <div id="toolbar_menu" class="ortro-table">
        <?php echo $toolbar['menu']; ?>
    </div>
    <?php 
} 
?>