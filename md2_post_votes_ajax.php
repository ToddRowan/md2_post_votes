<?php

add_action('wp_ajax_md2_set_vote', 'md2_set_vote_ajax');
add_action('wp_ajax_md2_edit_date_range', 'md2_edit_date_range_ajax');

function md2_set_vote_ajax()
{
    
}

function md2_edit_date_range_ajax()
{
    $id = $_POST['id'];
    $new_start = $_POST['new_start'];
    $new_end = $_POST['new_end'];
    
    if ($id==-1)
    {
        $id = md2_create_vote_date_range($new_start, $new_end);
    }
    else
    {
        md2_update_vote_date_range($id, array("start_date"=>$new_start, "end_date"=>$new_end));
    }
    
    $data = array();
    
    $data['id']=$id;
    $data['activatable'] = md2_is_date_range_activatable($id);
    $data['post_count'] = md2_get_total_count_of_posts_by_date_range($id);
    
    md2_output_ajax_json($data);
}

function md2_output_ajax_json($data)
{
    $output=json_encode($data);
    if(is_array($output))
    {
        print_r($output);  
    }
    else
    {
        echo $output;
    }
    die;
}