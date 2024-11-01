<?php
/**
 * Created by compemperor.
 * Date: 2016-07-25
 * Time: 05:41
 */

function socshw_clear_cron_jobs(){

    wp_clear_scheduled_hook('socshw_userdef_facebook_pull_hook');

}