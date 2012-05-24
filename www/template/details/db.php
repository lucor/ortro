<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Frontend page that shows the database details
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
$id_db   = key($_REQUEST['id_chk']);

$dbUtil  = new DbUtil();
$dbh     = $dbUtil->dbOpenConnOrtro();
$db_info = $dbUtil->dbQuery($dbh, 
                             $dbUtil->getDbInfo($id_db), 
                             MDB2_FETCHMODE_ASSOC);
$dbh     = $dbUtil->dbCloseConn($dbh);
unset($dbh);
?>
<!-- start body -->
<div id="ortro-title">
   <?php echo DATABASE_DETAILS_TOP; ?>
</div>

<div id="toolbar" class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
</div>
<br/>
<div class="ortro-table">
<?php
    $table = new HTML_Table($table_attributes);
    $table->addRow(array(FIELD_LABEL, 
                         FIELD_DB_NAME_SID, 
                         FIELD_PORT, 
                         FIELD_DBMS),
                   '', 'TH');
    $table->addRow(array($db_info[0]['label'],
                         $db_info[0]['sid'],
                         $db_info[0]['port'],
                         $db_info[0]['description']), 
                   'class=c2', 'TD', false);
    $table->display();
?>
</div>