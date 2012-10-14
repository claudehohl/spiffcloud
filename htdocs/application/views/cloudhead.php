<div id="cloudhead">
<?php

echo '<a id="homeurl" href="' . base_url() . '">spiffcloud.com</a>';
echo '<a id="title" href="' . site_url($space_name) . '">';

if($space_name != ''){

	echo '/' . $space_name;

}

echo '</a>';

?>

</div>

