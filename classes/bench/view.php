<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Kohana/Codebench
 * @category   Tests
 * @author     Geert De Deckere <geert@idoe.be>
 */
class Bench_View extends Codebench {

	public $description =
		'Testing different view methods';

	public $loops = 1000;

	public function bench_old_view($subject)
	{

		$view = Kohana_OriginalView::factory('originalview');
		$view->title = 'Foobar';
		$view->things = Inflector::plural(get_class(new Model_Test));
		$view->tests = AutoModeler::factory('test')->fetch_all();
		$test = $view->render();

	}

	public function bench_new_view($subject)
	{
		$foo = new View_Complex;
		$test = $foo->render();
	}

	public function bench_mustache($subject)
	{
		$foo = new View_Example;
		$test = $foo->render();
	}

}