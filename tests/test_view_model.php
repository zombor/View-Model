<?php

/**
 * Tests View_Model functionality
 *
 * @group view_model
 */
class View_Model_Test extends PHPUnit_Framework_TestCase
{
	public function test_render()
	{
		$view = new View_Test();

		$expected = file_get_contents(Kohana::find_file('tests', 'output/test', 'txt'));
		$this->assertSame($expected, $view->render());
	}
}

class View_Test extends View_Model
{
	public function var_foo()
	{
		return '<h2>foobar</h2>';
	}
}