<?php
/**
 * Check if this is a new post being created
 */

function amcharts_is_new_post () {
	global $pagenow;
	return 'post-new.php' == $pagenow;
}