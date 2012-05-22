<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Allows to display the space available on all currently 
 * mounted file systems of a remote server
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category Plugins
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

//###### Required core code ######

require_once 'sshUtil.php';
/**
 * Allows to display the space available on all currently 
 * mounted file systems of a remote server
 *
 * @param string $ip          IP address
 * @param array  $plugin_info Plugin info
 * @param object $form        The form object
 * 
 * @return string
 */
function get_dynamic_params($ip, $plugin_info, $form)
{
    include_once 'langUtil.php';
    i18n('system', 'file_system_check');
    
    $table_attributes = 'cellpadding=0 cellspacing=0 border=0 width=100%';
    
    $ssh    = new SSHUtil();
    $script = realpath(dirname(__FILE__)) . '/script.sh';
    
    //get static parameter
    $user = $plugin_info['file_system_check_user'];
    $port = $plugin_info['file_system_check_port'];
    
    //get the dynamic parameters and put them in an array 
    //(i.e. $dynamic_fields['/dev/hda1'] = 50)
    $dynamic_fields = $plugin_info['dyn_params'];
    
    $sshCommandResult = $ssh->sshConn($user, $ip, $port, $script, true);
    $stdout           = $sshCommandResult['stdout'];
    $exit_code        = $sshCommandResult['exit_code'];
    
    $table = new HTML_Table($table_attributes);
    
    $table->addRow(array(PLUGIN_FILE_SYSTEM_CHECK_FILESYSTEM,
                         PLUGIN_FILE_SYSTEM_CHECK_MOUNTPOINT,
                         PLUGIN_FILE_SYSTEM_CHECK_CAPACITY,
                         PLUGIN_FILE_SYSTEM_CHECK_THRESHOLD), '', 'TH', false);
    
    if ($exit_code == '0') {
        for ($index = 0; $index < sizeof($stdout); $index++) {
            if (preg_match('/^\/.*%$/', $stdout[$index]) == 1) {
                $tempArray = explode(' ', $stdout[$index]);
                $f_th_obj  = $form->addElement('text', 
                                               'dynamic_field_' . $tempArray[0], 
                                               '', 
                                               'size=3');
                if (isset($dynamic_fields[$tempArray[0]]) && 
                    $dynamic_fields[$tempArray[0]] != '') {
                    $f_th_obj->setValue($dynamic_fields[$tempArray[0]]);
                }
                $f_th = $f_th_obj->toHTML();
                $form->addRule('dynamic_field_' . $tempArray[0], 
                               $tempArray[0] . ': ' . 
                               PLUGIN_FILE_SYSTEM_CHECK_MSG_THRESHOLD_1, 
                               'numeric',
                               '',
                               'client');
                $form->addRule('dynamic_field_' . $tempArray[0], 
                               $tempArray[0] . ': ' . 
                               PLUGIN_FILE_SYSTEM_CHECK_MSG_THRESHOLD_2,  
                               'rangeValue', 
                               '1-100',
                               'client');
                $table->addRow(array($tempArray[0],
                                     $tempArray[1],
                                     $tempArray[2],
                                     $f_th), '', 'TD', false);
            }
        }    
    } else {
        $error_message = '<b>The following error has occurred during the ssh connection:</b><br/>' . 
                         implode("<br/>", $stdout);
        $table->addRow(array($error_message), 'colspan=4', 'TD', false);
    }
    $html = $table->toHTML();
    return $html;
}
?>