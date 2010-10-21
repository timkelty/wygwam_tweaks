<?php

/**
*
* @package Wygwam Tweaks
* @version 1.0
* @author Tim Kelty <http://fusionary.com>
* @copyright Copyright (c) 2010 Tim Kelty
* @license http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution-Share Alike 3.0 Unported
* 
*/

if ( ! defined('EXT'))
{
	exit('Invalid file request');
}

class Wygwam_tweaks_ext {
  
  var $name           = 'Wygwam Tweaks';
  var $version        = '1.0';
  var $description    = '';
  var $settings_exist = 'n';
  var $docs_url       = '';
	
	/**
	 * PHP4 constructor.
	 * @see __construct
	 */
	function Wygwam_tweaks_ext()
	{
		$this->__construct();
	}
  
  /**
   * PHP5 constructor
   * @param array|string $settings Extension settings; associative array or empty string.
   */
  function __construct()
  {
    global $PREFS;
    
    // Initialise the class name.
		$this->class_name = strtolower(get_class($this));
    $this->site_id = $PREFS->ini('site_id');		
    		
  }		
		
	function activate_extension()
	{
		global $DB;
				
    // Delete old hooks
		$DB->query("DELETE FROM exp_extensions
		            WHERE class = '{$this->class_name}'");

    // hooks
		$hooks = array(
  		'wygwam_config' => 'wygwam_config',
    );
		
		foreach ($hooks AS $hook => $method)
		{
			$sql[] = $DB->insert_string('exp_extensions', array(
					'class'        => get_class($this),
					'method'       => $method,
					'hook'         => $hook,
					'priority'     => 10,
					'version'      => $this->version,
					'enabled'      => 'y'
					));
		}
		
		// Run all the SQL queries.
		foreach ($sql AS $query)
		{
			$DB->query($query);
		}
	}

	/**
	 * Updates the extension.
	 * @param string $current Contains the current version if the extension is already installed, otherwise empty.
	 * @return bool FALSE if the extension is not installed, or is the current version.
	 */
	function update_extension($current='')
	{
		global $DB;

		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}

		if ($current < $this->version)
		{
			$DB->query("UPDATE exp_extensions
				SET version = '" . $DB->escape_str($this->version) . "' 
				WHERE class = '" . get_class($this) . "'");
		}
	}

  /**
   * Disables the extension, and deletes settings from the database.
   */
  function disable_extension()
  {
  	global $DB;	
  	$DB->query("DELETE FROM exp_extensions WHERE class = '" . get_class($this) . "'");
  }
  

	function wygwam_config($config, $settings)
	{
		global $EXT;
		$config = ($EXT->last_call !== FALSE) ? $EXT->last_call : $config;
		
    // get rid of the upload tab
		$config['filebrowserUploadUrl'] = FALSE;
		$config['filebrowserImageUploadUrl'] = FALSE;
		$config['filebrowserFlashUploadUrl'] = FALSE;
		
		return $config;
	}	
  	
}
?>
