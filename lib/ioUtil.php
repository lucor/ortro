<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Simple functions for io functionality
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
 * Read a file
 * 
 * @param string $filePath absolute path of the file
 * @param string $fileName file name
 *  
 * @return string the file content
 */
function readFiles($filePath, $fileName)
{
    $filename = $filePath . $fileName;
    $handle   = fopen($filename, "r");
    $contents = fread($handle, filesize($filename));
    fclose($handle);
    return $contents;
}

/**
* Create an html file
* 
* @param string $fileData file data
* @param string $filePath absolute path of the file
* @param string $fileName file name
*  
* @return boolean true on success
*/
 
function createHTMLFile($fileData, $filePath, $fileName)
{
    $temp_file = $fileName . '_' . date('Y_m_d_H_i') . '.html';
    createFile($fileData, $filePath, $temp_file);
}

 

/**
* Create a generic file
* 
* @param string $fileData file data
* @param string $filePath absolute path of the file
* @param string $fileName file name
* 
* @return boolean true on success
*/
function createFile($fileData, $filePath, $fileName)
{
    //Create the folder if not exists
    if (!is_dir($filePath)) {
        mkdir($filePath, 0755, true);
    }
    $temp_file = $filePath . $fileName;
    $fh        = fopen($temp_file, 'w+');
    fwrite($fh, $fileData);
    return fclose($fh);
}



/**
 * Create a file of the specified type starting from a query result
 * Note: The file is zipped if its size is greater than 
 *       "zip_file_threshold" parameter defined in the global configuration file.
 * 
 * @param array  $resultset                query resultset
 * @param string $filePath                 absolute path of the file
 * @param string $fileName                 file name
 * @param string $fileType                 file type/extension (e.g. txt,csv...)
 * @param string $colSeparator             char separator for column 
 *                                         (e.g. ";", "\t", ...)
 * @param string $rowSeparator             char separator for row (e.g. "\n"...)
 * @param string $print_column_header      print the column headers in the file 
 *                                         (default true)
 * @param string $append_data_to_file_name append the date to the filename 
 *                                         (default true)
 * @param string $compress_file            compress the output file if its size 
 *                                         is greater than [env][zip_threshold] 
 *                                         value (default true)
 * 
 * @return string created file (absolute path) 
 */

function createFileByQuery($resultset, $filePath, $fileName, 
                           $fileType, $colSeparator, $rowSeparator, 
                           $print_column_header = true, 
                           $append_data_to_file_name = true, $compress_file = true)
{
    $fileData    = '';
    $column_name = array_keys($resultset[0]);
    
    if ($print_column_header) {
        $fileData = implode($colSeparator, $column_name) . $rowSeparator;
    }
    
    $num_cols = count($column_name);

    foreach ($resultset as $row) {
        $i       = 1;
        $rowData = '';
        foreach ($column_name as $name) {
            switch ($fileType) {
            case 'csv':
                if (strlen($row[$name]) > 31000) {
                    //reached the max cell characters limit in a worksheet
                    // cell for excel
                    //trying to remove multiple spaces
                    $row[$name] = preg_replace('/\s+/', ' ', $row[$name]);
                }
                $rowData .= '"' . str_replace('"', '""', $row[$name]) . '"';
                break;
            default:
                $rowData .= $row[$name];
                break;
            }
            if ($i < $num_cols) {
                $rowData .= $colSeparator;
                $i++;
            }
        }
        $fileData .= $rowData . $rowSeparator;
    }
    //Create the folder if not exists
    if (!is_dir($filePath)) {
        mkdir($filePath, 0755, true);
    }
    
    if ($append_data_to_file_name) {
        $temp_file = $filePath . $fileName . '_' . date('Y_m_d_H_i') . '.';
    } else {
        $temp_file = $filePath . $fileName . '.';
    }
    
    $fh = fopen($temp_file . $fileType, 'w+');
    fwrite($fh, $fileData);
    fclose($fh);
    if ($compress_file && filesize($temp_file . $fileType) > 
        $GLOBALS['conf']['env']['zip_threshold'] && 
        is_file($GLOBALS['conf']['env']['zip_path'])) {
        $cmdLine = $GLOBALS['conf']['env']['zip_path'] . ' -j ' . 
                   $temp_file .'zip' . ' ' . $temp_file . $fileType;
        exec($cmdLine, $stdout, $exit_code);
        if ($exit_code == '0') {
            //remove the uncompressed file
            unlink($temp_file . $fileType);
            $fileZipped = true;
        }
    }
    if ($fileZipped) {
        return $temp_file . 'zip';
    } else {
        return $temp_file . $fileType;
    }
}


/**
 * Remove directories, even if they contain files or subdirectories.
 * 
 * @param string $dir absolute path of the file
 * 
 * @return void 
 */

function removeDirectory($dir) 
{
    $dir          = $dir . DIRECTORY_SEPARATOR;
    if (!is_dir($dir) || (strpos($dir, '..') !== false)) {
        return false;
    }
    $dir_contents = scandir($dir);
    foreach ($dir_contents as $item) {
        if (is_dir($dir.$item) && $item != '.' && $item != '..') {
            removeDirectory($dir.$item.DIRECTORY_SEPARATOR);
        } elseif (file_exists($dir.$item) && $item != '.' && $item != '..') {
            unlink($dir.$item);
        }
    }
    rmdir($dir);
}


/**
 * This function return a array with all files into a param directory
 * 
 * @param string $rootDir absolute path of the file
 * 
 * @param array  $allData name of files list
 * 
 * @return array
 */

function scanDirectories($rootDir, $allData=array()) 
{
    $invisibleFileNames = array(
        ".", 
        "..", 
        ".DS_Store",
        "backup"
    );
    $dirContent         = scandir($rootDir);
    foreach ($dirContent as $key => $content) {
        $path = $rootDir.DS.$content;
        if (!in_array($content, $invisibleFileNames)) {
            if (is_file($path) && is_readable($path)) {
                $allData[] = $path;
            } elseif (is_dir($path) && is_readable($path)) {
                $allData = scanDirectories($path, $allData);
            }
        }
    }
    return $allData;
}
?>