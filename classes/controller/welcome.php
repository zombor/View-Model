<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller {

	public function action_test_new()
	{
		echo new View_Complex;
	}
} // End Welcome
