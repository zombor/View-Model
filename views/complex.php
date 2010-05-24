<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title><?=$title?></title>
	</head>
	<body>
		<h1><?=$title?></h1>
		<p>Here are all my <?=$things?>:</p>
		<ul>
			<?php foreach ($tests as $test):?>
			<li><strong><?=$test->id?>:</strong> (<?=$test->name?>:<?=$test->value?>)</li>
			<?php endforeach;?>
		</ul>
	</body>
</html><?=!Kohana::debug($this)?>