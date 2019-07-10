<h1>All Data Types</h1>

<div id="content" class="container">
<?php foreach($all_data_types as $f) { ?>
<div class="tile col-md-3" style="background-color:<?php echo color() ?>">
<a href="?data_type=<?php echo basename($f, '.php') ?>" target="_blank"><?php echo format(str_replace(".php", "", $f)); ?></a>
</div>
<?php } ?>
</div>


<?php

function color() {
	static $index = 0;
	//$col = array('#EEA2AD', '#4876FF', '#1E90FF', '#00BFFF', '#00FA9A', '#76EE00','#CD950C', '#FFDEAD', '#EED5B7', '#FFA07A', '#FF6347', '#EE6363', '#71C671');
	$col = array('#f1632a','#ffe800','#282829','#22bbb8','#7e3f98','#54b847','#f1632a','#ffe800','#282829','#22bbb8','#7e3f98','#54b847','#e5002f');
	$index++;

	if($index >= count($col)) $index = 0;
	return $col[$index];
} 