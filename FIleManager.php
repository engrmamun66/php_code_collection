<?php

class FileManager
{
    public $errors = null;
    public $directoies = [
        "woocommerce/",
        "woocommerce/single-product/",
        "woocommerce/single-product/tabs/",
        "woocommerce/single-product/add-to-cart/",
    ];

    public function dir_is_empty($dir)
    {
        return (is_dir($dir) && count(glob("$dir/*")) === 0) ? true : false;
    }

    public function copy_folder_files()
    {
        $this->create_directories();
        $this->create_files();
    }

    public function remove_folder_files(): void
    {
        if (is_dir(RNTMY_EMBED_THEME_DIR . 'woocommerce')) {
            $this->create_files(true);
            $this->remove_directories();
        }
    }

    public function create_directories()
    {
        foreach ($this->directoies as $dir) {
            $directory = RNTMY_EMBED_THEME_DIR . $dir;
            if (!is_dir($directory)) {
                mkdir($directory);
            }
        }
    }

    public function remove_directories()
    {
        $errors = [];
        foreach (array_reverse($this->directoies) as $dir) {
            $directory = RNTMY_EMBED_THEME_DIR . $dir;
            if (is_dir($directory)) {
                try {
                    rmdir($directory);
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }
        if (!empty($errors)) {
            $this->errors = implode('<br>', $errors);
        }
    }

    public function create_files($deleteFromTheme = false)
    {
        foreach ($this->directoies as $base_dir) {
            $files = $this->get_list_of('files', RNTMY_EMBED_PLUGIN_DIR . 'assets/' . $base_dir);
            foreach ($files as $file) {
                $themeFile = RNTMY_EMBED_THEME_DIR . $base_dir . $file;
                fclose(fopen($themeFile, "w"));
                if ($deleteFromTheme) {
                    unlink($themeFile);
                } else {
                    $pluginFile = RNTMY_EMBED_PLUGIN_DIR . 'assets/' . $base_dir . $file;
                    $content = file_get_contents($pluginFile);
                    file_put_contents($themeFile, $content);
                }
            }
        }
    }

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
                        $all[$folder . '/'] = $this->list_folder_files($dir . DIRECTORY_SEPARATOR . $folder, $fileType, $search_by_pattern_or_filename);
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
     * We can get folders or files list from a directory
     *
     * @param string $type @example 'files'|'folder'
     * @param string $base_dir
     * @param array $exclude @example ['readme.md', 'test.txt'];
     * @param boolean $withFullPath
     * @return array
     */
    public function get_list_of($type, $base_dir, $exclude = [], $withFullPath = false): array
    {
        $myList = [];

        $base_dir = rtrim($base_dir, '/\\') . '/';

        if ($handle = opendir($base_dir)) {

            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $fullPath = $base_dir . $file;
                    if ('files' == $type) {
                        if (is_file($fullPath) && !is_dir($fullPath) && !in_array($file, $exclude)) {
                            $file = $withFullPath ? $base_dir . $file : $file;
                            $myList[] = $file;
                        }
                    }
                    if ('folders' == $type) {
                        if (!is_file($fullPath) && is_dir($fullPath) && !in_array($file, $exclude)) {
                            $file = $withFullPath ? $base_dir . $file : $file;
                            $myList[] = $file;
                        }
                    }
                }
            }
            closedir($handle);
            return $myList;
        }
    }
}
