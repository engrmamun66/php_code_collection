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

    public function list_folder_files($dir, string $global_scope_array_variable_name = '')
    {

        $fileInfo = scandir($dir);
        $all = [];

        if (!defined("GLOBAL_SCOPE_VAR")) {
            define("GLOBAL_SCOPE_VAR", $global_scope_array_variable_name);
            if (array_search('dirs', $GLOBALS[GLOBAL_SCOPE_VAR]) === false)
                $GLOBALS[GLOBAL_SCOPE_VAR]['dirs'] = [];
            if (array_search('files', $GLOBALS[GLOBAL_SCOPE_VAR]) === false)
                $GLOBALS[GLOBAL_SCOPE_VAR]['files'] = [];
        }

        if (is_dir($dir)) {
            $dir = str_replace('/', '/', rtrim($dir, '/\\'));
            foreach ($fileInfo as $folder) {
                if ($folder !== '.' && $folder !== '..') {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $folder) === true) {
                        $all[$folder . '/'] = $this->list_folder_files($dir . DIRECTORY_SEPARATOR . $folder);
                        if (!empty(GLOBAL_SCOPE_VAR)) {
                            array_unshift($GLOBALS[GLOBAL_SCOPE_VAR]['dirs'], $dir . DIRECTORY_SEPARATOR . $folder);
                        }
                    } else {
                        $all[$folder] = $folder;
                        if (!empty(GLOBAL_SCOPE_VAR)) {
                            array_push($GLOBALS[GLOBAL_SCOPE_VAR]['files'], $dir . DIRECTORY_SEPARATOR . $folder);
                        }
                    }
                }
            }
        }
        return $all;
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
