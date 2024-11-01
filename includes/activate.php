<?php
/**
 * Created by compemperor.
 * Date: 2016-07-25
 * Time: 05:39
 */

function socshw_start_cron_job(){

    wp_schedule_event(time(),'minute','socshw_userdef_facebook_pull_hook');
    
    socshw_create_options_db();


}


function socshw_create_options_db(){
    
    //get the theme options upon activation
    $theme_opts  = get_option('socshw_opts');

    //if the theme options does not exist in the database create new ones
    if(!$theme_opts){

        $opts  = array(

            'app_token_id'    => '',
            'app_token_secret'    => '',
            'page_id'     => '',
            'set_thumbnail'     => '',
            'socshw_get_video_content' =>'on',
            'get_embed_link' => 'on',
            'update_interval'    => 1800,
            'number_of_posts'   => 1,
        );

        //Finally add the theme options
        add_option('socshw_opts',$opts);

    }

    $error_opt = get_option('error_msg');

    if(!$error_opt){
        $error_opt = '';
        add_option('error_msg',$error_opt);
    }

}