<?php
/* 
* Plugin Name: Open Graph Headers for WP
*/

if ( ! defined( 'ABSPATH' ) ) {	
	exit;
}

?>
<!--  Open Graph Meta tags  --> 
<?php  foreach($data as $key=>$value) : ?>
<?php if ( strlen($value) ) : ?>
<meta property="og:<?php echo $key;?>" content="<?php echo $value;?>" />
<?php endif; ?>
<?php  endforeach; ?>
<!-- END: Open Graph Meta tags  -->
