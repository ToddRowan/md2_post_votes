<?php
header("Cache-Control: no-cache, must-revalidate, max-age=0"); // HTTP/1.1
header("Expires:Wed, 11 Jan 1984 05:00:00 GMT");
/*
Template Name: Page voting template
*/
?>
<?php get_header(); ?>
<?php $options = get_option('inove_options'); ?>

<script>
jQuery(document).ready(
	function()
	{
		jQuery('#votefordaterange').click(
			function(evt)
			{
				setDateRangeVote(evt.currentTarget);
			});
			
		jQuery(".votediv").click(
			function(evt)
			{
				handleVoteClick(evt);
			});
			
		jQuery(".vote_comment").dblclick(
			function(evt)
			{
				handleCommentClick(evt);
			});
			
		jQuery("#save_comment_edit").click(function(evt){handleCommentEdit(evt)});
		jQuery("#cancel_comment_edit").click(function(evt){handleCommentCancel(evt)});
		jQuery("#clear_comment_edit").click(function(evt){handleCommentDelete(evt)});
		setDateRangeVote(jQuery('#votefordaterange')[0]);
	}
);

function setDateRangeVote(el)
{
	if (jQuery(el).is(":checked"))
	{
		jQuery('.post').addClass('translucent');
		jQuery('#votefornone').css({'font-weight':'bold'});
	}
	else
	{
		jQuery('.post').removeClass('translucent');
		jQuery('#votefornone').css({'font-weight':'normal'});
	}
}

function handleCommentEdit(evt)
{
	var id = getCommentIdForAction(evt);
	// Get the text area value
	var text = jQuery('#comment_edit_textarea').val();
	
	// populate the input and the span
	if (text.trim()=='')
	{
		handleCommentDelete(evt);
		return;
	}
	
	jQuery('#vote_comment-' + id).val(encodeURI(text.trim()));
	jQuery('#comment-'+id).text(text.trim());
	
	// close the window
	jQuery('#comment_edit_box').hide();
}

function handleCommentCancel(evt)
{
	var id = getCommentIdForAction(evt);
	
	var $comment = jQuery(evt.currentTarget).closest(".vote_comment_box");
	
	// If we cancel when nothing exists, hide everything
	if ($comment.find('input').val()=='')
	{
		// hide the div
		$comment.hide();
	}	
	
	// close the window
	jQuery('#comment_edit_box').hide();
}

function handleCommentDelete(evt)
{
	var id = getCommentIdForAction(evt);
	var $comment = jQuery(evt.currentTarget).closest(".vote_comment_box");
	
	// populate the input and the span with nothing
	$comment.find('input').val('');
	$comment.find('.vote_comment_text').text('');
	// close the window
	jQuery('#comment_edit_box').hide();
	
	// hide the div
	$comment.hide();
}

function getCommentIdForAction(evt)
{
	var $el = jQuery(evt.currentTarget);
	var id = $el.closest(".vote_comment_box").attr("id");
	return id.split('-')[1];
}

function showCommentEditor(id)
{
	// locate the div who will be the editor parent
	var $parent = jQuery('#vote_comment_box-' + id);
	var $editor = jQuery('#comment_edit_box');
	if (jQuery('#vote_comment_box-' + id + ' #comment_edit_box').length == 0)
	{
		// The editor is not where we want it to be.
		var $currentParent = $editor.closest('.vote_comment_box');
		
		// If we cancel when nothing exists, hide everything
		if ($currentParent.find('input').val()=='')
		{
			// hide the div
			$currentParent.hide();
		}
		
		$editor.appendTo($parent);
	}
	
	$editor.css('display', 'block');
	
	// Show the editor parent and grab the editable content.
	var	$inputcontent = $parent.show().find("#vote_comment-"+id).first();	
	jQuery("#comment_edit_textarea").val(decodeURI(cleanMultiline($inputcontent.val())));
}

function cleanMultiline(text)
{
	var regex = /(\s)+/g;
	regex.multiline = true;
	return text.replace(regex,' ');
}

function handleVoteClick(evt)
{
	var $el = jQuery(evt.currentTarget);
	var id = $el.attr("id");
	id = id.split('-')[1];
	
	if ($el.hasClass('notvoted'))
	{
		$el.removeClass('notvoted').addClass('voted');
		setVote(id);
		showCommentEditor(id);
	}
	else
	{
		$el.removeClass('voted').addClass('notvoted');
		setVote(id);
		jQuery('#vote_comment_box-'+id).hide();
	}
}

function handleCommentClick(evt)
{
	var id = getCommentIdForAction(evt);
	
	showCommentEditor(id);
}

function getVoteCommentInput(id)
{
}

function getVoteCommentBox(id)
{
}

function setVote(id)
{
	var idRoot = "vote-";	
	var $voteVal = jQuery("#"+idRoot+id);
	$voteVal.val($voteVal.val()=='1'?0:1);
}
</script>

<style>            
	.post {
		padding: 10px 0px;
		clear: both;
	}
	
	.masthead, .post_box, .latest_comment {
		float: left;
		padding-right: 5px;
	}
	
	.post_author, .votediv {
		display: inline-block;
	}
	
	.votediv {
		min-height:52px;
		min-width:52px;
		cursor: pointer;
		background-size:52px 52px;
		background-repeat:no-repeat;
	}
	
	.notvoted {
		background-image: url('images/not_selected.png');
	}
	
	.voted {
		background-image: url('images/selected.png');
	}
	
	.post h2.post_title_vote, .comment_head, .general_comment_head {
		margin: 0px auto 2px;
		padding-top: 0px;
		font-family: Verdana,"BitStream vera Sans";
		line-height: 95%;
		color: black;
		font-weight: bold;
		padding-left: 0px;
	}
	
	.post h2.post_title_vote, .general_comment_head {
		font-size: 12px;
	}
	
	.excerpt, .comment_excerpt {
		margin: 0px auto;
		overflow: hidden;
		height: 45px;
		position: relative;
		line-height: 1.25em;
	}
	
	.full_comment, .full_post {
		position: absolute;
		right: 0px;
		bottom: 0px;                
	}
	
	.full_post_text {
		background-color: white;
		font-weight: bold;
		cursor: pointer;
	}
	
	.fader {
		background-image:url('images/bgfade30.png');
		background-repeat:repeat;
		min-width: 30px;
		overflow:hidden;
		display: inline-block;
		position: relative;
		top: 3px;
	}
	
	.translucent {
		opacity: .25;
	}
	
	.post_box {
		width: 245px;
		overflow: hidden;
		padding-right: 15px;
	}
				
	.latest_comment {
		padding-right: 0px;
		width: 255px;
		overflow: hidden;
	}
	
	.comment_head {
		font-size: 12px;
		padding-bottom: 0px;
	}
	
	.author {
		font-weight: bold;
	}
	
	.comment_date {
		font-style: italic;
	}
	
	a.vote_link, a.vote_link:hover {
		color: black;
		text-decoration: none;
	}
	
	.post_hr {
		margin: 6px 0;
	}
	
	.vote_comment {
		position: relative;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
	}
	
	.no_comment {
		display: none;
	}
	
	.vote_comment_box {
		float: left;
		margin-top: 15px;
		position: relative;
		width: 100%;
	}
	
	.votewhy {
		font-weight: bold;
		cursor: pointer;
	}
	
	#comment_edit_box {
		display:none;
		position:absolute;
		background-color: #141519;
		top: 0;
		left: 0;
		right: 0;
		height: 90px;
		z-index: 5;
		color: white;
		font-weight: bold;
	}
	
	.comment_edit_header {
		line-height: 20px;
		padding-left: 2px;
	}
	
	#comment_edit_box textarea, #general_comment {
		min-height: 50px;
		width: 100%;
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
		padding: 0;
		margin: 0;
		box-sizing: border-box;
		resize: none;
		color: black;
		padding-left: 2px;
		font-weight: normal;
	}
	
	#comment_edit_controls {
		float: right;
	}
	
	.edit_button {
		cursor: pointer;
		padding: 0 5px;
	}
	
	.clearfix {
		clear: both;
	}
</style>
<h2 class="postTitle"><?php the_title(); ?></h2>
<?php 	
	$ranges = md2_get_voting_date_range();
	if (isset($_GET['force']))
	{
		$ranges = array(md2_get_vote_date_range_by_id($_GET['force']));
	}
	
	if (count($ranges)==0) 
	{
		// Voting is suspended. 
		?>
			<p>Sorry, voting is not active at this time. Keep an eye in your inbox for the next grand rounds voting period.</p>
		<?php
	}
	else
	{
		$cdr = $ranges[0];
		if (have_posts())
		{
			the_post();
			$cnt = get_the_content();
			$vote_end_date = date_create($cdr->date_voting_ended, md2_get_default_tz());
			$meet_date = date_create($cdr->date_of_meet, md2_get_default_tz());
			$ved_format = $vote_end_date->format("l, F jS");
			$md_format = $meet_date->format("l, F jS");
			
			echo "<div>" . str_replace(array("__VOTE_END__","__MEET_DATE__"), array($ved_format,$md_format), $cnt) . "</div>";
		}
		
		$current_date_range_id = $cdr->id; 
	
		$current_user_id = get_current_user_id();
		$current_votes = md2_get_votes_for_user($current_user_id, $current_date_range_id);
		
		$idsarray = md2_get_eligible_post_ids_by_date_range($current_date_range_id);
		$idslist = implode(",",$idsarray);

		$post_args = array(
			'posts_per_page'   => 99,
			'orderby'		   => 'post__in',
			'suppress_filters' => 0,
			'post__in'         => $idsarray ); 
		
		$posts_array = get_posts( $post_args );
		echo '<form action="' . md2_get_process_vote_action_url() . '" method="post">';
		echo '<input type="hidden" name="user_id" value="' . $current_user_id . '">';
		echo '<input type="hidden" name="date_range_id" value="' . $current_date_range_id . '">';
		echo '<input type="submit" name="Submit" value="Click to save your votes" style="margin: 15px 0 10px">';
		$checked = (md2_did_user_vote_for_date_range($current_date_range_id, $current_user_id)?"checked":"");
		echo '<p><label id="For"><input type="checkbox" name="votefordaterange" id="votefordaterange" value="drvote" ';
		echo $checked . ' style="vertical-align:bottom;position:relative;top:-2px;"/>';
		echo '&nbsp;<span id="votefornone">I\'m in but no special votes for me.</span></label></p>';
		foreach($posts_array as $p)
		{
			$post = $p;		
			setup_postdata( $post );
			$pid = get_the_ID();
			
			$comment_args = array(	
			'number' => 1,	
			'order' => 'DESC',
			'post_id' => $pid);
			
			$comments = get_comments($comment_args);
			$comment = (count($comments)>0 ? $comments[0]:null);	
		?>
			<div class="post">
				<div class="masthead">
					<?php $isvoted = in_array($pid,$current_votes);?>
					<div id="votebox-<?php echo $pid; ?>" class="votediv <?php echo ($isvoted?"voted":"notvoted"); ?>">
					<input type="hidden" id="vote-<?php echo $pid; ?>" name="vote-<?php echo $pid; ?>" value="<?php echo ($isvoted?"1":"0"); ?>">
					</div>
					<div class="post_author">
						<img src="images_upload/<?php echo get_usermeta(get_the_author_ID(), 'wp_md2_pic');?>" alt="By <?php the_author(); ?>"  title="By <?php the_author(); ?>"> 
					</div>
				</div>
				<div class="post_box">
					<h2 class="post_title_vote"><?php the_title();?></h2>
					<div class="excerpt">
						<p class="excerpt"><span class="comment_date author">(<?php 
							echo get_the_date('M j');
						?>):</span> <?php echo substr(strip_tags(get_the_content()),0,250);?></p>
						<div class="full_post"><div id="fdr" class="fader">&nbsp;</div><span class="full_post_text"><a class="vote_link" href="<?php the_permalink() ?>">Read the full article</a></span></div>
					</div>
				</div>
				<div class="latest_comment">
				<?php if (!is_null($comment)) : ?>
					<h3 class="comment_head">Latest comment</h3>
					<div class="excerpt">
						<p class="comment_excerpt">
						<span class="author"><?php echo $comment->comment_author;?> (<span class="comment_date"><?php 
							echo date('M j',strtotime($comment->comment_date));
						?></span>): </span><? echo substr(strip_tags($comment->comment_content),0,250);?>
						</p>
						<div class="full_post"><div id="fdr" class="fader">&nbsp;</div><span class="full_post_text"><a class="vote_link" href="<?php
						echo get_permalink()."#comments"; ?>">Read the full comment</a></span></div>
					</div>
					<?php else:?>
					<h3 class="comment_head">No comments</h3>
					<?php endif; ?>				
				</div>
				<?php 
					$vote_comment = md2_get_vote_comment($current_user_id, $pid, $current_date_range_id);
				?>
				<div class="vote_comment_box  <?php echo (is_null($vote_comment) || !$isvoted?"no_comment":"yes_comment")?>" id="vote_comment_box-<?php echo $pid;?>">
					<input name="vote_comment-<?php echo $pid;?>" id="vote_comment-<?php echo $pid;?>" type="hidden" value="<?php echo $vote_comment;?>">			
				  <div class="vote_comment" title="Double-click to edit">
					<span class="votewhy">Your vote comment: </span>
					<span id="comment-<?php echo $pid;?>" class="vote_comment_text"><?php echo urldecode($vote_comment);?></span>
				  </div>
				</div>
				<div class="clearfix"></div>
			</div>
			<hr class="post_hr">
		<?php
		}	
		?>
		<h2 class="general_comment_head">Any new topics you'd like to discuss? Any brain teasers? Add them here.<br><span style="font-size:11px">(350 characters or less)</span></h2>
		<textarea id="general_comment" name="general_comment" maxlength="350"><?php echo md2_get_vote_suggestion($current_user_id, $current_date_range_id);?></textarea>
		<input type="submit" name="Submit" value="Click to save your votes">
	</form>
	<div class="clearfix wtf"></div>
	<div id="comment_edit_box"> <!-- see if we can prevent resize and hide scrollbar area. If not, how to make input multiline-->
		<div class="comment_edit_header">Tell us why you're voting for this post (350 characters or less)</div>
		<textarea id="comment_edit_textarea" maxlength="350"></textarea>
		<div id="comment_edit_controls">
			<span id="save_comment_edit" class="edit_button">Save</span>
			<span id="cancel_comment_edit" class="edit_button">Cancel</span>
			<span id="clear_comment_edit" class="edit_button">Delete</span>
		</div>
	</div>

<?php
	} 
	get_footer();  ?>
