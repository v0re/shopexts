<?php

	require('gimage.class.php');
	$size = array('150'=>'150','300'=>'300','600'=>'600');
	$gimage = new Gimage();
	$gimage->resampledImage('2006110204184215522.jpg',$size);
?>