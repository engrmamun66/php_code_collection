<?php

/**
 * Plugin Name: AMS → Bangladesh Locations
 * Description: Whole bangladesh distric thana/upozilla's and unions are saved in Database
 * Plugin URI: 
 * Author: 
 * Version: 1.0.0
 * Author URI: 
 * Copyright: 
 * Text Domain: bdtu
 * Domain Path: /languages/
 */

defined('ABSPATH') || exit;
define('DBTU_PLUGIN_DIR', rtrim(plugin_dir_path(__FILE__), '/') . '/');
define('DBTU_TEXT_DOMAIN', 'intalpluging');

add_action('plugins_loaded', function () {
    load_plugin_textdomain('DBTU_TEXT_DOMAIN', false, DBTU_PLUGIN_DIR . 'language');
});
/* -------------------------------------------------------------------------- */
/*                               Creating Tables                              */
/* -------------------------------------------------------------------------- */

function bdtu_create_tables()
{
    global $wpdb;
    $table_divisions = $wpdb->prefix . 'bd_divisions';
    $table_districts = $wpdb->prefix . 'bd_districts';
    $table_thanas = $wpdb->prefix . 'bd_thanas';
    $table_unions = $wpdb->prefix . 'bd_unions';

    $wpdb->query("DROP TABLE if EXISTS " . $table_divisions);
    $wpdb->query("DROP TABLE if EXISTS " . $table_districts);
    $wpdb->query("DROP TABLE if EXISTS " . $table_thanas);
    $wpdb->query("DROP TABLE if EXISTS " . $table_unions);

    $sql = "CREATE TABLE `{$table_divisions}` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name_bl` varchar(255) NOT NULL,
        `name_en` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $sql .= "CREATE TABLE `{$table_districts}` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `division_id` int(11) NOT NULL,
        `name_bl` varchar(255) NOT NULL,
        `name_en` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $sql .= "CREATE TABLE `{$table_thanas}` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `district_id` int(11) NOT NULL,
        `name_bl` varchar(255) NOT NULL,
        `name_en` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $sql .= "CREATE TABLE `{$table_unions}` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `thana_id` int(11) NOT NULL,
        `name_bl` varchar(255) NOT NULL,
        `name_en` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";


    require_once ABSPATH . "wp-admin/includes/upgrade.php";
    dbDelta($sql);
}
register_activation_hook($file = __FILE__, $callback = "bdtu_create_tables");


/* -------------------------------------------------------------------------- */
/*                           Inserting Division Data                          */
/* -------------------------------------------------------------------------- */
function bdtu_insert_division_list()
{
    global $wpdb;
    $table_divisions = $wpdb->prefix . 'bd_divisions';
    $divisions = require_once DBTU_PLUGIN_DIR . 'includes/divisions.php';
    foreach ($divisions as $division) {
        $wpdb->insert($table_divisions, ['name_bl' => $division['name_bl'], 'name_en' => $division['name_en']]);
    }
}
register_activation_hook($file = __FILE__, $callback = "bdtu_insert_division_list");

/* -------------------------------------------------------------------------- */
/*                           Inserting District Data                          */
/* -------------------------------------------------------------------------- */
function bdtu_insert_district_list()
{
    global $wpdb;
    $table_districts = $wpdb->prefix . 'bd_districts';
    $districts = require_once DBTU_PLUGIN_DIR . 'includes/districts.php';
    foreach ($districts as $district) {
        $wpdb->insert($table_districts, [
            'name_bl' => $district['name_bl'],
            'name_en' => $district['name_en'],
            'division_id' => $district['division_id'],
        ]);
    }
}
register_activation_hook($file = __FILE__, $callback = "bdtu_insert_district_list");


/* -------------------------------------------------------------------------- */
/*                           Inserting Thana Data                          */
/* -------------------------------------------------------------------------- */
function bdtu_insert_thana_list()
{
    global $wpdb;
    $table_thanas = $wpdb->prefix . 'bd_thanas';
    $thanas = require_once DBTU_PLUGIN_DIR . 'includes/thanas.php';
    foreach ($thanas as $thana) {
        $wpdb->insert($table_thanas, [
            'name_bl' => $thana['name_bl'],
            'name_en' => $thana['name_en'],
            'district_id' => $thana['district_id'],
        ]);
    }
}
register_activation_hook($file = __FILE__, $callback = "bdtu_insert_thana_list");

/* -------------------------------------------------------------------------- */
/*                           Inserting Union Data                          */
/* -------------------------------------------------------------------------- */
function bdtu_insert_union_list()
{
    global $wpdb;
    $table_unions = $wpdb->prefix . 'bd_unions';
    $unions = require_once DBTU_PLUGIN_DIR . 'includes/unions.php';
    foreach ($unions as $union) {
        $wpdb->insert($table_unions, [
            'name_bl' => $union['name_bl'],
            'name_en' => $union['name_en'],
            'thana_id' => $union['thana_id'],
        ]);
    }
}
register_activation_hook($file = __FILE__, $callback = "bdtu_insert_union_list");


/* -------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------- */
/*                         Broadcast Function for all                         */
/* -------------------------------------------------------------------------- */
/* ------https://developer.wordpress.org/reference/classes/wpdb/#methods----- */
/* -------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------- */
if (!function_exists('getDivisions')) {
    function getDivisions()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'bd_divisions';
        return $wpdb->get_results("SELECT * FROM `{$table}` WHERE 1", OBJECT);
    }
}
if (!function_exists('getDivision')) {
    function getDivision($id)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'bd_divisions';
        return $wpdb->get_row("SELECT * FROM `{$table}` WHERE id={$id}", OBJECT);
    }
}
if (!function_exists('getDistricts')) {
    function getDistricts($division_id=false)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'bd_districts';
        $sql = "SELECT * FROM `{$table}` WHERE " . ($division_id ? 'division_id=' . $division_id : 1);
        return $wpdb->get_results($sql, OBJECT);
    }
}
if (!function_exists('getDistrict')) {
    function getDistrict($id)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'bd_districts';
        return $wpdb->get_row("SELECT * FROM `{$table}` WHERE id={$id}", OBJECT);
    }
}
if (!function_exists('getThanas')) {
    function getThanas($distict_id=false)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'bd_thanas';
        $sql = "SELECT * FROM `{$table}` WHERE " . ($distict_id ? 'district_id=' . $distict_id : 1);
        return $wpdb->get_results($sql, OBJECT);
    }
}
if (!function_exists('getThana')) {
    function getThana($id)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'bd_thanas';
        return $wpdb->get_row("SELECT * FROM `{$table}` WHERE id={$id}", OBJECT);
    }
}
if (!function_exists('getUnions')) {
    function getUnions($thana_id=false)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'bd_unions';
        $sql = "SELECT * FROM `{$table}` WHERE " . ($thana_id ? 'district_id=' . $thana_id : 1);
        return $wpdb->get_results($sql, OBJECT);
    }
}
if (!function_exists('getUnion')) {
    function getUnion($id)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'bd_unions';
        return $wpdb->get_row("SELECT * FROM `{$table}` WHERE id={$id}", OBJECT);
    }
}
