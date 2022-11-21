<?php

echo "<pre>";

/**
 * Get Folders and files from directori and all sub directroies
 *
 * @param [type] $dir
 * @param array $fileType
 * @param string $search_by_pattern_or_filename
 * @return object
 */
function list_folder_files($dir, array $fileType = [], string $search_by_pattern_or_filename = ''): object
{
    $search = $search_by_pattern_or_filename;
    $fileInfo = scandir($dir);

    if (!defined("GLOBAL_SCOPE_VAR")) {
        define("GLOBAL_SCOPE_VAR", '_ENV');
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


/**
 * Seach any text from string file
 *
 * @param [string or array] $filePath
 * @param string $search
 * @param boolean $case_sensitive
 * @return array
 */
function search_from_file($filePath, $search = '', $case_sensitive = true): array
{
    $results = [
        'search' => $search,
        'total' => 0,
        'result' => [],
    ];
    $case_sensitive = $case_sensitive ? '' : 'i';
    if (!empty($filePath) && !empty($search)) {
        if (!is_array($filePath)) {
            $filePath = [$filePath];
        }
        foreach ($filePath as $file) {
            if (is_file($file)) {
                $content = file_get_contents("$file");
                preg_match_all(preg_quote("/{$search}/{$case_sensitive}"), $content, $matched);
                if (!empty($matched[0] ?? [])) {
                    $count = count($matched[0]);
                    $results["$file::$count"] = $matched[0];
                }
            }
        }
    }
    $results['total'] = count($results, COUNT_RECURSIVE) - 1;
    return $results;
}

/**
 * Seach any text from string file
 *
 * @param [string or array] $filePath
 * @param string $pattern
 * @return array
 */
function search_from_file_by_regex($filePath, $pattern = ''): array
{
    $results = [
        'search' => $pattern,
        'total' => 0,
        'result' => [],
    ];
    if (!empty($filePath) && !empty($pattern)) {
        if (!is_array($filePath)) {
            $filePath = [$filePath];
        }
        foreach ($filePath as $file) {
            if (is_file($file)) {
                $content = file_get_contents("$file");
                preg_match_all("$pattern", $content, $matched);
                if (!empty($matched[0] ?? [])) {
                    $count = count($matched[0]);
                    $results["{$file} ► {$count}"] = $matched[0];
                }
            }
        }
    }
    $results['total'] = count($results, COUNT_RECURSIVE) - 1;
    return $results;
}

function search_matched_lines_from_file_by_regex($filePath, $pattern = ''): array
{
    $results = [
        'search' => $pattern,
        'total' => 0,
        'result' => [],
    ];
    if (!empty($filePath) && !empty($pattern)) {
        if (!is_array($filePath)) {
            $filePath = [$filePath];
        }
        foreach ($filePath as $file) {
            if (is_file($file)) {
                if ($_file = fopen($file, "r")) {
                    $lineCount = 1;
                    while (!feof($_file)) {
                        $line = fgets($_file);
                        if (!empty($line)) {
                            preg_match("$pattern", $line, $matched);
                            if (!empty($matched[0] ?? '')) {
                                // $results['result'][$file] = $line;
                                // $results['result'][$file] = $matched;
                                $results['result'][$file] = "{$matched[0]} ►► {$lineCount}";
                            }
                        }
                        $lineCount++;
                    }
                    fclose($_file);
                }
            }
        }
    }
    $results['total'] = count($results, COUNT_RECURSIVE) - 1;
    return $results;
}


$dirs = list_folder_files('F:\tasks', ['.php']);
// print_r($dirs->files);
// print_r(search_from_file($dirs->files, 'AB2'));
// print_r(search_from_file_by_regex($dirs->files, '/\$is_text/i'));
print_r(search_matched_lines_from_file_by_regex($dirs->files, '/\$is_text/i'));
