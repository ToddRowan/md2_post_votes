<?php

add_action('wp_ajax_md2_set_vote', 'md2_set_vote_ajax');
add_action('wp_ajax_md2_edit_date_range', 'md2_edit_date_range_ajax');
add_action('wp_ajax_md2_activate_date_range', 'md2_activate_date_range_ajax');

function md2_set_vote_ajax()
{
    
}

function md2_edit_date_range_ajax()
{
    $id = $_POST['id'];
    $new_start = $_POST['new_start'];
    $new_end = $_POST['new_end'];
    $data = array();
    
    if ($id==-1)
    {
        $id = md2_create_vote_date_range($new_start, $new_end);
        $data['process_state']=MD2_STATE_NOT_USED;
    }
    else
    {
        md2_update_vote_date_range($id, array("start_date"=>$new_start, "end_date"=>$new_end));
    }
    
    $data['id']=$id;
    $data['activatable'] = md2_is_date_range_activatable($id);
    $data['post_count'] = md2_get_total_count_of_posts_by_date_range($id);
    
    md2_output_ajax_json($data);
}

function md2_activate_date_range_ajax()
{
  // Get the id
  $id = $_POST['id'];
  $meet_detail_fields = array('date_of_meet','time_meet_start',
            'time_meet_end','phone_number','meeting_id','meeting_note');
  $meet_mail_fields = array('date_meet_email_sent');
  $vote_mail_fields = array('date_vote_email_sent', 'date_voting_ended');
  $active_dr = md2_get_vote_date_range_by_id($id);
  $current_state = intval($active_dr->process_state);
  
  $set_fields = array();
  // TODO: make sure we have minimum data
  // Are we activating or updating?
  if ($_POST['act']=='activate')
  {
    $fields = array_merge($meet_detail_fields, $meet_mail_fields, $vote_mail_fields);
    // If we are activating:  
    foreach ($fields as $f)
    {
      if (isset($_POST[$f]) && strlen(trim($_POST[$f]))>0)
      {
        $set_fields[$f]=$_POST[$f];
      }
    }
    
    $set_fields['process_state'] = MD2_STATE_ACTIVATED;
    $current_state = MD2_STATE_ACTIVATED;
  }
  else
  {
    // If we are updating
    $fields = array();
  
    if ($current_state>=MD2_STATE_MEET_MAIL_SENT && $current_state<=MD2_STATE_ARCHIVED) 
    {
        // update meeting info and special instructions
        $fields = array_merge($meet_detail_fields);
    }
    else if ($current_state>=MD2_STATE_VOTE_MAIL_SENT && $current_state<=MD2_STATE_VOTE_COMPLETED)
    {
      // update meeting mail and meeting details
      $fields = array_merge($meet_detail_fields, $meet_mail_fields);
    } 
    else if ($current_state==MD2_STATE_ACTIVATED)
    {
      // update everything
      $fields = array_merge($meet_detail_fields,$meet_mail_fields,$vote_mail_fields);
    }
    
    foreach ($fields as $f)
    {
      if (isset($_POST[$f]) && strlen(trim($_POST[$f]))>0)
      {
        $set_fields[$f]=$_POST[$f];
      }
    }    
  }
  
  $resp = array();
  
  if (count($set_fields)>0)
    $res = md2_update_vote_date_range($id, $set_fields);
  
  if ( is_wp_error( $res ) ) 
  {
    $resp['error']=1;
    $resp['msg'] = $res->get_error_message();
  }
  else 
  {
    $obj = md2_get_vote_date_range_by_id($id);
    
    if ($_POST['act']=='activate')
    {
      $dtz = md2_get_default_tz();
      $tmp_date = date_create($obj->date_vote_email_sent,$dtz);
      $tmp_date->modify("+6 hours");  
      md2_add_single_cron_datetime($id, $tmp_date);
    }
    
    $date_fields = array('start_date','end_date','date_of_meet','date_meet_email_sent',
                   'date_vote_email_sent','date_voting_ended');
    
    foreach ($obj as $k=>$v)
    {
      if (in_array($k, $date_fields))
      {
        $obj->$k = md2_format_view_date($v);
      }
    }
    $resp['error']=0;
    $resp['msg'] = "";
    $resp['new_state'] = $current_state;
    $resp['obj']= $obj;
  }
  
  // Set all necessary cron jobs.
  
  // Return any messages about what went wrong?
  md2_output_ajax_json($resp);
}

function md2_activate_date_range($id, $fields)
{
  md2_update_vote_date_range($id, $fields);
}

function md2_update_date_range_info()
{
  
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