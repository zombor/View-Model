<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title><?=$title?></title>
	</head>
	<body>
		<h1><?=html::chars($title)?></h1>
		<p>Here are all my <?=html::chars($things)?>:</p>
		<ul>
			<?php foreach ($tests as $test):?>
			<li><strong><?=$test->id?>:</strong> (<?=$test->name?>:<?=$test->value?>)</li>
			<?php endforeach;?>
		</ul>
		<h2>
	</body>
</html>