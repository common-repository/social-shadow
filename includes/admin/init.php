<?php
/**
 * Created by compemperor.
 * Date: 2016-07-25
 * Time: 21:07
 */

function socshw_admin_init(){

    add_action('admin_enqueue_scripts','socshw_admin_enqueue');
    add_action('admin_post_socshw_save_options','socshw_save_options');

}


function socshw_admin_enqueue(){

    if(!isset($_GET['page']) || $_GET['page'] != "socshw_plugin_opts"){
        return;
    }

    if(socshw_Plugin_Development){


        wp_register_style('material_dev',plugins_url('css/material.css',socshw_socshwdir));

        wp_enqueue_style('material_dev');



        wp_register_script('material_dev',plugins_url('/js/material.js',socshw_socshwdir));

        wp_enqueue_script('material_dev',false,array(),false,true);


    }else{


        wp_register_style('material_prod',plugins_url('css/material.min.css',socshw_socshwdir));

        wp_enqueue_style('material_prod');

        wp_register_script('material_prod',plugins_url('/js/material.min.js',socshw_socshwdir));

        wp_enqueue_script('material_prod',false,array(),false,true);


    }
}

//function used to save the theme options
function socshw_save_options(){


    //Check the user priv first.
    if(!current_user_can('edit_theme_options')){

        wp_die(__('You are not allowed to be on this page'));
    }



    check_admin_referer('socshw_options_verify'); //Check the hidden filed data ,named su_options_verify declared in options-page.php
    $error_opts = get_option('error_msg');
    $opts = get_option('socshw_opts');
    $opts['app_token_id'] =  sanitize_text_field($_POST['socshw_inputappid']);
    $opts['app_token_secret'] =  sanitize_text_field($_POST['socshw_inputappsecret']);
    $opts['page_id'] =  sanitize_text_field($_POST['socshw_inputpgid']);
    $opts['set_thumbnail'] =  isset($_POST['socshw_inputthumbnail']) ? sanitize_text_field($_POST['socshw_inputthumbnail']):'';
    $opts['get_video_content'] =  isset($_POST['socshw_inputtvideocon']) ? sanitize_text_field($_POST['socshw_inputtvideocon']):'';
    $opts['get_embed_link'] =  isset($_POST['socshw_inputembedlink']) ? sanitize_text_field($_POST['socshw_inputembedlink']):'';
    $opts['update_interval'] =  absint($_POST['socshw_inputuintervall'] * 60 ) >= 1800 ? absint($_POST['socshw_inputuintervall'] * 60): 1800; //min 30 min update intervall
    $opts['number_of_posts'] =  absint($_POST['socshw_inputnposts']) >= 1 ? absint($_POST['socshw_inputnposts']): 1; //min 30 min update intervall


    update_option('socshw_opts',$opts);

    //run the crawler once options are updated.
    socshw_get_facebook_posts();

    if(isset($error_opts) && !empty($error_opts)) {

        wp_redirect(admin_url('admin.php?page=socshw_plugin_opts&status=0'));

    }else{

        wp_redirect(admin_url('admin.php?page=socshw_plugin_opts&status=1'));
    }



}