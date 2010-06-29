Kohana-View
============

Kohana-View is a replacement for the default Kohana v3 class.

Why you should use it
============

 - Class based views
   - Lighter controllers
   - Takes logic out of your view templates
 - Auto escaping of variable output
   - Automatically escapes all view variables automatically
   - Also lets you obtain "raw" data by prepending your variable with ! (<?=!$foobar?>)

See the examples branch for examples and benchmarking.

This class is mostly backwards compatible. You can use it in replacement of the original view class when it was used in basic usage.

Notes
============

If you used views in your application without setting the filename initially, you must change those calls so that the first parameter is boolean FALSE:

	$foo = View::factory(FALSE);
	$foo->set_filename('foobar');
	echo $foo;

Setup
============

Put your view class files in the classes/view/ directory and name them the same as your other classes.

	// Note this is 'view' and not 'views'
	// application/classes/view/foo/bar.php
	class View_Foo_Bar extends View

Create the associated view file (template) in your views directory with the same filename and path as your view class.

	// application/views/foo/bar.php

Usage
============

Create your view template file the same as you would have before, but remember that any logic belongs in the view class.  If you want to prevent the automatic variable escaping, prepend the variable with an '!'.

	// application/views/foo/bar.php
	<p>This view is <?=$adjective?>!</p>
	<p>Another possible description is: <?=$random_adjective?></p>
	<p>Here are my adjective notes: <?=!$adjective_note?></p>

Your view class will pass on properties and methods that begin with 'var_' to the template.

	// application/classes/view/foo/bar.php
	class View_Foo_Bar extends View {

		public $adjectives = array('awesome', 'neato', 'cool beans');

		public $var_adjective = 'very nice';

		public function var_random_adjective()
		{
			return array_rand($this->adjectives);
		}

		public function var_adjective_note()
		{
			$note = new Model_Adjective($this->var_adjective);
			return $note->note;
		}
	}

And in your controller you could do

	$bar = View::factory('foo/bar')->set('var_adjective', 'the best');
	echo $bar;

which would render:

> This view is the best!
>
> Another possible description is: cool beans
>
> Here are my adjective notes: This is <sup>Super!!</sup>