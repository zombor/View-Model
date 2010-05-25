<?php

class View_Complex extends Kohana_View
{
	public $var_title = '<script>alert("omg xss!")</script>';

	public function var_things()
	{
		return Inflector::plural(get_class(new Model_Test));
	}

	public function var_tests()
	{
		return AutoModeler::factory('test')->fetch_all();
	}
}