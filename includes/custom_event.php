<?php
/**
 * Created by compemperor.
 * Date: 2016-07-25
 * Time: 06:33
 */

//custom update interval
function socshw_addCronMinutes($array) {
    $plugin_opts = get_option('socshw_opts');
    $interval = isset($plugin_opts['update_interval']) ? $plugin_opts['update_interval']: 1800;
    $array['minute'] = array(
        'interval' => $interval,
        'display' => 'Custom Minutes',
    );
    return $array;
}