<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Kohana/Codebench
 * @category   Tests
 * @author     Geert De Deckere <geert@idoe.be>
 */
class Bench_Reflection extends Codebench {

	public $description =
		'Testing reflection vs get_class_*';

	public $loops = 1000;

	public function bench_reflection()
	{
		$foo = new ReflectionClass('View_Complex');
		foreach ($foo->getProperties() as $property)
		{
			if (substr_count($property, 'var_'))
			{

			}
		}

		// Get the var_ methods
		foreach ($foo->getMethods() as $method)
		{
			if (substr_count($method, 'var_'))
			{

			}
		}

	}

	public function bench_functions()
	{
		$foo = new View_Complex;
		// Get the var_ properties
		foreach (get_object_vars($foo) as $property => $value)
		{
			if (substr_count($property, 'var_'))
			{
				
			}
		}

		// Get the var_ methods
		foreach (get_class_methods($foo) as $method)
		{
			if (substr_count($method, 'var_'))
			{
				
			}
		}
	}


}