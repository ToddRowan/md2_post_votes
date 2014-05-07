<?php
require ("../../../wp-load.php");

define ('VOTEPREFIX', 'vote-');
define ('VOTECOMMENTPREFIX', 'vote_comment-');

//error_log("--NEW PROCESS STARTING--", 3, "/var/www/html/portal/images_upload/vote.txt");

function startsWith($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}

function processVote($idstr,$val,&$votes)
{
    $retVal = false;
    if ($val==="1")
    {
        $idarr = explode('-', $idstr);
        $votes[]=$idarr[1];
    }
}

function processComment($idstr,$val,&$comments)
{
    $retVal = false;
    if (trim($val)!=="")
    {
        $idarr = explode('-', $idstr);
        $comments[$idarr[1]]=trim($val);
    }
}

function processGeneralComment($comment_text)
{
    
}

// If user not logged in, do nothing.
if (is_user_logged_in())
{
    $user_id = isset($_POST['user_id'])?$_POST['user_id']:-1;
    $date_range_id = isset($_POST['date_range_id'])?$_POST['date_range_id']:-1;
    
    if ($date_range_id != -1 && $user_id != -1)
    {
        // keep going if we have a valid user id and date range.
        $votesToSet = array();
        $commentsToSet = array();
        foreach ($_POST as $k => $v)
        {            
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
                md2_create_vote_suggestion($user_id, $date_range_id, $v);
            }
        }
        
        md2_delete_votes_by_user_and_date_range($user_id, $date_range_id);
    
        foreach ($votesToSet as $vote)
        {
            md2_set_vote($vote, $user_id, $date_range_id);
        }
        
        md2_delete_vote_comments_by_user_and_daterange($user_id, $date_range_id);
        foreach ($commentsToSet as $post_id => $comment_text)
        {
            md2_create_vote_comment($user_id, $post_id, $date_range_id, $comment_text);
        }
        
    }   
}
else
{
    error_log("User not logged in?", 3, "/var/www/html/portal/images_upload/vote.txt");
}

header("Location:" . "http://www.md2.com/portal/?page_id=2575");
die();