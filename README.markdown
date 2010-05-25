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