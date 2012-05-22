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

    //Create the form
    $form_export = new HTML_QuickForm('frm_export', 'post');
    $form_import = new HTML_QuickForm('frm_import', 'post');
    
    /* ACTION TOOLBAR */
    $toolbar = createToolbar(array('backPage'=>'default'));

    // The toolbar javascript is used below $toolbar['javascript'];
    
    $file_import =& $form_import->addElement('file', 'importFile', '', 'size=60');
    $form_import->addRule('importFile', 
                               MSG_SELECT_A_FILE, 
                               'required', 
                               '', 
                               'client');
    /* CHECKBOX FOR OVERWRITE THE ALREDY EXIST DABATASE */
    $f_check_dbae_export = $form_import->addElement('checkbox', 'check_dbae', 
        '', FIELD_OVERWRITE_EXISTING_DB);
    
    /* SUBMIT BUTTON */
    $f_submit_import = $form_import->addElement('submit', 
                                                'import', 
                                                FIELD_IMPORT_SUBMIT)->toHTML();
    $f_submit_export = $form_export->addElement('submit', 
                                                'export', 
                                                FIELD_EXPORT_SUBMIT)->toHTML();

    /* HIDDEN FIELDS */
    $f_hidden_export  = $form_export->createElement('hidden', 'action', 
        'export')->toHTML();
    $f_hidden_export .= $form_export->createElement('hidden', 'mode', 
        $_REQUEST['mode'])->toHTML();
    $f_hidden_export .= $form_export->createElement('hidden', 'cat', 
                                                        $_REQUEST['cat'])->toHTML();
    
    $f_hidden_import = 
        $form_import->createElement('hidden', 
                                         'action', 
                                         'import')->toHTML() .
        $form_import->createElement('hidden',
                                         'mode',
                                         $_REQUEST['mode'])->toHTML() .
        $form_import->createElement('hidden',
                                         'cat',
                                         $_REQUEST['cat'])->toHTML();

    //convert form in array for extract js and attributes
    $formArray_export = $form_export->toArray();
    $formArray_import = $form_import->toArray();
    echo $formArray_export['javascript'];
    echo $formArray_import['javascript'];
    ?>
    <div id="ortro-title">
    <?php echo IMPORT_EXPORT_TOP; ?>
    </div>
    <p>
    <?php echo IMPORT_EXPORT_TITLE; ?>
    </p>

    <form  <?php  echo $formArray_export['attributes']; ?> >
    <?php echo $f_hidden_export; ?>
    <div id="toolbar" class="ortro-table">
        <?php echo $toolbar['javascript']; ?>
        <?php echo $toolbar['header']; ?>
    </div>
    <br/>

    <div class="ortro-table">
        <?php 
             $table = new HTML_Table($table_attributes);
             $table->addRow(array(FIELD_EXPORT_DESCRIPTION), '', 'TH');
             $table->addRow(array(FIELD_EXPORT_SUB_DESCRIPTION), '', 'TD');
             $table->addRow(array($f_submit_export),
                            '', 
                            'TD', 
                            false);
             $table->display();
        ?>
    </div>
    </form>
    
    <form  <?php echo $formArray_import['attributes']; ?> >
    <?php echo $f_hidden_import; ?>
    <br/>
    <div class="ortro-table">
        <?php 
             $table = new HTML_Table($table_attributes);
             $table->addRow(array(FIELD_IMPORT_DESCRIPTION), '', 'TH');
             $table->addRow(array(FIELD_IMPORT_SUB_DESCRIPTION), '', 'TD');
             $table->addRow(array($file_import->toHtml() . 
                                  '<br/><br/>' . 
                                  $f_check_dbae_export->toHtml() . 
                                  '<br /><br />' . 
                                  $f_submit_import),
                            '', 
                            'TD', 
                            false);
             $table->display();
        ?>
    </div>
    </form>
    
    <?php
        if (isset($_SESSION['check_new_settings'])) {
            $check_new_setting = $_SESSION['check_new_settings'];
        }
        if ($check_new_setting == true) { ?>
        <form  <?php echo $formArray_import['attributes']; ?> >
        <?php echo $f_hidden_import; ?>
        <br/>
        <div class="ortro-table">
            <?php 
                $table = new HTML_Table($table_attributes);
                $table->addRow(array('<b>' . 
                              FIELD_CHECK_NEW_SETTINGS_DESCRIPTION . 
                              '</b>'), '', 'TH');
                $table->display();
                echo '<a class="important_link" href="index.php?mode=view&cat=settings">' . 
                    FIELD_CHECK_NEW_SETTINGS_LINK . 
                '</a>';
                $_SESSION['check_new_settings'] = false;
            ?>
        </div>
        </form>
    <?php } ?>

    <div id="toolbar_menu" class="ortro-table">
        <?php echo $toolbar['menu']; ?>
    </div>
    
    <?php 
}
?>