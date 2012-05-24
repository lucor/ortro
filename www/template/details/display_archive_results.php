<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Allows to display the archived job results
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

require_once 'cronUtil.php';

unset ($chart);

$create_report = false;
$error         = false;

$dbUtil         = new DbUtil();
$dbh            = $dbUtil->dbOpenConnOrtro();
$job_results    = $dbUtil->dbQuery($dbh,
                    $dbUtil->getArchivedJobResults($_REQUEST['id_job'], 'asc'), 
                                    MDB2_FETCHMODE_ASSOC);
$job_basic_info = $dbUtil->dbQuery($dbh, 
                    $dbUtil->getJobBasicInfo($_REQUEST['id_job']), 
                                    MDB2_FETCHMODE_ASSOC);
$dbh            = $dbUtil->dbCloseConn($dbh);
unset($dbh);

if (count($job_results) > 0) {
    $create_report = true;
} else {
    //cannot open dir
    showMessage(MSG_ARCHIVED_RESULTS_NOT_AVAILABLE . 
                $job_basic_info[0]['label'], 'warning');
    $error                  = true;
} 
    
if (!function_exists('gd_info')) {
    i18n('template', 'action_msg.php');
    showMessage(MSG_GD_LIB_NOT_ENABLED, 'warning');
    $create_report          = false;
}

if (array_key_exists('ADMIN', $_policy) || 
    in_array($_REQUEST['id_system'], 
             explode(',', $_policy['SYSTEM_ADMIN']))) {
    $admin_for_systems[$_REQUEST['id_system']] = true;
}

if ($admin_for_systems[$_REQUEST['id_system']] || 
    array_key_exists('GUEST', $_policy) || 
    in_array($_REQUEST['id_system'], 
             explode(',', $_policy['SYSTEM_GUEST']))) {
    $guest_for_systems[$_REQUEST['id_system']] = true;
}

if (array_key_exists($_REQUEST['id_system'], $guest_for_systems)) {    
    $table_page = new HTML_Table($table_attributes);
    $table_page->addRow(array(MSG_JOB_ARCHIVED_RESULT,
                              MSG_JOB_ARCHIVED_EXIT_CODE,
                              MSG_JOB_ARCHIVED_START_EXEC,
                              MSG_JOB_ARCHIVED_END_EXEC), 
                        'colspan=1 align=center', 
                        'TH');

    foreach ($job_results as $key) {
        
        $msg_exec = str_replace("\n", '<br/>', $key['msg_exec']);   
            
        if ($key["start_exec"] == '0') {
            $start_exec = '-';
        } else {
            $start_exec = date('Y-m-d H:i', $key["start_exec"]);
        }
        
        if ($key["end_exec"] == '0') {
            $end_exec = '-';
        } else {
            $end_exec = date('Y-m-d H:i', $key["end_exec"]);
        }
            
        //Create report only if all msg_exec values are numeric
        //otherwise store the exit code
        if (!is_numeric($msg_exec)) {
            $msg_exec_data = $key["status_exec"];
        } else {
            $msg_exec_data = $msg_exec;
        }

        if ($key["status_exec"] == '1') {
            $img_status_exec = 'success.png';
        } else {
            $img_status_exec = 'warning.png';
        }
        
        // costruzione dell'array
        $chart_data[] = '["' . $start_exec . '", ' . $msg_exec_data . ']';
        
        $img_status_exec_html = '<img src="img/' . 
                                $img_status_exec  . 
                                '" border="0">';
                
        $table_page->addRow(array(rawurldecode($msg_exec),
                             '<center>' . $img_status_exec_html . '</center>',
                             '<center>' . $start_exec .'</center>',
                             '<center>' . $end_exec .'</center>'), 
                             'class=c2 onmouseover=highlightRow(this)', 
                             'TD', true);
    }
}
?>
<!-- start body -->
<?php if (!$error) { ?>
    <div id="ortro-title">
    <?php echo DISPLAY_ARCHIVED_RESULTS_TOP . ' ' . $job_basic_info[0]['label']; ?>
    </div>
    <?php 
    if ($create_report) { ?>
        
        <?php
            //Generate chart data
            $chart_title = $job_basic_info[0]['label'];
            $chart_data_js = '[' . implode(',', $chart_data) . ']';
        ?>
        
        <div id="chartdiv" style="height:400px;width:95%;"></div>
        <script type="text/javascript" src="js/jqplot/jqplot.canvasTextRenderer.min.js"></script>
        <script type="text/javascript" src="js/jqplot/jqplot.canvasAxisTickRenderer.min.js"></script>
        <script type="text/javascript" src="js/jqplot/jqplot.categoryAxisRenderer.min.js"></script>
        <script type="text/javascript" src="js/jqplot/jqplot.barRenderer.min.js"></script>

        <script>
            var data = <?php echo $chart_data_js; ?>;
            
            plot = $.jqplot('chartdiv', [data], {
                title:'<?php echo $chart_title; ?>',
                series:[
                    {label:'Archive results', 
                     renderer:$.jqplot.BarRenderer}
                ],
                axes:{
                    xaxis:{renderer:$.jqplot.CategoryAxisRenderer,
                            rendererOptions:{tickRenderer:$.jqplot.CanvasAxisTickRenderer},
                            tickOptions:{
                                angle:-30
                            }},
                    yaxis:{min:0}
                }
            });
        </script>
        <br/><br/>
        <?php 
    } 
    ?>
    <div class="ortro-table">
    <?php 
        echo $table_page->toHTML();
    ?>
    </div>
    <?php 
} 
?>