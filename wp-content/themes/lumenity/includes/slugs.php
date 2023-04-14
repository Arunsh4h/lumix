<?php
$recent_questions_slug = get_post_meta($the_page_id,"recent_questions_slug",true);
$most_answers_slug     = get_post_meta($the_page_id,"most_answers_slug",true);
$question_bump_slug    = get_post_meta($the_page_id,"question_bump_slug",true);
$answers_slug          = get_post_meta($the_page_id,"answers_slug",true);
$most_visit_slug       = get_post_meta($the_page_id,"most_visit_slug",true);
$most_vote_slug        = get_post_meta($the_page_id,"most_vote_slug",true);
$no_answers_slug       = get_post_meta($the_page_id,"no_answers_slug",true);
$recent_posts_slug     = get_post_meta($the_page_id,"recent_posts_slug",true);
$recent_questions_slug = ($recent_questions_slug != ""?$recent_questions_slug:"recent-questions");
$most_answers_slug     = ($most_answers_slug != ""?$most_answers_slug:"most-answers");
$question_bump_slug    = ($question_bump_slug != ""?$question_bump_slug:"question-bump");
$answers_slug          = ($answers_slug != ""?$answers_slug:"answers");
$most_visit_slug       = ($most_visit_slug != ""?$most_visit_slug:"most-visit");
$most_vote_slug        = ($most_vote_slug != ""?$most_vote_slug:"most-vote");
$no_answers_slug       = ($no_answers_slug != ""?$no_answers_slug:"no-answers");
$recent_posts_slug     = ($recent_posts_slug != ""?$recent_posts_slug:"recent-posts");
?>