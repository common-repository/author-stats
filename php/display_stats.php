<?php 
$last_30_p = $this->get_post_stats($_GET['author'], 'publish', 30);
$last_7_p = $this->get_post_stats($_GET['author'], 'publish', 7);
$draft = $this->get_post_stats($_GET['author'], 'draft', 0);
$review = $this->get_post_stats($_GET['author'], 'review', 0);
$last_30_c = $this->get_comment_stats($_GET['author'], 30);
$last_7_c = $this->get_comment_stats($_GET['author'], 7);
$last_30_a = $this->get_comment_avg($_GET['author'], 30);
$last_30_w = $this->get_word_count_avg($_GET['author'], 30);
$last_30_i = $this->get_inch_count_avg($_GET['author'], 30);
?>

<div id="author_stats_display" style="margin: 10px 50px; padding-top: 10px;">

    <h3 style="padding-bottom: 5px;">
        Total published post(s) in last 30 days - <?php echo $last_30_p->c; ?>
    </h3>
    <h3 style="padding-bottom: 5px;">
        Total published post(s) in the last 7 days - <?php echo $last_7_p->c; ?>
    </h3>
    <h3 style="padding-bottom: 5px;">
        Total post(s) in draft status - <?php echo $draft->c; ?> 
    </h3>
    <h3 style="padding-bottom: 5px;">
        Total post(s) in pending review status - <?php echo $review->c; ?> 
    </h3>
    <br/>
    <h3 style="padding-bottom: 5px;">
        Average inch count per post in last 30 days - <?php print_r($last_30_i); ?>
    </h3>

    <h3 style="padding-bottom: 5px;">
        Average word count per story in last 30 days - <?php echo $last_30_w ?> 
    </h3>

    <h3 style="padding-bottom: 5px;">
        Average number of comments per story in last 30 days - <?php echo $last_30_a->c ?>
    </h3>
    <br/>
    <h3 style="padding-bottom: 5px;">
        Most commented story in last 30 days<br/>
        <p style="padding: 10px;">
        <?php if ($last_30_c) { ?>
            <a href="<?php echo get_permalink( $last_30_c->ID ); ?>">
            <?php echo $last_30_c->post_title; ?>
            </a> - (<?php echo $last_30_c->c; ?>)
        <?php } else { ?>
            No stories w/comments in last 30 days.
        <?php } ?>
        </p>
    </h3>
    
    <h3 style="padding-bottom: 5px;">
        Most commented story in last 7 days
        <p style="padding: 10px;">
        <?php if ($last_7_c) { ?>
            <a href="<?php echo get_permalink( $last_7_c->ID ); ?>">
            <?php echo $last_7_c->post_title; ?>
            </a> - (<?php echo $last_7_c->c; ?>)
        <?php } else { ?>
            No stories w/comments in last 7 days.
        <?php } ?>
        </p>
    </h3>
    <br/>
    <h3 style="padding-bottom: 5px;">
        <em style="color: #cdcdcd">Most viewed story in last 30 days</em>
    </h3>
    
    <h3 style="padding-bottom: 5px;">
       <em style="color: #cdcdcd">Most viewed story in last 7 days</em>
    </h3>
    
</div>


