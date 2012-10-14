<div id="fronthead">
<?php

echo '<a id="homeurl" href="' . base_url() . '">spiffcloud.com</a>';
echo '<a style="display: none;" id="title">aafront</a>';

if($action == 'create'){

	echo '<a id="create" href="' . site_url('x/create') . '">create</a>';
	echo '<form id="createform" method="post" action="">';
	echo '<p id="slash">/</p><div><input id="spacename" name="spacename" /></div>';
	echo '<div><input id="createsubmit" type="submit" value="create" /></div>';
	echo '</form>';

}else{

	echo '<a id="create" href="' . site_url('x/create') . '">create</a>';

}

?>

<!--a> | relevance</a-->
<a> | search: </a>
<form id="searchform" method="post" action="">
<?php if(isset($_POST['search'])){ ?>
<p><input id="search" name="search" value="<?php echo htmlspecialchars($_POST['search']); ?>" /></p>
<?php }else{ ?>
<p><input id="search" name="search" /></p>
<?php } ?>
</form>
<!--a> | more...</a-->
</div>

