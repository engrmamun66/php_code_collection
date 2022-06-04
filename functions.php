<?php
function list_folder_files($dir, array $fileType = [], string $search_by_pattern_or_filename = ''): object
{
    $search = $search_by_pattern_or_filename;
    $fileInfo = scandir($dir);

    if (!defined("GLOBAL_SCOPE_VAR")) {
        define("GLOBAL_SCOPE_VAR", '_GET');
        $GLOBALS[GLOBAL_SCOPE_VAR]['___dirs'] = [];
        $GLOBALS[GLOBAL_SCOPE_VAR]['___files'] = [];
    }

    if (is_dir($dir)) {
        $dir = str_replace('/', '/', rtrim($dir, '/\\'));
        foreach ($fileInfo as $folder) {
            if ($folder !== '.' && $folder !== '..') {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $folder) === true) {
                    $all[$folder . '/'] = list_folder_files($dir . DIRECTORY_SEPARATOR . $folder, $fileType, $search_by_pattern_or_filename);
                    if (!empty(GLOBAL_SCOPE_VAR)) {
                        array_unshift($GLOBALS[GLOBAL_SCOPE_VAR]['___dirs'], $dir . DIRECTORY_SEPARATOR . $folder);
                    }
                } else {
                    $file = $folder;

                    /* -------------------------------------------------------------------------- */
                    /*                     Is seach by pattern or solid string                    */
                    /* -------------------------------------------------------------------------- */
                    if (!empty(GLOBAL_SCOPE_VAR)) {
                        if (preg_match('/^\//', $search)) {
                            $byPattern = true;
                        } else {
                            $byPattern = false;
                        }
                        /* -------------------------------------------------------------------------- */
                        /*                               Check Filtering                              */
                        /* -------------------------------------------------------------------------- */

                        if (!empty($fileType)) {
                            $extension = pathinfo($file, PATHINFO_EXTENSION);
                            if (is_array($fileInfo) && (in_array($extension, $fileType) || in_array(".$extension", $fileType))) {
                                /* -------------------------------------------------------------------------- */
                                /*                         Search File in select types                        */
                                /* -------------------------------------------------------------------------- */
                                if (!empty($search)) {
                                    if ($byPattern && preg_match($search, $file, $f)) {
                                        array_push($GLOBALS[GLOBAL_SCOPE_VAR]['___files'], $dir . DIRECTORY_SEPARATOR . $file);
                                    } elseif (!$byPattern && strpos($file, $search) !== false) {
                                        array_push($GLOBALS[GLOBAL_SCOPE_VAR]['___files'], $dir . DIRECTORY_SEPARATOR . $file);
                                    }
                                } else {
                                    array_push($GLOBALS[GLOBAL_SCOPE_VAR]['___files'], $dir . DIRECTORY_SEPARATOR . $file);
                                }
                            }
                        } else {
                            /* -------------------------------------------------------------------------- */
                            /*                               Searching Files                              */
                            /* -------------------------------------------------------------------------- */
                            if (!empty($search)) {
                                if ($byPattern && preg_match($search, $file)) {
                                    array_push($GLOBALS[GLOBAL_SCOPE_VAR]['___files'], $dir . DIRECTORY_SEPARATOR . $file);
                                } elseif (!$byPattern && strpos($file, $search) !== false) {
                                    array_push($GLOBALS[GLOBAL_SCOPE_VAR]['___files'], $dir . DIRECTORY_SEPARATOR . $file);
                                }
                            } else {
                                array_push($GLOBALS[GLOBAL_SCOPE_VAR]['___files'], $dir . DIRECTORY_SEPARATOR . $file);
                            }
                        }
                    }
                }
            }
        }
    }

    return (object) [
        'dirs' => $GLOBALS[GLOBAL_SCOPE_VAR]['___dirs'],
        'files' => $GLOBALS[GLOBAL_SCOPE_VAR]['___files'],
    ];
}