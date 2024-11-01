<?php
/**
 * Created by compemperor.
 * Date: 2016-08-02
 * Time: 03:16
 */


function socshw_load_front_data(){

    wp_register_style('socshw_styles',plugins_url('css/socshw_styles.css',socshw_socshwdir));

    wp_enqueue_style('socshw_styles');


}