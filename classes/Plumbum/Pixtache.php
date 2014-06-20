<?php

namespace Plumbum;

/**
 * Haml plugin for PHPixie. 
 * Haml is an alternative markup used for creating
 * well-indented clean templates for HTML.
 *
 * Find out more about haml at http://haml.info/
 * this module allows you to include subtemplates inside
 * your Haml templates. Include templates using 'partial' keyword:
 * <code>
 *	 partial:templatename 
 * </code>
 * 
 * This module is not included by default, install it using Composer
 * by adding
 * <code>
 * 		"plumbum/pixtache": "2.*@dev"
 * </code>
 * to your requirement definition. Or download it from
 * https://github.com/plumbum/pixtache
 * 
 * To enable it add it to your Pixie class' modules array:
 * <code>
 * 		protected $modules = array(
 * 			//Other modules ...
 * 			'pixtache' => '\PHPixie\Pixtache',
 * 		);
 * </code>
 *
 * @see \PHPixie\Pixtache\View
 * @link https://github.com/plumbum/pixtache Download this module from Github
 * @package    Pixtache
 */
class Pixtache {

	/**
	 * Pixie Dependancy Container
	 * @var \PHPixie\Pixie
	 */
	public $pixie;
	
	/**
	 * Initializes the Email module
	 * 
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 */
	public function __construct($pixie)	{
		$this->pixie = $pixie;
	}
	
	/**
	 * Fetches a HAML template by name
	 * 
	 * @param string $name Name of the template to fetch
	 * @return \PHPixie\Haml\View
	 */
	public function get($name) {
		return new \Plumbum\Pixtache\View($this->pixie, $this->pixie->view_helper(), $name);
	}
	
}
