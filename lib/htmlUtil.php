<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Simple functions for html displaying
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category Libs
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

/**
 * Force the download for a file
 *
 * @param string $saved_filename The name to use for the file on saving
 * @param string $full_file_path The absolute path fo the file on the server
 *
 * @return void
 */
function httpDownload($saved_filename, $full_file_path)
{
    //Check for directory traversal
    if (!is_file($full_file_path) || (strpos($full_file_path, '..') !== false)) {
        return false;
    }
    //Force the download
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false);
    header('Content-Type: application/force-download');
    header('Content-Disposition: attachment; filename="'. $saved_filename . '";');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize($full_file_path));
    readfile($full_file_path);
    exit();
}

/**
 * Show a html message box
 *
 * @param string $action_message The message to display
 * @param string $type_message   The message type (i.e. success, warning)
 *
 * @return void
 */
function showMessage($action_message, $type_message)
{
    $allowed_type   = false;
    
    switch ($type_message) {
        case 'warning':
        case 'success':
            $allowed_type = true;
            break;
        default:
            break;
    }

    if (isset($action_message) && $allowed_type) {
        $table    = new HTML_Table(' class=' . $type_message);
        $img_html = '<img src=img/' . $type_message . '.png>';
        $table->addRow(array($img_html, $action_message),
                       'align=left valign=top', 'TD', false);
        $table->display();
        echo '<br/>';
    }
}

/**
 * Create the html code for a link
 *
 * @param string $link       uri
 * @param string $title      The alt message
 * @param string $message    The text to display
 * @param string $attributes The html attributes (optional)
 *
 * @return string The html code
 */
function createHref($link, $title, $message, $attributes='')
{
    $href = '<a href="' . $link . '" ' .
    $attributes .
             ' title="' . $title . '"' .
             ' onmouseover="self.status=\'' . 
    addslashes($title) . '\';return true;">' .
    $message . '</a>';
    return $href;
}

/**
 * Print sizes in human readable format (e.g., 1K 234M 2G)
 *
 * @param string $size size to convert
 *
 * @return string The converted size
 */
function resizeBytes($size)
{
    $count  = 0;
    $format = array('B','KB','MB','GB','TB','PB','EB','ZB','YB');
    while (($size/1024)>1 && $count<8) {
        $size = $size/1024;
        $count++;
    }
    $return = number_format($size, 0, '', '.') . ' ' . $format[$count];
    return $return;
}

/**
 * Allows to create the html starting from a configuration file.
 *
 * @param Object $_form               The form object
 * @param Array  $conf_field_metadata The form fields metedata
 * @param String $conf_field_value    The form field value (optional)
 * @param String $cfg_file_path       The absolute path of the configuration
 *                                    file (optional)
 *
 * @return Array
 */
function createDynamicForm($_form, $conf_field_metadata,
$conf_field_value, $cfg_file_path='')
{
    if ($conf_field_value == 'get_metadata_conf_value') {
        $form_field_value = $conf_field_metadata['value'];
    } else {
        $form_field_value = $conf_field_value;
    }
    $separator = '<br/>';
    switch ($conf_field_metadata['type']) {
        case 'select':
            $f_obj =  $_form->addElement($conf_field_metadata['type'],
            $conf_field_metadata['name'],
                                     '', 
            $conf_field_metadata['value'],
            $conf_field_metadata['attributes']);
            $f_obj->setSelected($form_field_value);
            $html_el =  $separator . $f_obj->toHTML();
            break;
        case 'radio':
            $f_obj =& $_form->addElement($conf_field_metadata['type'],
            $conf_field_metadata['name'],
                                     '', 
                                     '', 
            $conf_field_metadata['value'],
            $conf_field_metadata['attributes']);
            $f_obj->setChecked($form_field_value);
            $html_el =  $separator . $f_obj->toHTML();
            break;
        case 'file':
            $html_el  = $separator;  
            $html_el .= $_form->addElement($conf_field_metadata['type'],
                                           $conf_field_metadata['name'],
                                           $conf_field_metadata['value'],
                                           $conf_field_metadata['attributes'])->toHTML();
            break;
        case 'hidden':
            $html_el = $_form->addElement($conf_field_metadata['type'],
            $conf_field_metadata['name'],
            $conf_field_metadata['value'],
            $conf_field_metadata['attributes'])->toHTML();
            break;
        case 'submit':
            $f_obj =  $_form->addElement($conf_field_metadata['type'],
            $conf_field_metadata['name'],
                                     '', 
            $conf_field_metadata['attributes']);
            $f_obj->setValue($conf_field_metadata['value']);
            $html_el = '&nbsp;' . $f_obj->toHTML();
            $is_dynamic_param = !(strpos($conf_field_metadata['name'], '_get_dynamic_params') === false);
            if ($is_dynamic_param && isset($GLOBALS['ip'])) {
                //The plugin require to load an addition php file
                include_once $cfg_file_path . 'dynamic_parameters.php';
                // invoke the default function in dynamic_parameters.php
                $html_el .= get_dynamic_params($GLOBALS['ip'], 
                                               $GLOBALS['plugin_field_values'],
                                               $_form);
            }
            break;
        default:
            $f_obj =  $_form->addElement($conf_field_metadata['type'],
            $conf_field_metadata['name'],
                                     '', 
            $conf_field_metadata['attributes']);
            if ((strpos($conf_field_metadata['attributes'], 'htmlarea') === false)) {
                // Fix html editor strange behavior
                $f_obj->setValue($form_field_value);
            } else {
                $f_obj->setValue(str_replace('\\\\\\\'', "'", $conf_field_value));
            }
            $identity_picker = '';
            if (!(strpos($conf_field_metadata['name'], '_identity') === false)) {
                $identity_picker = '&nbsp;'.
                createHref('javascript:void(0);',
                TOOLTIP_IDENTITY_PICKER,
                                          '<img src="img/identity.png" border=0>', 
                                          'onclick="return identityPicker(\'' . 
                $conf_field_metadata['name'] . '\');"');

            }
            $html_el =  $separator . $f_obj->toHTML() . $identity_picker;
            break;
    }
    $form_html =  $conf_field_metadata['description'] . $html_el;
    //add the rule to form field if required
    if (isset($conf_field_metadata['num_rules']) &&
    $conf_field_metadata['num_rules'] != 0) {
        for ($j = 0; $j < $conf_field_metadata['num_rules']; $j++) {
            if (isset($conf_field_metadata['rule_type'][$j])) {
                $_form->addRule($conf_field_metadata['name'],
                $conf_field_metadata['rule_msg'][$j],
                $conf_field_metadata['rule_type'][$j],
                $conf_field_metadata['rule_attribute'][$j],
                                'client');
            }
        }
    }
    return array('form'=>$_form,'html'=>$form_html);
}

/**
 * Get crontab values starting from form request format
 *
 * @param Array $cron The $_REQUEST array
 *
 * @return Array
 */
function getCrontabValues($cron)
{

    /* Get crontab values starting from form request format */
    if (isset($cron['schedule_type'])) {
        $crontab['schedule_type'] = $cron['schedule_type'];
    }

    if ($cron != '' && $cron['schedule_type'] != 'D') {

        switch ($cron['crontab_m']) {
            case '*':
                $crontab['db']['m'] = '-00-01-02-03-04-05-06-07-08-09-10'.
                                  '-11-12-13-14-15-16-17-18-19-20'.
                                  '-21-22-23-24-25-26-27-28-29-30'.
                                  '-31-32-33-34-35-36-37-38-39-40'.
                                  '-41-42-43-44-45-46-47-48-49-50'.
                                  '-51-52-53-54-55-56-57-58-59-';
                break;
            case 'every5':
                $crontab['db']['m'] = '-00-05-10-15-20-25-30-35-40-45-50-55-';
                break;
            case 'every15':
                $crontab['db']['m'] = '-00-15-30-45-';
                break;
            case 'every30':
                $crontab['db']['m'] = '-00-30-';
                break;
            case 'custom':
                $crontab['db']['m'] = '-' . implode('-', $cron['minute_custom']) .
                                  '-';
                break;
            case '-00-01-02-03-04-05-06-07-08-09-10-11-12-13-14-15-16-17-18-19-20-21-22-23-24-25-26-27-28-29-30-31-32-33-34-35-36-37-38-39-40-41-42-43-44-45-46-47-48-49-50-51-52-53-54-55-56-57-58-59-':
                $crontab['form']['m'] = '*';
                $crontab['html']['m'] = '*';
                break;
            case '-00-05-10-15-20-25-30-35-40-45-50-55-':
                $crontab['form']['m'] = 'every5';
                $crontab['html']['m'] = FIELD_JOB_SCHEDULE_EVERY_5;
                break;
            case '-00-15-30-45-':
                $crontab['form']['m'] = 'every15';
                $crontab['html']['m'] = FIELD_JOB_SCHEDULE_EVERY_15;
                break;
            case '-00-30-':
                $crontab['form']['m'] = 'every30';
                $crontab['html']['m'] = FIELD_JOB_SCHEDULE_EVERY_30;
                break;
            default:
                $crontab['form']['m'] = 'custom';
                $crontab_m_values     = split('-', $cron['crontab_m']);
                array_shift($crontab_m_values);
                array_pop($crontab_m_values);
                $crontab['form']['values']['m'] = $crontab_m_values;
                $crontab['html']['m']           = implode(',', $crontab_m_values);
                break;
        }

        //hour
        switch ($cron['crontab_h']) {
            case '*':
                $crontab['db']['h'] = '-0-1-2-3-4-5-6-7-8-9-10-11-12-13-14-15-16-17-18-19-20-21-22-23-';
                break;
            case 'every6':
                $crontab['db']['h'] = '-0-6-12-18-';
                break;
            case 'every12':
                $crontab['db']['h'] = '-0-12-';
                break;
            case 'custom':
                $crontab['db']['h'] = '-' . implode('-', $cron['hour_custom']) . '-';
                break;
            case '-0-1-2-3-4-5-6-7-8-9-10-11-12-13-14-15-16-17-18-19-20-21-22-23-':
                $crontab['form']['h'] = '*';
                $crontab['html']['h'] = '*';
                break;
            case '-0-6-12-18-':
                $crontab['form']['h'] = 'every6';
                $crontab['html']['h'] = FIELD_JOB_SCHEDULE_EVERY_6;
                break;
            case '-0-12-':
                $crontab['form']['h'] = 'every12';
                $crontab['html']['h'] = FIELD_JOB_SCHEDULE_EVERY_12;
                break;
            default:
                $crontab['form']['h'] = 'custom';
                $crontab_h_values     = split('-', $cron['crontab_h']);
                array_shift($crontab_h_values);
                array_pop($crontab_h_values);
                $crontab['form']['values']['h'] = $crontab_h_values;
                $crontab['html']['h']           = implode(',', $crontab_h_values);
                break;
        }

        //day
        switch ($cron['crontab_dom']) {
            case '*':
                $crontab['db']['dom'] = '-1-2-3-4-5-6-7-8-9-10-11-12-13-14-15-16-17-18-19-20-21-22-23-24-25-26-27-28-29-30-31-';
                break;
            case 'custom':
                $crontab['db']['dom'] = '-' . @implode('-', $cron['day_custom']) . '-';
                break;
            case '-1-2-3-4-5-6-7-8-9-10-11-12-13-14-15-16-17-18-19-20-21-22-23-24-25-26-27-28-29-30-31-':
                $crontab['form']['dom'] = '*';
                $crontab['html']['dom'] = '*';
                break;
            default:
                $crontab['form']['dom'] = 'custom';
                $crontab_dom_values     = split('-', $cron['crontab_dom']);
                array_shift($crontab_dom_values);
                array_pop($crontab_dom_values);
                $crontab['form']['values']['dom'] = $crontab_dom_values;
                $crontab['html']['dom']           = implode(',', $crontab_dom_values);
                break;
        }

        //month
        switch ($cron['crontab_mon']) {
            case '*':
                $crontab['db']['mon'] = '-1-2-3-4-5-6-7-8-9-10-11-12-';
                break;
            case 'custom':
                $crontab['db']['mon'] = '-' . implode('-', $cron['month_custom']) . '-';
                break;
            case '-1-2-3-4-5-6-7-8-9-10-11-12-':
                $crontab['form']['mon'] = '*';
                $crontab['html']['mon'] = '*';
                break;
            default:
                $crontab['form']['mon'] = 'custom';
                $crontab_mon_values     = split('-', $cron['crontab_mon']);
                array_shift($crontab_mon_values);
                array_pop($crontab_mon_values);
                $crontab['form']['values']['mon'] = $crontab_mon_values;
                $crontab['html']['mon']           = implode(',', $crontab_mon_values);
                break;
        }

        //day of week
        switch ($cron['crontab_dow']) {
            case '*':
                $crontab['db']['dow'] = '-0-1-2-3-4-5-6-';
                break;
            case 'custom':
                $crontab['db']['dow'] = '-' . implode('-', $cron['dayweek_custom']) .
                                    '-';
                break;
            case '-0-1-2-3-4-5-6-':
                $crontab['form']['dow'] = '*';
                $crontab['html']['dow'] = '*';
                break;
            default:
                $crontab['form']['dow'] = 'custom';
                $crontab_dow_values     = split('-', $cron['crontab_dow']);
                array_shift($crontab_dow_values);
                array_pop($crontab_dow_values);
                $crontab['form']['values']['dow'] = $crontab_dow_values;
                $crontab['html']['dow']           = implode(',', $crontab_dow_values);
                break;
        }

    } else {
        $crontab['form']['m']   = '*';
        $crontab['form']['h']   = '*';
        $crontab['form']['dom'] = '*';
        $crontab['form']['mon'] = '*';
        $crontab['form']['dow'] = '*';
        $crontab['html']['m']   = '*';
        $crontab['html']['h']   = '*';
        $crontab['html']['dom'] = '*';
        $crontab['html']['mon'] = '*';
        $crontab['html']['dow'] = '*';
        $crontab['db']['m']     = '-';
        $crontab['db']['h']     = '-';
        $crontab['db']['dom']   = '-';
        $crontab['db']['mon']   = '-';
        $crontab['db']['dow']   = '-';
        if (isset($cron['schedule_type']) && $cron['schedule_type'] == 'D') {
            $crontab['html']['m']   = '-';
            $crontab['html']['h']   = '-';
            $crontab['html']['dom'] = '-';
            $crontab['html']['mon'] = '-';
            $crontab['html']['dow'] = '-';
        }
    }

    if (isset($cron['schedule_type'])) {
        switch ($cron['schedule_type']) {
            case 'A':
                $crontab['html']['schedule_type'] = FIELD_JOB_SCHEDULE_ENABLE;
                break;
            case 'T':
                $crontab['html']['schedule_type'] = FIELD_JOB_SCHEDULE_ENABLE_WORKFLOW;
                break;
            case 'J':
                $crontab['html']['schedule_type'] = FIELD_JOB_SCHEDULE_ENABLE_JOB;
                break;
            case 'D':
                $crontab['html']['schedule_type'] = FIELD_JOB_SCHEDULE_DISABLE;
                break;
        }
    }
    return $crontab;
}

/**
 * Allows to create the html code for crontab starting from the db parameters
 *
 * @param Object $form     The form object
 * @param Array  $cron     The crontab values
 * @param String $category The category where crontab is used.
 *
 * @return Array
 */
function createCrontabHtml($form, $cron = '', $category = '')
{
    $table_attributes        = 'cellpadding=0 cellspacing=0 border=0 width=100%';
    $hidden_table_attributes = $table_attributes . ' style="display: none"';
    $crontab_html_static     = array();
    // --- get the crontabs fields ---

    $crontab = getCrontabValues($cron);
    //minutes

    $select_minute['*']       = FIELD_JOB_SCHEDULE_EVERY_MIN;
    $select_minute['every5']  = FIELD_JOB_SCHEDULE_EVERY_5_MIN;
    $select_minute['every15'] = FIELD_JOB_SCHEDULE_EVERY_15_MIN;
    $select_minute['every30'] = FIELD_JOB_SCHEDULE_EVERY_30_MIN;
    $select_minute['custom']  = FIELD_JOB_SCHEDULE_CUSTOM;
    $f_minute_obj             = $form->addElement('select',
                                                  'crontab_m', 
                                                  '', 
    $select_minute,
                                                  'onchange="showFormFields(this.value, \'minute_\', \'custom\');"');
    $f_minute_obj->setSelected($crontab['form']['m']);
    $f_minute = $f_minute_obj->toHTML();

    for ($index = 0; $index < 60; $index++) {
        $prefix_0 = '';
        if ($index < 10) {
            $prefix_0 = 0;
        }
        $select_minute_custom[$prefix_0 . $index] = $index;
    }
    $f_minute_custom = $form->addElement('select', 'minute_custom', '',
    $select_minute_custom,
                                         'multiple id=minute_custom_sel');

    $form->registerRule('checkMultiSelectJob', 'callback', 'checkMultiSelectJob');
    $form->addRule('crontab_m', MSG_SELECT_A_VALUE_FOR_MINUTE,
                   'checkMultiSelectJob', 'minute_custom_sel', 'client');

    if ($crontab['form']['m'] == 'custom') {
        $table = new HTML_Table($table_attributes . ' id=minute_custom');
        $f_minute_custom->setSelected($crontab['form']['values']['m']);
    } else {
        $table = new HTML_Table($hidden_table_attributes . ' id=minute_custom');
    }
    $table->addRow(array($f_minute_custom->toHTML()),
                   'align=left valign=top', 'TD', false);
    $table_minute = $table->toHTML();

    //hours
    $select_hour['*']       = FIELD_JOB_SCHEDULE_EVERY_HOUR;
    $select_hour['every6']  = FIELD_JOB_SCHEDULE_EVERY_6_HOUR;
    $select_hour['every12'] = FIELD_JOB_SCHEDULE_EVERY_12_HOUR;
    $select_hour['custom']  = FIELD_JOB_SCHEDULE_CUSTOM;

    $f_hour_obj = $form->addElement('select', 'crontab_h', '', $select_hour,
                                    'onchange="showFormFields(this.value, \'hour_\', \'custom\');"');
    $f_hour_obj->setSelected($crontab['form']['h']);
    $f_hour = $f_hour_obj->toHTML();

    for ($index = 0; $index < 24; $index++) {
        $select_hour_custom[$index] = $index;
    }

    $f_hour_custom = $form->addElement('select', 'hour_custom', '',
    $select_hour_custom,
                                       'multiple id=hour_custom_sel');
    $form->addRule('crontab_h', MSG_SELECT_A_VALUE_FOR_HOUR,
                   'checkMultiSelectJob', 'hour_custom_sel', 'client');
    if ($crontab['form']['h'] == 'custom') {
        $table = new HTML_Table($table_attributes . ' id=hour_custom');
        $f_hour_custom->setSelected($crontab['form']['values']['h']);
    } else {
        $table = new HTML_Table($hidden_table_attributes . ' id=hour_custom');
    }
    $table->addRow(array($f_hour_custom->toHTML()),
                   'align=left valign=top', 'TD', false);
    $table_hour = $table->toHTML();

    //day of month
    $select_day['*']      = FIELD_JOB_SCHEDULE_EVERY_DAY;
    $select_day['custom'] = FIELD_JOB_SCHEDULE_CUSTOM;

    $f_day_obj = $form->addElement('select', 'crontab_dom', '', $select_day,
                                   'onchange="showFormFields(this.value, \'day_\', \'custom\');"');

    $f_day_obj->setSelected($crontab['form']['dom']);
    $f_day = $f_day_obj->toHTML();

    for ($index = 1; $index < 32; $index++) {
        $select_day_custom[$index] = $index;
    }

    $f_day_custom = $form->addElement('select', 'day_custom', '',
    $select_day_custom,
                                      'multiple id=day_custom_sel');
    $form->addRule('crontab_dom', MSG_SELECT_A_VALUE_FOR_DAY, 'checkMultiSelectJob',
                   'day_custom_sel', 'client');
    if ($crontab['form']['dom'] == 'custom') {
        $table = new HTML_Table($table_attributes . ' id=day_custom');
        $f_day_custom->setSelected($crontab['form']['values']['dom']);
    } else {
        $table = new HTML_Table($hidden_table_attributes . ' id=day_custom');
    }
    $table->addRow(array($f_day_custom->toHTML()),
                   'align=left valign=top', 'TD', false);
    $table_day = $table->toHTML();

    //month of year
    $select_month['*']      = FIELD_JOB_SCHEDULE_EVERY_MONTH;
    $select_month['custom'] = FIELD_JOB_SCHEDULE_CUSTOM;

    $f_month_obj = $form->addElement('select', 'crontab_mon', '', $select_month,
                                     'onchange="showFormFields(this.value, \'month_\', \'custom\');"');
    $f_month_obj->setSelected($crontab['form']['mon']);
    $f_month = $f_month_obj->toHTML();

    for ($index = 1; $index < 13; $index++) {
        $select_month_custom[$index] = $index;
    }

    $f_month_custom = $form->addElement('select', 'month_custom', '',
    $select_month_custom,
                                        'multiple  id=month_custom_sel');
    $form->addRule('crontab_mon', MSG_SELECT_A_VALUE_FOR_MONTH,
                   'checkMultiSelectJob', 'month_custom_sel', 'client');

    if ($crontab['form']['mon'] == 'custom') {
        $table = new HTML_Table($table_attributes . ' id=month_custom');
        $f_month_custom->setSelected($crontab['form']['values']['mon']);
    } else {
        $table = new HTML_Table($hidden_table_attributes . ' id=month_custom');
    }
    $table->addRow(array($f_month_custom->toHTML()),
                   'align=left valign=top', 'TD', false);
    $table_month = $table->toHTML();

    //day of week
    $select_dayweek['*']      = FIELD_JOB_SCHEDULE_EVERY_DAY_OF_WEEK;
    $select_dayweek['custom'] = FIELD_JOB_SCHEDULE_CUSTOM;

    $f_dayweek_obj = $form->addElement('select', 'crontab_dow', '', $select_dayweek,
                                       'onchange="showFormFields(this.value, \'dayweek_\', \'custom\');"');
    $f_dayweek_obj->setSelected($crontab['form']['dow']);
    $f_dayweek = $f_dayweek_obj->toHTML();

    $select_dayweek_custom['0'] = FIELD_JOB_SCHEDULE_SUNDAY;
    $select_dayweek_custom['1'] = FIELD_JOB_SCHEDULE_MONDAY;
    $select_dayweek_custom['2'] = FIELD_JOB_SCHEDULE_TUESDAY;
    $select_dayweek_custom['3'] = FIELD_JOB_SCHEDULE_WEDNEDAY;
    $select_dayweek_custom['4'] = FIELD_JOB_SCHEDULE_THURSDAY;
    $select_dayweek_custom['5'] = FIELD_JOB_SCHEDULE_FRIDAY;
    $select_dayweek_custom['6'] = FIELD_JOB_SCHEDULE_SATURDAY;

    $f_dayweek_custom = $form->addElement('select', 'dayweek_custom', '',
    $select_dayweek_custom,
                                          'multiple id=dayweek_custom_sel');
    $form->addRule('crontab_dow', MSG_SELECT_A_VALUE_FOR_DAY_OF_WEEK,
                   'checkMultiSelectJob', 'dayweek_custom_sel', 'client');
    if ($crontab['form']['dow'] == 'custom') {
        $table = new HTML_Table($table_attributes . ' id=dayweek_custom');
        $f_dayweek_custom->setSelected($crontab['form']['values']['dow']);
    } else {
        $table = new HTML_Table($hidden_table_attributes . ' id=dayweek_custom');
    }
    $table->addRow(array($f_dayweek_custom->toHTML()),
                   'align=left valign=top', 'TD', false);
    $table_dayweek = $table->toHTML();

    if ($category == 'workflow') {
        $select_schedule_type['A'] = FIELD_JOB_SCHEDULE_ENABLE;
    } else {
        $select_schedule_type['T'] = FIELD_JOB_SCHEDULE_ENABLE_WORKFLOW;
        $select_schedule_type['J'] = FIELD_JOB_SCHEDULE_ENABLE_JOB;
    }
    $select_schedule_type['D'] = FIELD_JOB_SCHEDULE_DISABLE;

    $f_schedule_type = $form->addElement('select', 'schedule_type', '',
    $select_schedule_type,
                                         'onchange="enableSchedule(this.value);"');
    if (isset($crontab['schedule_type'])) {
        $f_schedule_type->setSelected($crontab['schedule_type']);
    }

    $table = new HTML_Table($table_attributes);
    $table->addRow(array(FIELD_JOB_SCHEDULE), 'colspan=5', 'TH');
    $table->addRow(array($f_schedule_type->toHTML()), 'colspan=5', 'TD', false);
    $table->addRow(array(FIELD_JOB_SCHEDULE_MINUTE,
    FIELD_JOB_SCHEDULE_HOUR,
    FIELD_JOB_SCHEDULE_DAY_OF_MONTH,
    FIELD_JOB_SCHEDULE_MONTH_OF_YEAR,
    FIELD_JOB_SCHEDULE_DAY_OF_WEEK),
                   '', 'TH');
    $table->addRow(array($f_minute,
    $f_hour,
    $f_day,
    $f_month,
    $f_dayweek), 'valign=top', 'TD', false);
    $table->addRow(array($table_minute,
    $table_hour,
    $table_day,
    $table_month,
    $table_dayweek), 'valign=top class=c1', 'TD', false);
    $form_html = $table->toHTML();

    return  array('form'=>$form, 'html'=>$form_html);

}

/**
 * Allows to create the html toolbar code
 *
 * @param array $buttons The associative array action => role
 *
 * @return array the associative array with javascript and html code
 */
function createToolbar($buttons)
{
    $html      = array();
    $html_menu = array();
    $i         = 0;

    $js_default_status       = '';
    $js_admin_status         = '';
    $js_guest_status         = '';
    $js_default_admin_status = '';
    $js_hide_edit_button     = '';
    $auto_refresh_html       = '';
    $custom_html             = '';
    $install_html            = '';

    foreach ($buttons as $button => $role) {
        $class = 'toolbar_hidden';
        switch ($button) {
            case 'add':
                $description = ACTION_ADD_DESCRIPTION ;
                $image       = 'add.png';
                break;
            case 'edit':
                $description = ACTION_EDIT_DESCRIPTION ;
                $image       = 'edit.png';
                break;
            case 'copy':
                $description = ACTION_COPY_DESCRIPTION ;
                $image       = 'copy.png';
                break;
            case 'delete':
                $description = ACTION_DELETE_DESCRIPTION ;
                $image       = 'eraser.png';
                break;
            case 'lock':
                $description = ACTION_LOCK_DESCRIPTION ;
                $image       = 'lock.png';
                break;
            case 'unlock':
                $description = ACTION_UNLOCK_DESCRIPTION ;
                $image       = 'unlock.png';
                break;
            case 'run':
                $description = ACTION_RUN_DESCRIPTION ;
                $image       = 'run.png';
                break;
            case 'details':
                $description = ACTION_DETAIL_DESCRIPTION;
                $image       = 'details.png';
                break;
            case 'userGroup':
                $description = ACTION_GROUP_DESCRIPTION;
                $image       = 'groups.png';
                break;
            case 'reload_page':
                $description = ACTION_RELOAD_DESCRIPTION;
                $image       = 'reload_page.png';
                $class       = 'toolbar';
                break;
            case 'forward':
                $description = ACTION_NEXT_STEP_DESCRIPTION;
                $image       = 'forward.png';
                $class       = 'toolbar';
                break;
            case 'back':
                $description = ACTION_PREV_STEP_DESCRIPTION;
                $image       = 'back.png';
                $class       = 'toolbar';
                break;
            case 'forwardPage':
                $description = ACTION_NEXT_PAGE_DESCRIPTION;
                $image       = 'forward.png';
                $class       = 'toolbar';
                break;
            case 'backPage':
                $description = ACTION_PREV_PAGE_DESCRIPTION;
                $image       = 'back.png';
                $class       = 'toolbar';
                break;
            case 'kill':
                $description = ACTION_KILL_DESCRIPTION;
                $image       = 'kill.png';
                $class       = 'toolbar';
                break;
            case 'auto_refresh':
                $auto_refresh_html = $role;
                continue 2;
                break;
            case 'custom':
                $custom_html = $role;
                continue 2;
                break;
            case 'install':
                $install_html = $role;
                continue 2;
                break;
            default:
                break;
        }

        $toolbar_id      = 'toolbar_' .  $button;
        $toolbar_menu_id = 'toolbar_menu_' .  $button;

        $js_default_status .= 'document.getElementById("' . $toolbar_id .
                              '").className = \'toolbar_hidden\';' . "\n";
        $js_default_status .= 'document.getElementById("' . $toolbar_menu_id .
                              '").className = \'toolbar_hidden\';' . "\n";

        switch($role) {
            case 'admin':
                $js_admin_status .= 'document.getElementById("' .
                $toolbar_id .
                                '").className = \'toolbar\';' . "\n";
                $js_admin_status .= 'document.getElementById("' .
                $toolbar_menu_id .
                                '").className = \'toolbar\';' . "\n";   
                break;
            case 'guest':
                //guest
                $js_guest_status .= 'document.getElementById("' .
                $toolbar_id .
                                '").className = \'toolbar\';' . "\n";
                $js_guest_status .= 'document.getElementById("' .
                $toolbar_menu_id .
                                '").className = \'toolbar\';' . "\n";
                break;
            case 'default':
                $js_default_status .= 'document.getElementById("' .
                $toolbar_id .
                                  '").className = \'toolbar\';'. "\n";
                $js_default_status .= 'document.getElementById("' .
                $toolbar_menu_id .
                                  '").className = \'toolbar\';'. "\n";
                break;
            case 'default_admin':
                $js_default_admin_status .= 'document.getElementById("' .
                $toolbar_id .
                                        '").className = \'toolbar\';'. "\n";
                $js_default_admin_status .= 'document.getElementById("' .
                $toolbar_menu_id .
                                        '").className = \'toolbar\';'. "\n";
                break;
        }

        $html[$i] = createHref('javascript:void(0);',
        $description,
                                      '<img src="img/' . $image . '" border="0" alt="' .
        $description . '"/>',
                                      'onclick="return submitForm(\'' . $button . 
                                      '\');" id="' .$toolbar_id . '" class="' . 
        $class . '"');

        $html_menu[$i] = createHref('javascript:void(0);',
        $description,
                                      '<img src="img/' . $image . '" border="0" alt="' .
        $description . '"/>',
                                      'onclick="return submitForm(\'' . $button . 
                                      '\');" id="' .$toolbar_menu_id . '" class="' . 
        $class . '"');
        $i++;
    }

    $javascript = "<script type=\"text/javascript\">" . "\n" .
                    "//<![CDATA[" . "\n" . 
                    "function checkRoles(){" . "\n" .
                    "var chks = document.frm.id_chk;" . "\n" .
                    "var hide_edit_button = false;"  . "\n" .
    $js_default_status  . "\n" .
                    "if (document.frm.is_admin_for_a_system.value == \"1\") {" . 
                    "\n" .
                    "   // add the default admin actions" . "\n" .
    $js_default_admin_status  . "\n" .
                    "}" . "\n" .
                    "if (chks.length == undefined) {" . "\n" .
                    "   // only a check box create an array" . "\n" .
                    "   el = [ chks ];" . "\n" .
                    "} else {" . "\n" .
                    "   el = chks;" . "\n" .
                    "}" . "\n" .
                    "var enable_admin_default = false;" . "\n" .
                    "var is_admin = false;" . "\n" .
                    "var is_guest = false;" . "\n" .
                    "for (var i = 0; i < el.length; i++) {" . "\n" .
                    "   if (el[i].checked) {" . "\n" .
                    "       switch(el[i].getAttribute(\"role\")) {" . "\n" .
                    "           case 'admin':" . "\n" .
                    "               is_admin = true;" . "\n" .
                    "           break;" . "\n" .
                    "       default:" . "\n" .
    $js_guest_status  . "\n" .
                    "           is_guest = true;" . "\n" .
                    "           break;" . "\n" .
                    "       }" . "\n" .
                    "       if(el[i].getAttribute(\"hide_edit_plugin_conf\") == 1) {" . "\n" .
                    "           hide_edit_button = true;" . "\n" .
                    "       }"  . "\n" .
                    "   }" . "\n" .
                    "   if (is_guest) {" . "\n" .
                    "       break;//perform an action on a guest system" . "\n" .
                    "   }" . "\n" .
                    "}" . "\n" .
                    "if (is_admin && !is_guest) {" . "\n" .
    $js_guest_status  . "\n" .
    $js_admin_status  . "\n" .
                    "}" . "\n" .
                    "if (hide_edit_button) {" . "\n" .
                    "   document.getElementById(\"toolbar_edit\").className = 'toolbar_hidden'"  . "\n" .
                    "   document.getElementById(\"toolbar_menu_edit\").className = 'toolbar_hidden'"  . "\n" .
                    "}" . "\n" .
                  "}" . "\n" .
                  "//]]>" . "\n" .
                  "</script>";

    $table_attributes = 'cellpadding="0" cellspacing="0" border="0" width="100%"';

    $table_toolbar_attributes = 'cellpadding="0" cellspacing="0" ' .
                                'border="0" width="100px"';

    $table_toolbar = new HTML_Table($table_attributes);

    if ($install_html != '') {
        $table_toolbar->addRow(array($install_html,$html), 'class=install', 'TH');
        $table_toolbar->updateCellAttributes('0', '0', 'width=99%');
        $toolbar = array('header' => $table_toolbar->toHtml(),
                         'javascript'=>$javascript);                   
    } else {
        $block_toolbar = createHref('javascript:void(0);',
        ACTION_LOCK_UNLOCK,
                                    '<img id="toolbar_img" ' . 
                                    'src="img/toolbar-unlocked.png" ' . 
                                    'border="0" alt=""/>', 
                                    'onclick="return ' . 
                                    'blockToolbar(\'toolbar_img\');"');
        $table_toolbar->addRow(array($block_toolbar . ACTION_TITLE), '', 'TH');
        $align = '';
        if ($custom_html != '') {
            //add custom button
            array_push($html, $custom_html);
        }
        if ($auto_refresh_html != '') {
            //add auto_refresh select
            array_push($html, REFRESH_TITLE . $auto_refresh_html);
            $align = 'align=right';
        }
        $table_toolbar->updateCellAttributes('0', '0', 'colspan=' . count($html));
        $table_toolbar->addRow($html, 'class="c2"', 'TD', true);
        $table_toolbar->updateCellAttributes('1', count($html)-1,
        $align . ' width=99%');

        /* Create the toolbar menu for scrolling */
        $table_toolbar_menu = new HTML_Table($table_toolbar_attributes);
        $table_toolbar_menu->addRow(array(ACTION_TITLE), 'colspan=3', 'TH');
        $j        = 0;
        $temp_row = array();
        for ($k = 0; $k < sizeof($html_menu); $k++) {
            array_push($temp_row, $html_menu[$k]);
            if ($j==2) {
                $table_toolbar_menu->addRow($temp_row, 'class=c2', 'TD', true);
                $temp_row = array();
                $j        = 0;
            } else {
                $j++;
            }
        }
        if ($j!=0) {
            $table_toolbar_menu->addRow($temp_row, 'class=c2', 'TD', true);
        }
        $toolbar = array('header' => $table_toolbar->toHtml(),
                         'menu' => $table_toolbar_menu->toHtml(),
                         'javascript'=>$javascript);                   
    }
    return $toolbar;
}
?>
