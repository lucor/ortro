<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Installer: Database Page
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

i18n('template', 'configure_metadata_install.php');

require_once ORTRO_CONF . 'configure_metadata_install.php';
require_once 'installUtil.php';
require_once 'MDB2.php';
require_once 'MDB2/Schema.php';

$check_result = 0;

if (isset($_POST['db-phptype']) && $_POST['db-phptype'] != '') {
    
    //override default values for form
    $_SESSION['installation']['db']['phptype']     = $_REQUEST['db-phptype'];
    $_SESSION['installation']['db']['host']        = $_REQUEST['db-host'];
    $_SESSION['installation']['db']['port']        = $_REQUEST['db-port'];
    $_SESSION['installation']['db']['database']    = $_REQUEST['db-database'];
    $_SESSION['installation']['db']['username']    = $_REQUEST['db-username'];
    $_SESSION['installation']['db']['password']    = $_REQUEST['db-password'];
    $_SESSION['installation']['db']['tableprefix'] = $_REQUEST['db-tableprefix'];
    
    $dbUtil = new DbUtil();
    
    switch ($_POST['db-phptype']) {
    case 'sqlite':
        if (!is_dir(ORTRO_SQLITE_DB)) {
            @mkdir(ORTRO_SQLITE_DB, 0700, true);
        }
        $db_name = ORTRO_SQLITE_DB . $_REQUEST['db-database'];
        break;
    default:
        $db_name = $_REQUEST['db-database'];
        break;
    }
    
    $dsn = $dbUtil->setDSN(array('phptype'  => $_REQUEST['db-phptype'], 
                                 'hostspec' => $_REQUEST['db-host']  . ":" .  
                                               $_REQUEST['db-port'],
                                 'database' => $_REQUEST['db-database'],
                                 'username' => $_REQUEST['db-username'],
                                 'password' => $_REQUEST['db-password']));
    
    $options = array('use_transactions'=> false);
    
    $schema =& MDB2_Schema::factory($dsn, $options);
    if (PEAR::isError($schema)) {
        showMessage($schema->getMessage() . '<br/>' . 
                    $schema->getDebugInfo(), 'warning');
    }
    
    $variables = array('db_name'=> $db_name,
                       'table_prefix'=> $_REQUEST['db-tableprefix'],
                       'user_language'=>AuthUtil::getSessionData('language'));
       
    $fileschema = ORTRO_INSTALL . 'ortro.schema.xml';

    $definition = $schema->parseDatabaseDefinitionFile($fileschema, $variables);
    
    if (isset($_REQUEST['overwrite_db'])) {
        $mdb2 =& MDB2::factory($dsn);
        if (PEAR::isError($mdb2)) {
            showMessage($mdb2->getMessage() . '<br/>' . 
                        $mdb2->getDebugInfo(), 'warning');
        }
        $mdb2->loadModule('Manager');
        $mdb2->dropDatabase($db_name);
    }
    $op = $schema->createDatabase($definition);

    if (PEAR::isError($op)) {
        showMessage($op->getMessage() . '<br/>' . 
                    $op->getDebugInfo(),
                    'warning');
    } else {
        $check_result = 1;
        
        $_SESSION['installation']['db_created'] = '1';
        showMessage(INSTALL_MSG_DB_CREATED_WITH_SUCCESS, 'success');
    }

}
/* ACTION TOOLBAR */
$form = new HTML_QuickForm('frm', 'post');

$table_attributes = 'cellpadding=0 cellspacing=0 border=0 width=100%';


$array_results = array();

$table = new HTML_Table($table_attributes);

$conf_metadata['db']['phptype']['value'] = 
    $_SESSION['installation']['metadata']['db']['phptype']['value'];

$html = '';

foreach ($conf_metadata as $key => $value) {
    if ($key == 'db') {
        foreach ($value as $myKey => $myValue) {
            if (is_array($myValue)) {
                $elements = split('-', $myValue['name']);
                if (isset($_SESSION['installation'][$elements[0]][$elements[1]])) {
                    $element_value = 
                        $_SESSION['installation'][$elements[0]][$elements[1]];
                } else {
                    //load default value
                    $element_value = $myValue['value'];
                    if (is_array($element_value)) {
                        if (isset($myValue['value'][0])) {
                            $element_value = $myValue['value'][0];
                        } else {
                            $element_value = '';
                        }
                    }
                }
                if (isset($_SESSION['installation']['db_created'])) {
                    if ($myValue['name'] != 'db-password') {
                        $html .= $myValue['description'] . ': <b>' . 
                                 $element_value . '</b><br/>';
                    }
                } else {
                    $temp  = createDynamicForm($form, 
                                                 $myValue, 
                                                 $element_value, 
                                                 true);
                    $html .= $temp['html'] . '<br/>';
                }
            }
        }
    }     
}


if (!isset($_SESSION['installation']['db_created'])) {
    /* Allows to overwrite existing database */
    $f_overwrite_db = $form->createElement('checkbox', 'overwrite_db',
                                           '', '&nbsp;' . FIELD_OVERWRITE_EXISTING_DB);
    $f_submit = $form->createElement('submit', 
                                     'create_db', 
                                     INSTALL_BUTTON_CREATE);
    $html .= '<br/>' . $f_overwrite_db->toHTML() . 
             '<br/><br/>' .
             $f_submit->toHTML();

}

$table->addRow(array($html), 'colspan=2', 'TD', true);

//Create Toolbar
$table_toolbar = new HTML_Table($table_attributes);

if ($check_result || isset($_SESSION['installation']['db_created'])) {
    $toolbar = createToolbar(array('back'=>'default',
                                   'forward'=>'default',
                                   'install'=>INSTALL_MENU_DATABASE));
} else {
    $toolbar = createToolbar(array('back'=>'default',
                                   'install'=>INSTALL_MENU_DATABASE));
}

// The toolbar javascript is used below $toolbar['javascript'];

if ($check_result) {
    $f_hidden  = $form->createElement('hidden', 
                                      'action', 
                                      $_SESSION['installation_step'])->toHTML();
    $f_hidden .= $form->createElement('hidden', 
                                      'mode', 
                                      $_SESSION['installation_step'])->toHTML();    
} else {
    $f_hidden  = $form->createElement('hidden', 
                                      'action', 
                                      $_SESSION['installation_step']-1)->toHTML();
    $f_hidden .= $form->createElement('hidden', 
                                      'mode', 
                                      $_SESSION['installation_step']-1)->toHTML();
}

$f_hidden .= $form->createElement('hidden', 'cat', 'install')->toHTML();

//convert form in array for extact js and attributes
$formArray = $form->toArray(); 
echo $formArray['javascript'];
?>

<form  <?php echo $formArray['attributes']; ?> >
<?php echo $f_hidden; ?>
<div class="ortro-table">
    <?php echo $toolbar['javascript']; ?>
    <?php echo $toolbar['header']; ?>
    <?php $table->display(); ?>
</div>
</form>
