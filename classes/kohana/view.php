<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Acts as an object wrapper for output with embedded PHP, called "views".
 * Variables can be assigned with the view object and referenced locally within
 * the view.
 *
 * @package    Kohana
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2008-2010 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_View {

	// Array of global variables
	protected static $_global_data = array();

	// View filename
	protected $_file;

	// Encoded view data
	protected $_data = array();

	/**
	 * Returns a new raw View object. If you do not define the "file" parameter,
	 * you must call [View::set_filename].
	 *
	 *     $view = View::factory($file);
	 *
	 * @param   string  view filename
	 * @param   array   array of values
	 * @return  View
	 */
	public static function factory($file = NULL, array $data = NULL)
	{
		return new View($file);
		// Return a raw view object if no template is specified.
		if ($file === FALSE)
			return new View(FALSE, $data);

		$class = 'View_'.strtr($file, '/', '_');
		return new $class($file, $data);
	}

	/**
	 * Captures the output that is generated when a view is included.
	 * The view data will be extracted to make local variables.
	 *
	 *     $output = $this->capture($file, $data);
	 *
	 * @param   string  filename
	 * @param   array   variables
	 * @return  string
	 */
	protected function capture($kohana_view_filename, array $kohana_view_data)
	{
		if ( ! in_array('kohana.view', stream_get_wrappers()))
		{
			stream_wrapper_register('kohana.view', get_class($this));
		}

		// Import the view variables to local namespace
		extract($kohana_view_data, EXTR_SKIP);

		// Capture the view output
		ob_start();

		try
		{
			include 'kohana.view://'.$kohana_view_filename;
		}
		catch (Exception $e)
		{
			// Delete the output buffer
			ob_end_clean();

			// Re-throw the exception
			throw $e;
		}

		// Get the captured output and close the buffer
		return ob_get_clean();
	}

	/**
	 * Sets a global variable, similar to [View::set], except that the
	 * variable will be accessible to all views.
	 *
	 *     View::set_global($name, $value);
	 *
	 * @param   string  variable name or an array of variables
	 * @param   mixed   value
	 * @return  void
	 */
	public static function set_global($key, $value = NULL)
	{
		if (is_array($key))
		{
			foreach ($key as $key2 => $value)
			{
				View::$_global_data[$key2] = $value;
			}
		}
		else
		{
			View::$_global_data[$key] = $value;
		}
	}

	/**
	 * Assigns a global variable by reference, similar to [View::bind], except
	 * that the variable will be accessible to all views.
	 *
	 *     View::bind_global($key, $value);
	 *
	 * @param   string  variable name
	 * @param   mixed   referenced variable
	 * @return  void
	 */
	public static function bind_global($key, & $value)
	{
		View::$_global_data[$key] =& $value;
	}

	/**
	 * Sets the initial view filename and local data. Views should almost
	 * always only be created using [View::factory].
	 *
	 *     $view = new View($file);
	 *
	 * @param   string  view filename
	 * @param   array   array of values
	 * @return  void
	 * @uses    View::set_filename
	 */
	public function __construct($file = NULL, array $data = NULL)
	{
		if ($file === NULL)
		{
			$foo = explode('_', get_class($this));
			array_shift($foo);
			$file = strtolower(implode('/', $foo));
			$this->set_filename($file);
		}
		elseif ($file !== FALSE)
		{
			$this->set_filename($file);
		}

		if ( $data !== NULL)
		{
			// Add the values to the current data
			$this->_data = $data + $this->_data;
		}
	}

	/**
	 * Magic method, searches for the given variable and returns its value.
	 * Local variables will be returned before global variables.
	 *
	 *     $value = $view->foo;
	 *
	 * [!!] If the variable has not yet been set, an exception will be thrown.
	 *
	 * @param   string  variable name
	 * @return  mixed
	 * @throws  Kohana_Exception
	 */
	public function & __get($key)
	{
		if (isset($this->_data[$key]))
		{
			return $this->_data[$key];
		}
		elseif (isset(View::$_global_data[$key]))
		{
			return View::$_global_data[$key];
		}
		else
		{
			throw new Kohana_Exception('View variable is not set: :var',
				array(':var' => $key));
		}
	}

	/**
	 * Magic method, calls [View::set] with the same parameters.
	 *
	 *     $view->foo = 'something';
	 *
	 * @param   string  variable name
	 * @param   mixed   value
	 * @return  void
	 */
	public function __set($key, $value)
	{
		$this->set($key, $value);
	}

	/**
	 * Magic method, determines if a variable is set.
	 *
	 *     isset($view->foo);
	 *
	 * [!!] `NULL` variables are not considered to be set by [isset](http://php.net/isset).
	 *
	 * @param   string  variable name
	 * @return  boolean
	 */
	public function __isset($key)
	{
		return (isset($this->_data[$key]) OR isset(View::$_global_data[$key]));
	}

	/**
	 * Magic method, unsets a given variable.
	 *
	 *     unset($view->foo);
	 *
	 * @param   string  variable name
	 * @return  void
	 */
	public function __unset($key)
	{
		unset($this->_data[$key], View::$_global_data[$key]);
	}

	/**
	 * Magic method, returns the output of [View::render].
	 *
	 * @return  string
	 * @uses    View::render
	 */
	public function __toString()
	{
		try
		{
			return $this->render();
		}
		catch (Exception $e)
		{
			// Display the exception message
			Kohana::exception_handler($e);

			return '';
		}
	}

	/**
	 * Sets the view filename.
	 *
	 *     $view->set_filename($file);
	 *
	 * @param   string  view filename
	 * @return  View
	 * @throws  Kohana_View_Exception
	 */
	public function set_filename($file)
	{
		if (($path = Kohana::find_file('views', $file)) === FALSE)
		{
			//throw new Kohana_View_Exception('The requested view :file could not be found', array(
			//	':file' => $file,
			//));
		}

		// Store the file path locally
		$this->_file = $path;

		return $this;
	}

	/**
	 * Assigns a variable by name. Assigned values will be available as a
	 * variable within the view file:
	 *
	 *     // This value can be accessed as $foo within the view
	 *     $view->set('foo', 'my value');
	 *
	 * You can also use an array to set several values at once:
	 *
	 *     // Create the values $food and $beverage in the view
	 *     $view->set(array('food' => 'bread', 'beverage' => 'water'));
	 *
	 * @param   string   variable name or an array of variables
	 * @param   mixed    value
	 * @return  $this
	 */
	public function set($key, $value = NULL)
	{
		if (is_array($key))
		{
			foreach ($key as $name => $value)
			{
				$this->_data[$name] = $value;
			}
		}
		else
		{
			$this->_data[$key] = $value;
		}

		return $this;
	}

	/**
	 * Assigns a value by reference. The benefit of binding is that values can
	 * be altered without re-setting them. It is also possible to bind variables
	 * before they have values. Assigned values will be available as a
	 * variable within the view file:
	 *
	 *     // This reference can be accessed as $ref within the view
	 *     $view->bind('ref', $bar);
	 *
	 * @param   string   variable name
	 * @param   mixed    referenced variable
	 * @return  $this
	 */
	public function bind($key, & $value)
	{
		$this->_data[$key] =& $value;

		return $this;
	}

	/**
	 * Renders the view object to a string. Global and local data are merged
	 * and extracted to create local variables within the view file.
	 *
	 *     $output = View::render();
	 *
	 * [!!] Global variables with the same key name as local variables will be
	 * overwritten by the local variable.
	 *
	 * @param    string  view filename
	 * @return   string
	 * @throws   Kohana_View_Exception
	 * @uses     View::capture
	 */
	public function render($file = NULL)
	{
		if ($file !== NULL)
		{
			$this->set_filename($file);
		}

		if (empty($this->_file))
		{
			throw new Kohana_View_Exception('You must set the file to use within your view before rendering');
		}

		// Combine local and global data and capture the output
		return $this->capture($this->_file, $this->_data + View::$_global_data);
	}

	/**
	 * Define stream wrapper methods
	 */

	/**
	 * Current stream position.
	 *
	 * @var int
	 */
	protected $_pos = 0;

	/**
	 * Stream stats.
	 *
	 * @var array
	 */
	protected $_stat;

	/**
	 * Raw output character. Prepend this on any echo variables to
	 * turn off auto encoding of the output
	 */
	protected $_raw_output_char = '!';

	/**
	 * The encoding method to use on view output. Only use the method name
	 */
	protected $_encode_method = 'HTML::chars';

	/**
	 * Opens the script file and converts markup.
	 */
	public function stream_open($path, $mode, $options, &$opened_path)
	{
		// get the view script source
		$path        = str_replace('kohana.view://', '', $path);
		$this->_data = file_get_contents($path);

		/**
		 * If reading the file failed, update our local stat store
		 * to reflect the real stat of the file, then return on failure
		 */
		if ($this->_data === false)
		{
			$this->_stat = stat($path);
			return false;
		}

		/**
		 * Convert <?= ?> to long-form <?php echo ?> and <? ?> to <?php ?>
		 *
		 */
		$regex = '/<\?(\=|php echo)(.+?)\?>/';
		$this->_data = preg_replace_callback($regex, array($this, '_escape_val'), $this->_data);

		/**
		 * file_get_contents() won't update PHP's stat cache, so we grab a stat
		 * of the file to prevent additional reads should the script be
		 * requested again, which will make include() happy.
		 */
		$this->_stat = stat($path);

		return true;
	}

	/**
	 * Escapes a variable from template matching
	 *
	 * @param   array   matches
	 * @return  string
	 */
	protected function _escape_val($matches)
	{
		if (method_exists($this, str_replace('$', 'var_', $matches[2])))
		{
			$var = str_replace('$', '$this->var_', $matches[2]).'()';
		}
		else
		{
			$var = str_replace('$', '$this->var_', $matches[2]);
		}

		if (substr(trim($matches[2]), 0, 1) != $this->_raw_output_char)
		{
			return '<?php echo '.$this->_encode_method.'('.$var.'); ?>';
		}
		else // Remove the "turn off escape" character
			return '<?php echo '.substr(trim($var), strlen($this->_raw_output_char), strlen($var)-1).'(); ?>';
	}

	/**
	 * Included so that __FILE__ returns the appropriate info
	 *
	 * @return array
	 */
	public function url_stat()
	{
		return $this->_stat;
	}

	/**
	 * Reads from the stream.
	 */
	public function stream_read($count)
	{
		$ret = substr($this->_data, $this->_pos, $count);
		$this->_pos += strlen($ret);
		return $ret;
	}

	/**
	 * Tells the current position in the stream.
	 */
	public function stream_tell()
	{
		return $this->_pos;
	}

	/**
	 * Tells if we are at the end of the stream.
	 */
	public function stream_eof()
	{
		return $this->_pos >= strlen($this->_data);
	}

	/**
	 * Stream statistics.
	 */
	public function stream_stat()
	{
		return $this->_stat;
	}

	/**
	 * Seek to a specific point in the stream.
	 */
	public function stream_seek($offset, $whence)
	{
		switch ($whence)
		{
			case SEEK_SET:
				if ($offset < strlen($this->_data) && $offset >= 0)
				{
					$this->_pos = $offset;
					return true;
				}
				else
				{
					return false;
				}
				break;

			case SEEK_CUR:
				if ($offset >= 0)
				{
					$this->_pos += $offset;
					return true;
				}
				else
				{
					return false;
				}
				break;

			case SEEK_END:
				if (strlen($this->_data) + $offset >= 0)
				{
					$this->_pos = strlen($this->_data) + $offset;
					return true;
				}
				else
				{
					return false;
				}
				break;

			default:
				return false;
		}
	}

} // End View