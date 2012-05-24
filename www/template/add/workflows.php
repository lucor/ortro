<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page to add a workflow in Ortro
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

$dbUtil    = new DbUtil();
$dbh       = $dbUtil->dbOpenConnOrtro();
$systemJob = $dbUtil->dbQuery($dbh, $dbUtil->getSystemJob(), MDB2_FETCHMODE_ASSOC);
$workflows = $dbUtil->dbQuery($dbh, $dbUtil->getWorkflows(), MDB2_FETCHMODE_ASSOC);
$jobsLabel = $dbUtil->dbQuery($dbh, $dbUtil->getJobsLabel(), MDB2_FETCHMODE_ASSOC);
$dbh       = $dbUtil->dbCloseConn($dbh);
unset($dbh);

//Create the form
$form = new HTML_QuickForm('frm', 'post');

/* SYSTEM SYSTEM JOB AND WORKFLOW SELECT FIELDS */
//create the advancede select list for system->jobs                

$select1[0]    = FIELD_SELECT_SYSTEM;
$select1[0][0] = FIELD_SELECT_LABEL;
$select2[0][0] = FIELD_SELECT_LABEL;

$id_host = '';
foreach ($systemJob as $key) {
    if (array_key_exists('ADMIN', $_policy) ||  
        in_array($key['id_system'], 
                 explode(',', $_policy['SYSTEM_ADMIN']))) {
        $select1[$key["id_system"]]                 = $key["name"];
        $select2[$key["id_system"]][$key["id_job"]] = $key["label"];
    }
}

// Check if there are jobs defined in ortro
if (count($select2) == 1) {
    //No jobs are defined    
    showMessage(MSG_ADD_WORKFLOW_JOB_IS_REQUIRED, 'warning');
} else {

    $f_hidden_rule_field = $form->addElement('hidden', 'rule', 'rule')->toHTML();
    
    $sel = & $form->addElement('hierselect', 'systemJob', '');
    $sel->setOptions(array($select1, $select2));
    $form->registerRule('checkHier', 'callback', 'checkHier');
    $form->addRule('rule', MSG_SELECT_A_WORKFLOW_OR_JOB, 
                   'checkHier', 'systemJob[0]', 'client');
    
    $f_systemJob = $sel->toHTML();
    
    /* CRONTAB FIELDS */
    $crontab_array = createCrontabHtml($form, '', 'workflow');
    
    /* Workflow properties */
    $f_label = $form->addElement('text', 'label', '', 'size=50')->toHTML();
    $form->addRule('label', MSG_LABEL_REQUIRED, 'required', '', 'client');
    
    $f_description = $form->addElement('textarea', 
                                       'description', 
                                       '', 
                                       'id=description rows=4 cols=50')->toHTML();
    
    /* SUBMIT BUTTON */
    $f_submit = $form->addElement('submit', 'Update', BUTTON_ADD)->toHTML();
    
    /* HIDDEN FIELDS */
    $f_hidden  = $form->createElement('hidden', 
                                      'action', 
                                      $_REQUEST['mode'])->toHTML();
    $f_hidden .= $form->createElement('hidden', 
                                      'mode', 
                                      $_REQUEST['mode'])->toHTML();
    $f_hidden .= $form->createElement('hidden', 'cat', $_REQUEST['cat'])->toHTML();
    $f_hidden .= $f_hidden_rule_field;
    $formArray = $form->toArray();
    echo $formArray['javascript'];
    
    ?>
        
    <!-- start body -->
    <div id="ortro-title">
    <?php echo WORKFLOW_ADD_TOP; ?>
    </div>    
    <p>
    <?php echo WORKFLOW_ADD_TITLE; ?>
    </p>
    <form  <?php echo $formArray['attributes']; ?> >
    <div class="ortro-table">
    
    <?php
     //Hidden fields
     echo $f_hidden; //hidden field
     
     $table = new HTML_Table($table_attributes);
     $table->addRow(array(FIELD_LABEL), '', 'TH');
     $table->addRow(array($f_label), 'align=left valign=top', 'TD', false);
     $table->addRow(array(FIELD_DESCRIPTION), '', 'TH');
     $table->addRow(array($f_description), 'align=left valign=top', 'TD', false);
     $table->display();
     echo $crontab_array['html'];
     $table = new HTML_Table($table_attributes);
     $table->addRow(array(FIELD_WORKFLOW_STEP,FIELD_WORKFLOW_JOB_TO_EXEC), 
                    '', 
                    'TH');
     $table->addRow(array('1',$f_systemJob), '', 'TD', false);
     $table->display();
     $table = new HTML_Table($table_attributes);
     $table->addRow(array($f_submit), "", 'TD', false);
     $table->display();
    ?>
    
    </div>
    </form>
    <?php
}
?>