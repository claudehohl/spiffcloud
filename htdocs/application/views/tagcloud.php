<?php

if(!isset($ajax)){

	echo '<div id="tagcloud">';
	echo '<div id="words">';

}

$adata = array();

foreach($words as $word){

	$hl = '';

	if($word['timestamp'] > (mktime() - 5)){

		$hl = ' class="highlight"';

	}

	echo '<a'
	. $hl
	. ' href="'
	. base_url() . space_name($word['word'])
	. '" style="font-size: '
	.  $word['size']
	. 'pt; color: #'
	. $word['color']
	. '">'
	.  htmlspecialchars($word['word'])
	. '</a> ';

}

if(!isset($ajax)){

	echo '</div>';

}

if(isset($input)){

	echo '<form id="inputarea" action="" method="post">';
	echo '<p><input';

	if($this->uri->segment(2) == 'error'){

		echo ' class="error"';

	}

	echo ' id="word" name="word" maxlength="50" /></p>';
	echo '</form>';

}

if(!isset($ajax)){

	echo '</div>';

}

?>

