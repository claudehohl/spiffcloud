<data>
<?php foreach($words as $word){ ?>
	<entry>
		<word><?php echo $word['word']; ?></word>
		<weight><?php echo $word['size']; ?></weight>
	</entry>
<?php } ?>
</data>
