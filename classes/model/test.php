<?php

class Model_Test extends AutoModeler
{
	protected $_table_name = 'tests';

	protected $_data = array(
		'id' => '',
		'name' => '',
		'value' => '',
	);
	
	protected $_rules = array(
		'name' => array('not_empty'),
		'value' => array('not_empty'),
	);
}