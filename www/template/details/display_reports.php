<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Allows to display and download created reports
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

$id_system   = $_REQUEST['id_system'];
$id_job      = $_REQUEST['id_job'];
$plugin_type = $_REQUEST['plugin_type'];
$report_path = ORTRO_REPORTS . $id_system . DIRECTORY_SEPARATOR . $id_job;
$html        = '';
$error       = false;

if (!is_dir($report_path)) {
    //cannot open dir
    showMessage(MSG_REPORTS_NOT_AVAILABLE, 'warning');
    $error                  = true;
} else {
    $handle = opendir($report_path);
    $a      = array();
    if ($handle) {
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $complete_path = $report_path . DIRECTORY_SEPARATOR . $file;
                array_push($a, array('filename' => $file, 
                                     'date' => filemtime($complete_path), 
                                     'size' => filesize($complete_path)));
            }
        }
    }
    
    //define the order by method display
    //default order by date desc
    
    if (!isset($_REQUEST['orderby'])) {
        $_SESSION['orderby']     = 'date';
        $_SESSION['orderbymode'] = SORT_DESC;
    } else {
        if ($_REQUEST['orderby'] == $_SESSION['orderby']) {
            if ($_SESSION['orderbymode'] == SORT_ASC) {
                $_SESSION['orderbymode'] = SORT_DESC;
            } else {
                $_SESSION['orderbymode'] = SORT_ASC;
            }
        } else {
            $_SESSION['orderbymode'] = SORT_ASC;
        }
        $_SESSION['orderby'] = $_REQUEST['orderby'];
    }
    
    if ($_SESSION['orderbymode'] == SORT_ASC) {
        $img_arrow = '<img src="img/arrowdown.png" alt="" border="0"/>';
    } else {
        $img_arrow = '<img src="img/arrowup.png" alt="" border="0"/>';
    }
    
    $b = array();    
    foreach ($a as $key=>$value) {
        $b[$key] = $value[$_SESSION['orderby']];
    }
    
    $img_filename = '';
    $img_date     = '';
    $img_size     = '';
    
    switch ($_SESSION['orderby']) {
    case 'date':
        $img_date  = $img_arrow;
        $sort_type = SORT_NUMERIC;
        break;
    case 'filename':
        $img_filename = $img_arrow;        
        $sort_type    = SORT_STRING;
        break;
    case 'size':
        $img_size  = $img_arrow;
        $sort_type = SORT_NUMERIC;
        break;
    }
    array_multisort($b, $_SESSION['orderbymode'], $sort_type, $a);
    
    $form      = new HTML_QuickForm('frmActionPlugin', 'post');
    $formArray = $form->toArray();
    
    $plugin_action_html = 
        $form->addElement('hidden', 'cat', 'plugins')->toHTML() . 
        $form->addElement('hidden', 'file', $_REQUEST['file'])->toHTML() .
        $form->addElement('hidden', 'id_system', $id_system)->toHTML() .
        $form->addElement('hidden', 'id_job', $id_job)->toHTML() .
        $form->addElement('hidden', 'plugin_type', $plugin_type)->toHTML() .
        $form->addElement('hidden', 'mode', 'detail')->toHTML() .
        $form->addElement('hidden', 'download', '', 'id=download')->toHTML() .
        $form->addElement('hidden', 'orderby', '', 'id=orderby')->toHTML();

    //create the input button
    $f_submit_file_name = 
        $form->addElement('submit', 
                          'order', 
                          BUTTON_ORDER_BY_FILENAME,
                          'onclick=(document.frmActionPlugin.orderby.value=' . 
                          '\'filename\');')->toHTML() . $img_filename;
    $f_submit_date      = 
        $form->addElement('submit', 
                          'order', 
                          BUTTON_ORDER_BY_DATE,
                          'onclick=(document.frmActionPlugin.orderby.value=' .
                          '\'date\');')->toHTML() . $img_date;
    $f_submit_size      = 
        $form->addElement('submit',
                          'order', 
                          BUTTON_ORDER_BY_SIZE,
                          'onclick=(document.frmActionPlugin.orderby.value=' .
                          '\'size\');')->toHTML() . $img_size;
    
    $table_attributes = 'cellpadding=0 cellspacing=0 border=0 width=100%';
    $table            = new HTML_Table($table_attributes);
    $table->addRow(array($f_submit_file_name,
                         $f_submit_date,$f_submit_size), 
                   'class="ortro-input"', 
                    'TH');
    
    foreach ($a as $key=> $value) {
        $table->addRow(array(createHref('javascript:download(\'' . 
                                        $value['filename'] . '\');',
                             MSG_DOWNLOAD, $value['filename'],
                             ''),
                             date($GLOBALS['conf']['env']['dateFormat'] . ' ' . 
                                  $GLOBALS['conf']['env']['timeFormat'],
                                  $value['date']),
                             resizeBytes($value['size'])),
                        '', 'TD', false);
    }
    $html .= $table->toHTML();
}
?>
<!-- start body -->
<?php 
if (!$error) { 
    ?>
    <div id="ortro-title">
    <?php echo DISPLAY_REPORTS_TOP;?>
    </div>
    <script language="JavaScript" type="text/javascript">
        function download(filename){
            var frm = document.getElementById("frmActionPlugin");
            frm.download.value = filename;
            frm.submit();
            frm.download.value = '';
        }
    </script>
    <form  <?php echo  $formArray['attributes']; ?>>
    <div class="ortro-table">
    <?php 
        echo $plugin_action_html;
        echo $html;
    ?>
    </div>
    </form>
    <?php
}
?>