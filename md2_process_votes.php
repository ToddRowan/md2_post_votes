<?php
require ("../../../wp-load.php");

define ('VOTEPREFIX', 'vote-');
define ('VOTECOMMENTPREFIX', 'vote_comment-');
define ('DATERANGEVOTEPREFIX', 'votefordaterange');

error_log("\n--NEW PROCESS STARTING--\n", 3, "/var/www/html/portal/images_upload/vote.txt");

function startsWith($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}

function processVote($idstr,$val,&$votes)
{
    error_log("--processVote: looking at $val for $idstr\n", 3, "/var/www/html/portal/images_upload/vote.txt");
    if ($val==="1")
    {
        $idarr = explode('-', $idstr);
        $votes[]=$idarr[1];
        error_log("Added vote for id {$idarr[1]} to array\n", 3, "/var/www/html/portal/images_upload/vote.txt");
    }
}

function processDateRangeVote($date_range_id,$user_id)
{
  error_log("--processDateRangeVote: looking for date range vote\n", 3, "/var/www/html/portal/images_upload/vote.txt");
  if (isset($_POST[DATERANGEVOTEPREFIX]) && $_POST[DATERANGEVOTEPREFIX]==="drvote")
  {
    error_log("set user vote for date range\n", 3, "/var/www/html/portal/images_upload/vote.txt");
    md2_add_date_range_vote($date_range_id, $user_id);
  }
  else 
  {
    error_log("no user vote for date range\n", 3, "/var/www/html/portal/images_upload/vote.txt");
    md2_delete_date_range_vote($date_range_id, $user_id);
  }
}

function processComment($idstr,$val,&$comments)
{
    error_log("--processComment: looking at $val for $idstr\n", 3, "/var/www/html/portal/images_upload/vote.txt");
    if (trim($val)!=="")
    {
        $idarr = explode('-', $idstr);
        $comments[$idarr[1]]=trim($val);
        error_log("Added comment for id {$idarr[1]} to array\n", 3, "/var/www/html/portal/images_upload/vote.txt");
    }
}

function processGeneralComment($comment_text)
{
    
}

// If user not logged in, do nothing.
if (is_user_logged_in())
{
    $user_id = isset($_POST['user_id'])?$_POST['user_id']:-1;
    error_log("User $user_id is voting\n", 3, "/var/www/html/portal/images_upload/vote.txt");
    $date_range_id = isset($_POST['date_range_id'])?$_POST['date_range_id']:-1;
    error_log("User is voting on date range $date_range_id\n", 3, "/var/www/html/portal/images_upload/vote.txt");
    
    if ($date_range_id != -1 && $user_id != -1)
    {
        // keep going if we have a valid user id and date range.
        $votesToSet = array();
        $commentsToSet = array();
        foreach ($_POST as $k => $v)
        {  
            error_log("Analyzing vote data $k -> $v\n", 3, "/var/www/html/portal/images_upload/vote.txt");
            if (startsWith($k, VOTEPREFIX))
            {
                processVote($k,$v,$votesToSet);
            }
            if (startsWith($k, VOTECOMMENTPREFIX))
            {
                processComment($k,$v,$commentsToSet);
            }
            if ($k == 'general_comment')
            {
              error_log("Looking at general comment\n", 3, "/var/www/html/portal/images_upload/vote.txt");
              if (empty($v))
              {
                error_log("Empty general comment. Deleting.\n", 3, "/var/www/html/portal/images_upload/vote.txt");
                md2_delete_vote_suggestion($user_id, $date_range_id);
              }
              else
              {
                error_log("General comment. Saving.\n", 3, "/var/www/html/portal/images_upload/vote.txt");
                md2_create_vote_suggestion($user_id, $date_range_id, $v);
              }
            }
        }
        error_log("Deleting all votes for user {$user_id} and dr {$date_range_id}.\n", 3, "/var/www/html/portal/images_upload/vote.txt");
        md2_delete_votes_by_user_and_date_range($user_id, $date_range_id);
    
        foreach ($votesToSet as $vote)
        {
          error_log("Setting vote {$vote} for user {$user_id} and dr {$date_range_id}.\n", 3, "/var/www/html/portal/images_upload/vote.txt");  
          md2_set_vote($vote, $user_id, $date_range_id);
        }
        error_log("Deleting all comments for user {$user_id} and dr {$date_range_id}.\n", 3, "/var/www/html/portal/images_upload/vote.txt");
        md2_delete_vote_comments_by_user_and_daterange($user_id, $date_range_id);
        foreach ($commentsToSet as $post_id => $comment_text)
        {
            error_log("Setting comment {$comment_text} for user {$user_id} and dr {$date_range_id} and post {$post_id}.\n", 3, "/var/www/html/portal/images_upload/vote.txt");  
            md2_create_vote_comment($user_id, $post_id, $date_range_id, $comment_text);
        }
        
        // See if they just voted for the date range.
        processDateRangeVote($date_range_id,$user_id);
    }
    else 
    {
      error_log("date_range_id ({$date_range_id} or user_id ({$user_id}) invalid. Skipping processing.\n", 3, "/var/www/html/portal/images_upload/vote.txt");  
    }
}
else
{
    error_log("User not logged in?\n", 3, "/var/www/html/portal/images_upload/vote.txt");
}

error_log("Finished. Exiting and redirecting.\n", 3, "/var/www/html/portal/images_upload/vote.txt");  

header("Cache-Control: no-cache, must-revalidate, max-age=0", true, 302); // HTTP/1.1
header("Expires:Wed, 11 Jan 1984 05:00:00 GMT", true, 302);
header("Location:" . "http://www.md2.com/portal/?page_id=2575", true, 302);
die();