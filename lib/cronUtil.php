<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Job scheduleder class.
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

require_once 'logUtil.php';
require_once 'dbUtil.php';
require_once 'notifyUtil.php';

/**
 * CronUtil Class
 *
 * @category Libs
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */
class CronUtil
{
    private $requestType;
    
    /**
     * CronUtil Class constructor.
     * 
     * @param string $requestType The request type
     * 
     * @return void
     */
    function cronUtil($requestType = 'CRONTAB')
    {
        $this->logger      = new LogUtil('cronUtil.php');
        $this->requestType = $requestType;
    }
    
    /**
     * Get all info for the job requested by an XML-RPC call and execute it
     * 
     * @param string $label The job label to execute
     * 
     * @return string The job output
     */
    function runJobRPC($label)
    {
        $this->logger->trace('DEBUG', 
                             'Start RPC job execution ' . $this->requestType);
        $dbUtil = new DbUtil();
        $dbh    = $dbUtil->dbOpenConnOrtro();
                        
        $rows = $dbUtil->dbQuery($dbh, 
                                  $dbUtil->getJobsToExecRPC($label), 
                                  MDB2_FETCHMODE_ASSOC);
        
        $ret = $this->execJob($rows[0]['id_job'], 
                              $rows[0]['label'],
                              $rows[0]['category'],
                              'XML-RPC');
        
        $dbh = $dbUtil->dbCloseConn($dbh);
        unset($dbh);
        $this->logger->trace('DEBUG', 'End RPC job execution');
        return $dbUtil->dbUnserialize($ret);
    }
    
    /**
     * Get all the scheduled jobs that must be executed and execute them
     * 
     * @return void
     */
    function runCron()
    {
        $this->logger->trace('DEBUG', '######################################');
        $this->logger->trace('DEBUG', 'Start crontab');
        $dbUtil = new DbUtil();
        
        $dbh = $dbUtil->dbOpenConnOrtro();
        //Get all long running jobs
        if ($GLOBALS['conf']['env']['job_timeout'] > 0) {
            $long_run_jobs = $dbUtil->dbQuery($dbh, $dbUtil->getJobsTimeout());
            if (count($long_run_jobs) > 0) {
                foreach ($long_run_jobs as $key=>$row) {
                    $this->logger->trace('DEBUG', 
                                         $log_prefix . 
                                         'Attempt to kill zombie job: ' . $row[0]);
                    $this->killJob($row[0]);
                    $timeout_msg = 'Job execution timeout. Please contact administrator.'; 
                    $dbUtil->dbExec($dbh, $dbUtil->setJobEnd($row[0], 0, $timeout_msg));
                }
            }
        }
        //Get all available shd
        $locked_shd_result = $dbUtil->dbQuery($dbh, $dbUtil->getLockedSystemHostDb());
        
        $locked_shd = 0;
        if (count($locked_shd_result)>0) {
            foreach ($locked_shd_result as $key=>$row) {
                $locked_shd_arr[] = $row[0];
            }
            $locked_shd = implode(',', $locked_shd_arr);
        }
        
        //Get all scheduled jobs as part of workwflow ready for execution
        $rows_job_workflows = $dbUtil->dbQuery($dbh, 
                                               $dbUtil->getJobsWorkflowToExec($locked_shd), 
                                               MDB2_FETCHMODE_ASSOC);
        //Get all scheduled workwflows ready for execution
        $rows_workflows = $dbUtil->dbQuery($dbh, 
                                           $dbUtil->getWorkflowsToExec($locked_shd), 
                                           MDB2_FETCHMODE_ASSOC);
        $executed_jobs  = array();
        
        $rows_workflows_to_exec = $rows_job_workflows + $rows_workflows;
        foreach ($rows_workflows_to_exec as $key=>$row) {
            $log_prefix = '[workflow: ' . $row['id_workflow'] . 
                          '][step: ' . $row['step'] . 
                          '][job: ' . $row['id_job'] . ']';
            $this->logger->trace('DEBUG', 
                                 $log_prefix . 
                                 '[mode: crontab] workflow started...');
            //Execute the job
            $this->execJob($row['id_job'], $row['label'], $row['category']);
            //Update the workflow status to running
            $dbUtil->dbExec($dbh, 
                             $dbUtil->updateWorkflowStatus($row['id_workflow'], 
                                                           'R'));
            //Update the step status for this workflow as undefined
            if ($row['step'] == 1) {
                $dbUtil->dbExec($dbh, 
                    $dbUtil->updateWorkflowStepStatus($row['id_workflow'], 
                                                     '*', 
                                                     '-'));
            }
            //Update the status of the first step as running
            $dbUtil->dbExec($dbh, 
                $dbUtil->updateWorkflowStepStatus($row['id_workflow'],
                                                  $row['step'],
                                                  'R'));
            //Add the job to $executed_jobs array
            array_push($executed_jobs, $row['id_job']);
        }
        $executed_jobs_values = array_values($executed_jobs);
        unset($rows_workflows);
        unset($executed_jobs);
        
        //Get all scheduled jobs ready for execution
        $rows = $dbUtil->dbQuery($dbh, 
                                  $dbUtil->getJobsToExec($locked_shd), 
                                  MDB2_FETCHMODE_ASSOC);

        foreach ($rows as $key=>$row) {
            if (!in_array($row['id_job'], $executed_jobs_values)) {
                //This job is not a workflow step execute it!
                echo $row['id_job'];
                $this->execJob($row['id_job'], $row['label'], $row['category']);
            } else {
                $this->logger->trace('DEBUG', 
                                     'Job already executed as workflow step' . 
                                     $row['id_job']);
            }
        }
        
        $dbh = $dbUtil->dbCloseConn($dbh);
        unset($dbh);
        $this->logger->trace('DEBUG', 'End crontab');
        $this->logger->trace('DEBUG', '######################################');
    }
    
    /**
     * Kill the specified process and all children
     * 
     * @param int $pid The pid of the process to kill
     * 
     * @return void 
     */
    function killProcess($pid)
    {
        $pid_kill = array($pid);
        while ($pid > 0) {
            $cmd = 'ps -ef| awk \'$3 == \'' . $pid . '\' { print $2 }\'';
            exec($cmd, $std_out, $exit_code);
            if (count($std_out) > 0) {
                $pid = $std_out[0];
                array_unshift($pid_kill,$pid);
            } else {
                $pid = 0;
            }
            unset($std_out);
        }
        //print_r($pid_kill);
        foreach ($pid_kill as $pid) {
            posix_kill($pid, 9);
        }
    }
    
    /**
     * Verify if process process associated with the ortro job is running.
     * 
     * @param int $pid   The pid of the process to kill
     * @param int $jobId The ortro id of the job to kill
     * 
     * @return bool
     */
    function killJobCheck($pid, $jobId)
    {
        $cmd = 'ps -ef | awk \'$2 == \'' . $pid . '\' { print $10 }\'';
        exec($cmd, $std_out, $exit_code);
        if (count($std_out)>0 && $std_out[0] == $jobId) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Kill the specified job
     * 
     * @param int $job_id The id of the job to kill
     * 
     * @return boolean true on success
     */
    function killJob($id_job)
    {
        $result     = false;
        $killLogger = new LogUtil('killJob');
        $killLogger->trace('DEBUG', 'Trying to kill job: ' . $id_job);
        $dbUtil = new DbUtil();
        $dbh    = $dbUtil->dbOpenConnOrtro();
        //Retrieve msg_exec contains info about pid
        $jobs_infos = $dbUtil->dbQuery($dbh, $dbUtil->getJobStatusDetail($id_job),
                                             MDB2_FETCHMODE_ASSOC);
        $dbh = $dbUtil->dbCloseConn($dbh);
        unset($dbh);
        if ($jobs_infos[0]['status'] == 'R') {
            $temp = explode('pid=',rawurldecode($jobs_infos[0]['msg_exec']));
            $pid  = $temp[1];
            if ($this->killJobCheck($pid, $id_job)) {
                $killLogger->trace('DEBUG', 'Job ' . $id_job . 
                                            ' is running with pid: ' . $pid);
                $this->killProcess($pid);
                $killLogger->trace('DEBUG', 'Processes for Job ' . 
                                            $id_job . ' killed.');
            } else {
                $killLogger->trace('DEBUG', 'No process running for Job ' . 
                                             $id_job);
            }
            unset($temp);
            $result = true;
        } else {
            $killLogger->trace('DEBUG', 'Job ' . $id_job . ' is not running. Skip.');
        }
        unset($jobs_infos);
        return $result;
    }
    
    /**
     * Execute the job using php-cli in background
     * 
     * @param int    $id_job      The job id to execute
     * @param string $type        The type of the the plugin
     * @param string $category    The category of the the plugin
     * @param string $requestType The type of request (Crontab, xml-rpc,...)
     * 
     * @return void
     */
    function execJob ($id_job, $type, $category, $requestType='CRONTAB') 
    {
        $execLogger = new LogUtil('execJob');
        $execLogger->trace('DEBUG', "request type: " . $requestType);
        $script = '"' . ORTRO_PLUGINS . $category . DS . $type . DS .'core.php"';
        
        // Create the cmd line
        $cmdLine = $GLOBALS['conf']['env']['php_path'] . 
                   'php' . ' ' . 
                   $script . ' ' .
                   $id_job . ' ' . 
                   $requestType;
        
        $execLogger->trace('DEBUG', 'Executing job: ' . $cmdLine);
        
        switch ($requestType) {
        case 'XML-RPC':
            $bg_command = ' 1>' . ORTRO_LOG . 'ortro_error_XML_RPC_' . 
                          date($GLOBALS['conf']['env']['dateFormat']) . 
                          '.log 2>&1';
            exec($cmdLine . $bg_command, $output, $exit_code);
            return implode($output); //return a serialized output
            break;
        default: // Type request: CRONTAB
            $bg_command = ' 1>>' . ORTRO_LOG . 'ortro_error_' . 
                          date($GLOBALS['conf']['env']['dateFormat']) . 
                          '.log 2>&1 &';
            //execute the command in background
            system($cmdLine . $bg_command);
            break;
        }
    }
    
    /**
     * Perform the actions on the job start event
     * 
     * @param string $pluginName The name of the plugin
     * @param string $id_job     The job id
     * 
     * @return array The parameters required for the job execution
     */
    
    function startJobEvent($pluginName, $id_job)
    {
        
        $dbUtil = new DbUtil();
        $dbh    = $dbUtil->dbOpenConnOrtro();
        // Get all the info required for the job execution
        $jobs_infos = $dbUtil->dbQuery($dbh, 
                                        $dbUtil->getJobToExecInfo($id_job), 
                                        MDB2_FETCHMODE_ASSOC);
        
        if ($jobs_infos[0]['identity'] != 0) {
            //get the identity credential
            $identity = $dbUtil->dbQuery($dbh, 
                            $dbUtil->getIdentityById($jobs_infos[0]['identity']), 
                                          MDB2_FETCHMODE_ASSOC);
                                          
            $identity[0]['password']   = 
                $dbUtil->dbUnserialize($identity[0]['password']);
            $jobs_infos[0]['identity'] = $identity[0];
        }
        
        switch ($this->requestType) {
        case 'XML-RPC':
            $this->logger->trace('INFO', 'Executing job ' . $pluginName . 
                                         ' with id=' . $id_job . 
                                         ' requested via xml-rpc');
            break;
        default: // Type request: CRONTAB
            // Update the job start time and lock the process
            $msg_exec = 'Running with: id=' . $id_job . ' ,pid=' . getmypid();
            $dbUtil->dbExec($dbh, $dbUtil->setJobStart($id_job, $msg_exec));
            $this->logger->trace('INFO', 'Executing job ' . $pluginName . 
                                         '. ' . $msg_exec);
            break;
        }
        
        //Get dynamic info used eventually if the job is a workflow step
        $dynamic_params = $dbUtil->dbQuery($dbh, 
                            $dbUtil->getWorkflowStepDynamicParams($id_job));
        if (isset($dynamic_params[0])) {        
            $jobs_infos[0]['dynamic_params'] = 
                $dbUtil->dbUnserialize($dynamic_params[0][0]);
        }
        
        //Get all notifications for this job
        $resultSet = $dbUtil->dbQuery($dbh, 
                                      $dbUtil->getNotifyInfoByJobId($id_job, 3), 
                                      MDB2_FETCHMODE_ASSOC);
        for ($i = 0; $i < sizeof($resultSet); $i++) {
            $row = $resultSet[$i];
            $row['parameters'] = $dbUtil->dbUnserialize($row['parameters']);
            if ($row['identity'] != 0) {
                //get the identity credential
                $identity = $dbUtil->dbQuery($dbh, 
                                             $dbUtil->getIdentityById($row['identity']), 
                                             MDB2_FETCHMODE_ASSOC);
                                             
                $identity[0]['password'] = $dbUtil->dbUnserialize($identity[0]['password']);
                    
                $row['parameters']['identity'] = $identity[0];
                //Add the id_job to able to create a unique notify
                $row['parameters']['id_job'] = $row['id_job']; 
            }
            include_once 'notifyUtil.php';
            // Send notify
            $notify = new NotifyUtil();
            $notify->sendNotify($row['label'], $row['parameters'], $attachments);
        }        
        
        $dbh = $dbUtil->dbCloseConn($dbh);
        unset($dbh);
        
        // Unserialize the parameters
        $jobs_infos[0]['parameters'] = 
            $dbUtil->dbUnserialize($jobs_infos[0]['parameters']);
        
        return $jobs_infos[0];
    }
    
    /**
     * Perform the actions on the job end event:
     * - Update database job info
     * - Send notification
     * 
     * @param string $pluginName     The name of the plugin
     * @param string $id_job         The job id
     * @param string $result         The job result (0 = error, 1 = success)
     * @param string $msg_exec       The job message result
     * @param array  $attachments    The job info used as attachements by the 
     *                               notification plugins
     * @param string $dynamic_params Job output fields serialized used eventually 
     *                               as parameters in a job step if required
     * 
     * @return void
     */
    function endJobEvent($pluginName, $id_job, $result, $msg_exec, 
                         $attachments, $dynamic_params='', $enable_notification=true)
    {
        //Inserts HTML line breaks before all newlines for message
        $msg_exec = nl2br($msg_exec);
        
        $dbUtil = new DbUtil();
        $this->logger->trace('DEBUG', 'Result for job ' . $id_job . 
                                      ' -> ' . $result);
        $this->logger->trace('INFO', 'Job ' . $pluginName . 
                                     ' with id=' . $id_job . ' done.');
        switch ($this->requestType) {
        case 'XML-RPC':
            include_once 'XML/RPC/Server.php';
            $output = array(
                       'result' => new XML_RPC_Value($result, "boolean"),
                       'msg_exec' => new XML_RPC_Value($msg_exec, "string"),
                       'txt_attachment' => new XML_RPC_Value($attachments['txt'], 
                                                             "string"),
                       'html_attachment' => new XML_RPC_Value($attachments['html'], 
                                                              "string")
                            );

            $serialized_output = $dbUtil->dbSerialize($output);
            echo $serialized_output; //return a serialized output
            break;
        default: // Type request: CRONTAB
            
            $dbh = $dbUtil->dbOpenConnOrtro();
            //Check for retry on job failure
            $resultSet    = $dbUtil->dbQuery($dbh, 
                                              $dbUtil->getJobProperties($id_job), 
                                              MDB2_FETCHMODE_ASSOC);
            $properties   = $dbUtil->dbUnserialize($resultSet[0]['properties']);
            $num_failures = $resultSet[0]['num_failures'];
            if ($properties['max_check_attempts'] > $num_failures && $result == 0) {
                $num_failures = $num_failures + 1;
                $this->logger->trace('DEBUG', 'Job ' . $id_job . 
                                              ' retry on failure attempt ' . 
                                              $num_failures . ' of ' . 
                                              $properties['max_check_attempts']);
                $dbUtil->dbExec($dbh, $dbUtil->setJobEnd($id_job, 
                                                          $result,
                                                          $msg_exec,
                                                          $num_failures));
                $this->logger->trace('DEBUG', 'Job ' . $id_job . 
                                     ' : Wait for retry execution ' . 
                                     $properties['delay_retry'] . 
                                     ' seconds');
                sleep($properties['delay_retry']);
                //Check for lock status
                $rows = $dbUtil->dbQuery($dbh, $dbUtil->getJobStatus($id_job));
                
                $job_status = $rows[0][0];
                if ($job_status == 'W') {
                    $resultSet = $dbUtil->dbQuery($dbh, 
                                                   $dbUtil->getJobType($id_job), 
                                                   MDB2_FETCHMODE_ASSOC);
                }
                $dbh = $dbUtil->dbCloseConn($dbh);
                unset($dbh);
                if ($job_status == 'W') {
                    $this->execJob($id_job, 
                                   $resultSet[0]['label'],
                                   $resultSet[0]['category']);
                } else {
                    $this->logger->trace('DEBUG', '[id_job: ' . $id_job . 
                                                  '] Job status is: ' . $job_status.
                                                  ' -> skip execution');
                }
            } else {
                $dbUtil->dbExec($dbh, $dbUtil->setJobEnd($id_job, 
                                                          $result, 
                                                          $msg_exec));
                
                if ($enable_notification === true) {
                    //Get all notifications for this job
                    $resultSet = $dbUtil->dbQuery($dbh, 
                                                  $dbUtil->getNotifyInfoByJobId($id_job), 
                                                  MDB2_FETCHMODE_ASSOC);
                    
                                                  
                                     
                    for ($i = 0; $i < sizeof($resultSet); $i++) {
                        $row = $resultSet[$i];
                        
                        $notify_on = explode('-', $row['notify_on']);
                        array_shift($notify_on);
                        array_pop($notify_on);
                        
                        if (sizeof(array_intersect($notify_on, array($result, '2'))) > 0) {
                            $row['parameters'] = $dbUtil->dbUnserialize($row['parameters']);
                            if ($row['identity'] != 0) {
                                //get the identity credential
                                $identity = $dbUtil->dbQuery($dbh, 
                                                             $dbUtil->getIdentityById($row['identity']), 
                                                             MDB2_FETCHMODE_ASSOC);
                                                             
                                $identity[0]['password'] = $dbUtil->dbUnserialize($identity[0]['password']);
                                    
                                $row['parameters']['identity'] = $identity[0];
                                //Add the id_job to able to create a unique notify
                                $row['parameters']['id_job'] = $row['id_job']; 
                            }
                            include_once 'notifyUtil.php';
                            // Send notify
                            $notify = new NotifyUtil();
                            $notify->sendNotify($row['label'], $row['parameters'], $attachments);
                        }
                    }
                }
                $dbh = $dbUtil->dbCloseConn($dbh);
                unset($dbh);
            }
            break;
        }
        
        /* WORKFLOW ACTIONS */
        // Check if the job is a workflow step
        $dbh = $dbUtil->dbOpenConnOrtro();
        //Get all steps with this $id_job with status R or W (if exist one)
        $workflowsStep = $dbUtil->dbQuery($dbh, 
                            $dbUtil->checkWorkflowStepInfoByIdJob($id_job), 
                                           MDB2_FETCHMODE_ASSOC);
        if (count($workflowsStep) != 0) {
            //The job is a step of at least a workflow
            for ($i = 0; $i < sizeof($workflowsStep); $i++) {
                $id_workflow = $workflowsStep[$i]['id_workflow'];
                $step        = $workflowsStep[$i]['step'];
                $log_prefix  = '[workflow: ' . $id_workflow . 
                               '][step: ' . $step . ']';
                $this->logger->trace('DEBUG', $log_prefix . 
                                              ' -> [job:' . $id_job . ']');

                // Update the step status in accord to $result
                $dbUtil->dbExec($dbh, 
                                 $dbUtil->updateWorkflowStepStatus($id_workflow, 
                                                                   $step, 
                                                                   $result));
                
                //Get the job to execute on result value
                switch ($result) {
                case 1:
                    //the job to execute on success
                    $next_step = $workflowsStep[$i]['on_success'];
                    $when      = $workflowsStep[$i]['on_success_when'];
                    break;
                case 0:
                    //the job to execute on success
                    $next_step = $workflowsStep[$i]['on_error'];
                    $when      = $workflowsStep[$i]['on_error_when'];
                    break;
                }
                $this->logger->trace('DEBUG', $log_prefix . 
                                              ' [next_step: ' .$next_step. 
                                              '][when:' . $when.']');
                if ($next_step != 0 ) {
                    //There is a next step
                    $this->logger->trace('DEBUG', $log_prefix . 
                                                  ' -> Next step found');
                    $log_prefix     = '[workflow: ' . $id_workflow . 
                                      '][step: ' . $next_step . ']';
                    $next_step_info = $dbUtil->dbQuery($dbh, 
                        $dbUtil->getWorkflowInfoById($id_workflow, $next_step), 
                                                        MDB2_FETCHMODE_ASSOC);
                    $id_job         = $next_step_info[0]['id_job'];
                    if ($id_job == 0) {
                        //The job to exec is not defined -> 
                        //force the end of the workflow.
                        $this->logger->trace('DEBUG', $log_prefix . 
                                                      '[id_job: ' . $id_job . 
                                                      ']The job to exec is not '.
                                                      'defined -> force the end'.
                                                      'of the workflow. ');
                        $dbUtil->dbExec($dbh, 
                            $dbUtil->updateWorkflowStatus($id_workflow, 'W'));
                    } else {
                        //Store the $dynamic_params values in the database
                        $dbUtil->dbExec($dbh, 
                            $dbUtil->setWorkflowStepDynamicParams($id_workflow, 
                                                                  $next_step, 
                                    $dbUtil->dbSerialize($dynamic_params)));
                        if ($when == 'R') {
                            $this->logger->trace('DEBUG', 
                                                 $log_prefix . 
                                                 ' -> Trying to exec immediatly');
                            // Update the step status to running
                            $dbUtil->dbExec($dbh, 
                                $dbUtil->updateWorkflowStepStatus($id_workflow, 
                                                                  $next_step, 
                                                                  'R'));
                            //Get all info needed to execute the job
                            // -- Check for lock status
                            $rows       = $dbUtil->dbQuery($dbh, 
                                            $dbUtil->getJobStatus($id_job));
                            $job_status = $rows[0][0];
                            if ($job_status == 'W') {
                                $resultSet = $dbUtil->dbQuery($dbh, 
                                    $dbUtil->getJobType($id_job),
                                                               MDB2_FETCHMODE_ASSOC);
                                //execute the job immediatly
                                $this->execJob($id_job, 
                                               $resultSet[0]['label'],
                                               $resultSet[0]['category']);
                            } else {
                                $this->logger->trace('DEBUG', 
                                                     $log_prefix . 
                                                     '[id_job: ' . $id_job . 
                                                     '] Job status is: ' . 
                                                     $job_status . 
                                                     ' -> skip execution');
                            }
                        } else {
                            //wait for job schedule
                            // Update the step status to wait
                            $this->logger->trace('DEBUG', 
                                                 $log_prefix . ' -> The step: ' . 
                                                 $next_step . 
                                                ' is waiting for job schedule now');
                            $dbUtil->dbExec($dbh, 
                                $dbUtil->updateWorkflowStepStatus($id_workflow, 
                                                                  $next_step, 
                                                                  'W'));
                        }
                    }
                } else {
                    //This is the last step update the workflow status to wait
                    $dbUtil->dbExec($dbh, 
                                     $dbUtil->updateWorkflowStatus($id_workflow, 
                                                                   'W', 
                                                                   time()));
                }
                
            }
        }
        //Close DB connection used for workflow actions
        $dbh = $dbUtil->dbCloseConn($dbh);
        unset($dbh);
    }
    
    /**
     * Allows to achive and handle the retention of the job results
     * 
     * @param string $id_job The job id (i.e. filesystem, db, ...)  
     * @param array  $data   Archivation info.
     * 
     * @return void
     */
    function archiveJobResult($id_job, $data)
    {
        
        $logger = new LogUtil('Archive');
        $logger->trace('DEBUG', 'Apply retention policy for job: ' . $id_job);
        switch ($this->requestType) {
        case 'XML-RPC':
                $logger->trace('DEBUG', 
                               'Skipped the job is executed via xml-rpc');
            break;
        default: // Type request: CRONTAB
            $log_label = '[' . $id_job . ']';
            $logger->trace('DEBUG', $log_label . ' -> mode: ' . 
                                    $data['archive_mode']);
            $logger->trace('DEBUG', $log_label . ' -> retention: ' . 
                                    $data['retention']);
            switch ($data['archive_mode']) {
            case 'FILESYSTEM':
                //apply retention policy
                $handle = opendir($data['path']);
                if ($handle) {
                    while (false !== ($file = readdir($handle))) {
                        $pos = strpos($file, $data['filename']);
                        if ($pos !== false && $pos=='0') {
                            if (filectime($data['path'] . $file) < 
                             (time() - $data['retention'] * 60 * 60 * 24)) {
                                   $logger->trace('DEBUG', 
                                                  $log_label . 
                                                  'Trying to remove ' . 
                                                  $data['path'] . 
                                                  $file . ' ...');
                                unlink($data['path'] . $file);
                                $logger->trace('DEBUG', 
                                               $log_label . 'Removed.');
                            }
                        }
                    }
                }
                break;
            default:
                //store in the database
                $dbUtil = new DbUtil();
                $dbh    = $dbUtil->dbOpenConnOrtro();            
                //archive the job result
                $dbUtil->dbExec($dbh, $dbUtil->archiveJobResult($id_job));
                //apply retention
                
                $date   = time() - ($data['retention'] * 60 * 60 * 24);
                $result = $dbUtil->dbExec($dbh, 
                    $dbUtil->applyRetentionPolicy($id_job, $date));
                $logger->trace('DEBUG', $log_label . 
                                        ' deleted records -> ' . 
                                        $result);
                $dbh = $dbUtil->dbCloseConn($dbh);
                unset($dbh);
                break;
            }
            $logger->trace('DEBUG', $log_label . ' -> Done.');
            break;
        }
    }
    
    /**
     * Trace the exception occorred in the plugin in the error file
     * 
     * @param string $file_name The name of the plugin
     * @param object $e         The exception object
     * 
     * @return void
     */
    function traceError($file_name, $e) 
    {
        $loggerError = new LogUtil($file_name, ORTRO_LOG . 'error');
        $loggerError->trace('ERROR', "Exception catched:");
        $loggerError->trace('ERROR', "File: ".$e->getFile()."");
        $loggerError->trace('ERROR', "Message: ".$e->getMessage()."");
        $loggerError->trace('ERROR', "Line: ".$e->getLine()."");
        $loggerError->trace('ERROR', "Code: ".$e->getCode()."");
        $loggerError->trace('ERROR', "Trace: ".$e->getTraceAsString());
    }
    
    /**
     * The purpose of this method is to calculate the next execution time
     * from a specified crontab entry
     * 
     * @param string $m        The cron value for minute in the Ortro format
     * @param string $h        The cron value for hour in the Ortro format
     * @param string $dom      The cron value for day of month in the Ortro format
     * @param string $mon      The cron value for month of year in the Ortro format
     * @param string $dow      The cron value for day of week in the Ortro format
     * @param int    $ref_time The reference time for which the next time should be 
     *                         searched which matches. By default, take the current time
     * 
     * @return int $date The unix timestamp of the next matched date for cron.
     */
    function calcNextCronDate($m, $h, $dom, $mon, $dow, $ref_time=0)
    {
      $expanded = array($this->getCronEntries($m, 'minute'),
                        $this->getCronEntries($h, 'hour'),
                        $this->getCronEntries($dom, 'day_of_month'),
                        $this->getCronEntries($mon, 'month'),
                        $this->getCronEntries($dow, 'day_of_week')
                        );
      
      //Calculating time:
      // =================
      if ($ref_time == 0) {
          $ref_time = time();
      }
      if ($expanded[2][0] != '*' && $expanded[4][0] != '*') {
          // Special check for which time is lower (Month-day or Week-day spec):
          $bak = $expanded[4];
          $expanded[4] = '*';
          $t1 = $this->calcTime($ref_time, $expanded);
          $expanded[4] = $bak;
          $expanded[2] = '*';
          $t2 = $this->calcTime($ref_time, $expanded);
          return $t1 < $t2 ? $t1 : $t2;
      } else {
          // No conflicts possible:
          return $this->calcTime($ref_time, $expanded);
      } 
    }
      
    /**
     * This method parse the Ortro crontab string and return the crontab entries
     * 
     * @param string $cronString The Ortro crontab string to parse
     * @param string $typeCron   The crontab field we are parsing
     * 
     * @return array $cronValues Contains the values for the specified entry.
     */
    function getCronEntries($cronString, $typeCron){
        
        $cronValues = split('-', $cronString);
        array_shift($cronValues);
        array_pop($cronValues);
        
        switch ($typeCron) {
            case 'minute':
                if (count($cronValues) == 60) {
                    $cronValues = array('*');
                }
            break;
            case 'hour':
                if (count($cronValues) == 24) {
                    $cronValues = array('*');
                }
            break;
            case 'day_of_month':
                if (count($cronValues) == 31) {
                    $cronValues = array('*');
                }
            break;
            case 'month':
                if (count($cronValues) == 12) {
                    $cronValues = array('*');
                }
            break;
            case 'day_of_week':
                if (count($cronValues) == 7) {
                    $cronValues = array('*');
                }
            break;
        }
        return $cronValues;
    }
    
    /**
     * Get the next entry in list
     * 
     * @param string $x        The value for test
     * @param array  $to_check The entries to test
     * 
     * @return mixed Next entry in list or false if is the highest entry found
     */
    function getNearest($x, $to_check) {
        for ($i = 0; $i < count($to_check); $i++) {
            if ($to_check[$i] >= $x) {
                return $to_check[$i] ;
            }
        }
        return false;
    }
    
    /**
     * Calculate the next concrete date for execution from a crontab entry
     * 
     * @param int   $ref_time 
     * @param array $expanded
     * 
     * @return int The next concrete date in the Unix timestamp format
     */
    function calcTime($ref_time, $expanded)
    {
    
        $dayweek['0'] = 'Sunday';
        $dayweek['1'] = 'Monday';
        $dayweek['2'] = 'Tuesday';
        $dayweek['3'] = 'Wednesday';
        $dayweek['4'] = 'Thursday';
        $dayweek['5'] = 'Friday';
        $dayweek['6'] = 'Saturday';
        
        $now_min  = (int) date('i', $ref_time);
        $now_hour = date('H', $ref_time);
        $now_mday = date('d', $ref_time);
        $now_mon  = date('m', $ref_time);
        $now_wday = date('w', $ref_time);
        $now_year = date('Y', $ref_time);
        
        // Notes on variables set:
        // $now_... : the current date, fixed at call time
        // $dest_...: date used for backtracking. At the end, it contains
        //            the desired lowest matching date
    
        $dest_mon  = $now_mon;
        $dest_mday = $now_mday;
        $dest_wday = $now_wday;
        $dest_hour = $now_hour;
        $dest_min  = $now_min + 1;
        $dest_year = $now_year;
    
        $dest_mon_error = 0;
        $dest_mday_error = 0;
        $dest_year_error = 0;
        
        while ($dest_year <= ($now_year + 1)){
            // Check month:
            if ($expanded[3][0] != '*') {
                $dest_mon = $this->getNearest($dest_mon, $expanded[3]);
                if ($dest_mon === false) {
                    $dest_mon = $expanded[3][0];
                    $dest_year++;
                }
            }
            // Check for day of month:
            if ($expanded[2][0] != '*') {
                if ($dest_mon != $now_mon) {
                    $dest_mday = $expanded[2][0];
                } else {
                    $dest_mday = $this->getNearest($dest_mday,$expanded[2]);
                    if ($dest_mday === false) {
                        // Next day matched is within the next month. ==> redo it
                        $dest_mday = $expanded[2][0];
                        $dest_mon++;
                        if ($dest_mon > 12) {
                            $dest_mon = 1;
                            $dest_year++;
                        }
                        
                        continue;
                    }
                }
            } else {
                $dest_mday = ($dest_mon == $now_mon ? $dest_mday : 1);
            }
            // Check for day of week:
            if ($expanded[4][0] != '*') {
                $dest_wday = $this->getNearest($dest_wday,$expanded[4]);
                
                if ($dest_wday === false) { 
                    $dest_wday = $expanded[4][0];
                }
                if ($dest_mon != $now_mon) {
                    $dest_mday = 1;
                }
    
                $t = mktime(0,0,0,$dest_mon,$dest_mday,$dest_year);

                $nt = strtotime($dayweek[$dest_wday],$t);
 
                foreach ($expanded[4] as $available_day) {
                    $nt_tmp = strtotime("next $dayweek[$available_day]",$t);
                    if ($nt_tmp < $nt) {
                        $nt = $nt_tmp;
                    }
                }
                $mday = date('d', $nt);
                $mon  = date('m', $nt);
                $year = date('Y', $nt); 
                
                if ($mon != $dest_mon || $year != $dest_year) {
                    //backtracking;
                    $dest_mon  = $mon;
                    $dest_year = $year;
                    $dest_mday = 1;
                    $dest_wday = date('w', mktime(0,0,0,$dest_mon,$dest_mday,$dest_year));
                    continue;
                }
    
                $dest_mday = $mday;
            } else {
                if (!$dest_mday) {
                    $dest_mday = ($dest_mon == $now_mon ? $dest_mday : 1);
                }
            }
            
            // Check for hour
            if ($expanded[1][0] != '*') {
                if ($dest_mday != $now_mday) {
                    $dest_hour = $expanded[1][0];
                } else {
                    // Checking for next hour $dest_hour
                    if (($dest_hour = $this->getNearest($dest_hour,$expanded[1])) === false) {
                        // Hour to match is at the next day ==> redo it
                        $dest_hour = $expanded[1][0];
                        
                        $t  = mktime($dest_hour, $dest_min, 0, $dest_mon, $dest_mday, $dest_year);
                        $nt = strtotime("+ 1 day", $t);
                        
                        $dest_mday = date('d', $nt);
                        $dest_mon  = date('m', $nt);
                        $dest_year = date('Y', $nt);
                        $dest_wday = date('w', $nt);
    
                        continue;
                    }
                }
            } else {
                $dest_hour = ($dest_mday == $now_mday ? $dest_hour : 0);
            }
    
            // Check for minute
            if ($expanded[0][0] != '*') {
                if ($dest_hour != $now_hour) {
                    $dest_min = $expanded[0][0];
                } else {
                    if (($dest_min = $this->getNearest($dest_min,$expanded[0])) === false) {
                        // Minute to match is at the next hour ==> redo it
                        if ($dest_mday <= $now_mday){
                            $dest_min = $expanded[0][0];
                            $t = mktime($dest_hour, $dest_min, 0, $dest_mon, $dest_mday, $dest_year);
                            $nt = strtotime("+ 1 hour",$t);
                        
                            $dest_hour = date('H', $nt);
                            $dest_mday = date('d', $nt);
                            $dest_mon  = date('m', $nt);
                            $dest_year = date('Y', $nt);
                            $dest_wday = date('w', $nt);
                            continue;
                        }
                    }
                }
            } else {
                $dest_min = ($dest_hour == $now_hour ? $dest_min : 0);
            }
    
            // We did it !!
            if (checkdate($dest_mon,$dest_mday,$dest_year)){
                $date = mktime($dest_hour,$dest_min,0,$dest_mon,$dest_mday,$dest_year);
            } else {
                if ($dest_mon_error != $dest_mon && $dest_mday_error != $dest_mday && $dest_year_error != $dest_year){
                    $dest_mon_error = $dest_mon;
                    $dest_mday_error = $dest_mday;
                    $dest_year_error = $dest_year;
                    $nt = mktime($dest_hour,$dest_min,0,$dest_mon,$dest_mday,$dest_year);
                        
                    $dest_hour = date('H', $nt);
                    $dest_mday = date('d', $nt);
                    $dest_mon  = date('m', $nt);
                    $dest_year = date('Y', $nt);
                    $dest_wday = date('w', $nt);
                    continue;
                } else {
                    $date = 0;
                }
            }
            return $date;
        }
    }
}
?>