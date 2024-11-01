<?php
/**
 * Created by compemperor.
 * Date: 2016-07-25
 * Time: 21:08
 */

function socshw_admin_menus(){

    add_menu_page(
        'Social Shadow options',
        'SS options',
        'edit_theme_options',
        'socshw_plugin_opts',
        'socshw_plugin_opts_page'


    );

}


function socshw_plugin_opts_page(){

    $plugin_options = get_option('socshw_opts');
    $error_opt = get_option('error_msg');
?>

<div class="wrap">

        <div class="mdl-card mdl-cell mdl-cell--7-col">
            <div class="mdl-card__title">
                <h3 class="mdl-color-text--blue-grey-300"><?php _e('Social shadow settings','FG')?></h3>
</div>

<?php
if(isset($_GET['status'])){

    if($_GET['status'] == 1 && empty($error_opt)) {
        ?>

        <div class="mdl-cell mdl-cell--8-col mdl-color--green-100 mdl-color-text--green-400"><h4 class="mdl-cell">Success!</h4></div>

        <?php
    }

}

if(isset($error_opt) && !empty($error_opt)) {
    ?>

    <div class="mdl-cell mdl-cell--8-col mdl-color--red-100 mdl-color-text--red-400"><h4 class="mdl-cell mdl-cell--10-col"><?php echo isset($error_opt ) ? $error_opt  : '' ;?> </h4></div>

    <?php
}

?>

    <div class="mdl-cell mdl-cell--12-col">
<form method="post" action="admin-post.php" class="form-table" >

    <input type="hidden" name="action" value="socshw_save_options"> <?php //this field is used to identify this form ?>

    <?php wp_nonce_field('socshw_options_verify'); //generate a nonce field?>


    <div class="mdl-card mdl-cell--10-col">

        <h4><?php _e('Facebook app config','FG')?></h4>

        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label mdl-cell mdl-cell--8-col">
            <input class="mdl-textfield__input" type="text" name="socshw_inputappid" value="<?php echo $plugin_options['app_token_id'] ? $plugin_options['app_token_id'] : ''; ?>">
            <label class="mdl-textfield__label" for="socshw_inputappid"><?php _e('App id','FG')?></label>
        </div>

        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label mdl-cell mdl-cell--8-col">
            <input class="mdl-textfield__input" type="text" name="socshw_inputappsecret" value="<?php echo $plugin_options['app_token_secret'] ? $plugin_options['app_token_secret'] : '' ; ?>">
            <label class="mdl-textfield__label" for="socshw_inputappsecret"><?php _e('App secret','FG')?></label>
        </div>

        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label mdl-cell mdl-cell--8-col">
            <input class="mdl-textfield__input" type="text" name="socshw_inputpgid" value="<?php echo $plugin_options['page_id'] ?>">
            <label class="mdl-textfield__label" for="socshw_inputpgid"><?php _e('Page id','FG')?></label>
        </div>


        <div class="mdl-cell mdl-cell--10-col">
            <h4><?php _e('Thumbnails','FG')?></h4>
            <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="socshw_inputthumbnail">
                <input type="checkbox" id="socshw_inputthumbnail" name="socshw_inputthumbnail" class="mdl-checkbox__input" <?php echo $plugin_options['set_thumbnail'] == 'on' ? 'checked':''?>>
                <span class="mdl-checkbox__label"><?php _e('Let the plugin set the posts thumbnails?(requires a theme that supports thumbnails)','FG')?></span>
            </label>

        </div>

        <div class="mdl-cell mdl-cell--10-col">
            <h4><?php _e('Content','FG')?></h4>
            <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="socshw_inputtvideocon">
                <input type="checkbox" id="socshw_inputtvideocon" name="socshw_inputtvideocon" class="mdl-checkbox__input" <?php echo $plugin_options['get_video_content'] == 'on' ? 'checked':''?>>
                <span class="mdl-checkbox__label"><?php _e('Get post video content, if it exists?','FG')?></span>
            </label>

            <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="socshw_inputembedlink">
                <input type="checkbox" id="socshw_inputembedlink" name="socshw_inputembedlink" class="mdl-checkbox__input" <?php echo $plugin_options['get_embed_link'] == 'on' ? 'checked':''?>>
                <span class="mdl-checkbox__label"><?php _e('Get post embedded link, if it exists?','FG')?></span>
            </label>

        </div>

        <br/>

        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label mdl-cell mdl-cell--8-col">
            <input class="mdl-textfield__input" type="text" name="socshw_inputuintervall" value="<?php echo $plugin_options['update_interval'] / 60 ?>">
            <label class="mdl-textfield__label" for="socshw_inputuintervall"><?php _e('Update interval in min.(min 30 min)','FG')?></label>
        </div>

        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label mdl-cell mdl-cell--8-col">
            <input class="mdl-textfield__input" type="text" name="socshw_inputnposts" value="<?php echo $plugin_options['number_of_posts']?>">
            <label class="mdl-textfield__label" for="socshw_inputnposts"><?php _e('The number of posts to fetch (default the latest post) max 35','FG')?></label>
        </div>

        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label mdl-cell mdl-cell--8-col">
            <p class="mdl-color-text--orange-A700"><?php _e('The plugin must be restarted for the interval change to take effect','FG')?></p>
            <p class="mdl-color-text--red-A700"><?php _e('Don\'t abort the saving process, if you do. The fetched data will be corrupted!' ,'FG')?></p>
        </div>

        </div>


    <div class="mdl-shadow--2dp"> <hr/></div>
    <div class="mdl-cell">

        <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-color--blue-300" type="submit"><?php _e('Update settings','FG')?></button>
    </div>

  <div><br/><a href="<?php echo plugins_url('/LICENSE.txt',socshw_socshwdir);?>">Material design license</a> </div>

    </form>

    </div>
  <?php
}

?>