<?php
/**
 * Created by compemperor.
 * Date: 2016-07-24
 * Time: 22:58
 */

function socshw_get_facebook_posts(){
    $plugin_options = get_option('socshw_opts');
    $error_opt = get_option('error_msg');

//App accesstoken
$accessToken = $plugin_options['app_token_id'] . '|' . $plugin_options['app_token_secret'];

//The ID of the Facebook page
$id = $plugin_options['page_id'];

//the number of posts to fetch
$num_of_posts = $plugin_options['number_of_posts'];

//Construct the url
$url = "https://graph.facebook.com/{$id}/posts?limit=35&access_token={$accessToken}";

//Make the API call
$result = @file_get_contents($url);

    if(!$result){
        $error_opt = __('Error app-id, app-secret or page id is not valid','FG');
        update_option('error_msg',$error_opt);
        return;

    }else{
        $error_opt = '';
        update_option('error_msg',$error_opt);
    }


$result = utf8_encode($result);

//Decode the JSON result.
$json_data = json_decode($result);


if($num_of_posts > count($json_data->data)){
    $error_opt = __('Error the facebook page does not contain that much posts: ','FG').$num_of_posts;
    update_option('error_msg',$error_opt);
    return;

}else{
    $error_opt = '';
    update_option('error_msg',$error_opt);

}

    //get posts
    for($i = 0; $i < $num_of_posts ;$i++){


    //post id
    $post_id = $json_data->data[$i]->id;


    //Title
    $title = isset($json_data->data[$i]->message) ? explode(' ',$json_data->data[$i]->message) : '';

        if($title != ''){
            $title = array_slice($title,0,5);
            $new_title = implode(' ',$title) . '...';
        }else{

            $new_title = isset($json_data->data[$i]->name) ? $json_data->data[$i]->name : '';  // post has no content = no title = get name
        }



    //content
   $content = isset($json_data->data[$i]->message) ? $json_data->data[$i]->message : '';
        $post_excerpt = $content != '' ? socshw_getExcerpt($content,0,(strlen($content)*0.5)) : '';

        //Find urls in content and extract them, add a tag to them, then put the urls back
        $content = $content != '' ? socshw_replace_urls_in_content($content) : '';


    // Creation date
    $m = new DateTime($json_data->data[$i]->created_time);
    $date =  $m->format('Y-m-d');


        //get the post image and save it locally.
        $image_id = isset($json_data->data[$i]->object_id) ? $json_data->data[$i]->object_id: '';
        $img_local_url = $image_id != '' ? '<img class="aligncenter center-block" src="'.socshw_get_post_img($image_id).'"/> <br> <br>' : '';


        $video_content = socshw_get_video_content($json_data,$i);
        //Glue the content together
        if($video_content != ''){

            if(isset($plugin_options['get_video_content']) && $plugin_options['get_video_content'] == 'on') {
                //If there is video content don't return the content image
                $content = $video_content . socshw_get_embed_link_data($json_data, $i) . $content;
            }else{

                $content = socshw_get_embed_link_data($json_data, $i) . $content;
            }

        }else{
            //there are no video content. Prob. big image, return it.
            $content =  $img_local_url .socshw_get_embed_link_data($json_data,$i) . $content ;
        }



        //create e new post with the glued content.
        $post_id = socshw_create_new_post($post_id,$new_title,$content,$post_excerpt,$date);


        //set the thumbnail
        if($img_local_url != ''){
            socshw_generate_post_thumbnail($img_local_url,$post_id,$image_id);

        }



    }

}


function socshw_replace_urls_in_content($content){

    $positions = socshw_strpos_all($content,'http');
    //$url_pos = strpos($content, 'http');
    if(count($positions) > 0) {

        foreach ($positions as $url_pos) {

            $url = substr($content, $url_pos);
            $url = str_replace($url, $url . ' ', $url);
            $space_after_url = strpos($url, ' ');
            $url = substr($url, 0, $space_after_url);

            $url_template = '<a href="' . $url . '">' . $url . '</a>';

            $content = str_replace($url, $url_template, $content);



        }
    }

    return $content;


}

function socshw_strpos_all($content, $htt_p)
{
    $offset = 0;
    $allpos = array();
    while (($pos = strpos($content, $htt_p, $offset)) !== FALSE) {
        $offset = $pos + 1;
        $allpos[] = $pos;
    }
    return $allpos;
}



function socshw_get_post_img($object_id){

    $post_photo ="http://graph.facebook.com/{$object_id}/picture";


    //get the url contents
    $content = file_get_contents($post_photo);

    //Store temporary.
    $temp_filename =  fopen(wp_upload_dir()['path']."/".$object_id.'.tmp','w');
    fwrite($temp_filename,'sds');
    fclose($temp_filename);
    $temp_filename = wp_upload_dir()['path']."/".$object_id.'.tmp';

    $new_name = '';

    $temp_fh = fopen($temp_filename,'w');
    fputs($temp_fh,$content);
    fclose($temp_fh);

    //Check the mime type and change the temp file ext.
    switch (mime_content_type($temp_filename)){

        case 'image/png':
            $new_name = str_replace('tmp','png',$temp_filename);
            copy($temp_filename,$new_name);
            unlink($temp_filename);
            $new_name = wp_upload_dir()['url']."/".$object_id.'.png';
            break;


        case 'image/jpeg':
            $new_name = str_replace('tmp','jpeg',$temp_filename);
            copy($temp_filename,$new_name);
            unlink($temp_filename);
            $new_name = wp_upload_dir()['url']."/".$object_id.'.jpeg';
            break;

        case 'image/gif':
            $new_name = str_replace('tmp','gif',$temp_filename);
            copy($temp_filename,$new_name);
            unlink($temp_filename);
            $new_name = wp_upload_dir()['url']."/".$object_id.'.gif';
            break;

        case 'image/bmp':
            $new_name = str_replace('tmp','bmp',$temp_filename);
            copy($temp_filename,$new_name);
            unlink($temp_filename);
            $new_name = wp_upload_dir()['url']."/".$object_id.'.bmp';
            break;

    }


    //return the temp dir.
    return $new_name;


}


function socshw_generate_post_thumbnail($image_lib_url,$post_id,$img_id){

    global $wpdb;
    $plugin_options = get_option('socshw_opts');

    //check if  the posts already exist in db
    $query =<<<EOD

                SELECT COUNT(*) FROM `{$wpdb->posts}` 
                WHERE guid='http://{$img_id}';
EOD;
    $post_count = $wpdb->get_var($query);

    if($post_count <= 0) {
        $wp_filetype = wp_check_filetype($image_lib_url, null);
        $attachment = array(
            'guid' => $img_id,
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name(substr($image_lib_url, strrpos($image_lib_url, '/') + 1)),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment($attachment, $image_lib_url, $post_id);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $image_lib_url);
        $res1 = wp_update_attachment_metadata($attach_id, $attach_data);

        if(isset($plugin_options['set_thumbnail']) && $plugin_options['set_thumbnail'] == 'on'){
            $res2 = set_post_thumbnail($post_id, $attach_id);
        }


    }else{

        $get_query=<<<EOD
               SELECT * FROM `{$wpdb->posts}` 
                WHERE guid='http://{$img_id}';
EOD;
        $thumb = $wpdb->get_results($get_query,ARRAY_A);

        if(isset($thumb)){

            if(isset($plugin_options['set_thumbnail']) && $plugin_options['set_thumbnail'] == 'on') {
                set_post_thumbnail($post_id, $thumb[0]['ID']);
            }
        }
    }

}



function socshw_create_new_post($post_id,$post_title,$post_content,$post_excerpt,$post_date){

    global $wpdb;

    //check if  the posts already exist in db
    $query =<<<EOD

                SELECT COUNT(*) FROM `{$wpdb->posts}` 
                WHERE guid='http://{$post_id}';
EOD;

 $post_count = $wpdb->get_var($query);

  $new_id = '';

    if($post_count <= 0){
        $new_id = wp_insert_post(array(
            'guid' => $post_id,
            'post_title' => $post_title,
            'post_content' => $post_content,
            'post_excerpt'  => $post_excerpt,
            'post_date' =>  $post_date,
            'post_status'   => 'publish'

        ));
    }else{

        $get_query =<<<EOD

                SELECT * FROM `{$wpdb->posts}` 
                WHERE guid='http://{$post_id}';
EOD;


        $results = $wpdb->get_results($get_query,ARRAY_A);
        $new_id = $results[0]['ID'];
    }



 return $new_id;

}


function socshw_getExcerpt($str, $startPos=0, $maxLength=100) {
    if(strlen($str) > $maxLength) {
        $excerpt   = substr($str, $startPos, $maxLength-3);
        $lastSpace = strrpos($excerpt, ' ');
        $excerpt   = substr($excerpt, 0, $lastSpace);
        $excerpt  .= '...';
    } else {
        $excerpt = $str;
    }

    return $excerpt;
}


function socshw_get_video_content($json_data,$i){

        $content_embed_link = isset($json_data->data[$i]->link) ? $json_data->data[$i]->link : '';
        $content_embed_source = isset($json_data->data[$i]->source) ? $json_data->data[$i]->source : '';
        $content_type =  isset($json_data->data[$i]->type) ? $json_data->data[$i]->type : '';

        $video_content_template = <<<EOT
        
        <video class="aligncenter center-block vid_bed" width="520" height="240" controls>
        <source src="{$content_embed_source}" type="video/mp4">
        <a href="{$content_embed_link}">video url</a>
        </video>   
EOT;

        if ($content_embed_link != '' && $content_embed_source != '' && $content_type == 'video') {

            if (strpos($content_embed_source, '.mp4') != false) {
                return $video_content_template;
            } else {

                return '';
            }

        }

}

function socshw_get_embed_link_data($json_data,$i){
    $plugin_options = get_option('socshw_opts');

    if(isset($plugin_options['get_embed_link']) && $plugin_options['get_embed_link'] == 'on') {
        $content_embed_link = isset($json_data->data[$i]->link) ? $json_data->data[$i]->link : '';
        $content_embed_name = isset($json_data->data[$i]->name) ? $json_data->data[$i]->name : '';
        $content_embed_desc = isset($json_data->data[$i]->description) ? $json_data->data[$i]->description : '';
        $content_embed_picture = isset($json_data->data[$i]->picture) ? $json_data->data[$i]->picture : '';
        $content_embed_picture_backup = isset($json_data->data[$i]->picture) ? $json_data->data[$i]->picture : '';
        $content_embed_source = isset($json_data->data[$i]->source) ? $json_data->data[$i]->source : '';
        $content_type =  isset($json_data->data[$i]->type) ? $json_data->data[$i]->type : 'link';

        if ($content_embed_picture != '') {
            $img_src = parse_url($content_embed_picture);
            $pos = strpos($img_src['query'], 'url=');
            $img_src_url = substr($img_src['query'], $pos + 4);
            $img_src_url = urldecode($img_src_url);
            $pos_and = strpos($img_src_url, '&');
            $content_embed_picture = substr($img_src_url, 0, $pos_and);

        }


        //Check if big image url is an image of not just return the usual small image.
        $content_embed_picture = '<img class= "aligncenter" src="' .socshw_check_if_is_image($content_embed_picture,$content_embed_picture_backup) . '">';


        $embed_template = <<<EOT
    
    <div class="em_bed aligncenter">
       <a href="{$content_embed_link}">
       
    <div>
      {$content_embed_picture}
     <h4>{$content_embed_name}</h4> 
     </div> 
     
     <div> 
     <p>{$content_embed_desc}</p> 
     </div> 
    
    </a>
    </div> 
    <br/>

EOT;

        if ($content_type != 'photo' && strpos($content_embed_source, '.mp4') == false && $content_embed_link != '') {

            return $embed_template;
        }
    }

}

function socshw_check_if_is_image($embed_picture_url,$embed_picture_post){
    $url = $embed_picture_url;
    $file = @file_get_contents($url);

    if($file != false) {
        $tmpfile = tempnam("/tmp", "img");
        $handle = fopen($tmpfile, "w");
        fwrite($handle, $file);

        switch (mime_content_type($tmpfile)) {

            case 'image/png':
                return $embed_picture_url;
                break;


            case 'image/jpeg':
                return $embed_picture_url;
                break;

            case 'image/gif':
                return $embed_picture_url;
                break;

            case 'image/bmp':
                return $embed_picture_url;
                break;

            default:
                return $embed_picture_post;
                break;

        }
    }else{
		 return $embed_picture_post;

	     }

}
