<?php

namespace Plumbum\Pixtache;

/**
 * Haml View.
 * You can treat it as a regular View,
 * as it follows the same interface.
 * The only difference is that the template must have
 * a .haml extension.
 *
 * @package    Haml
 */
class View extends \PHPixie\View {

	/**
	 * Mustache Parser
	 * @var \Mustache\Environment   
	 */
	protected $_parser;
	
	/**
	 * File extension of the templates
	 * @var string   
	 */
	protected $_extension = 'mustache';
	
	/**
	 * Constructs the haml view.
	 * 
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 * @param \PHPixie\View\Helper View Helper
	 * @param string   $name The name of the template to use.
	 * @return Haml    
	 */
	public function __construct($pixie, $helper, $name) {
		parent::__construct($pixie, $helper, $name);
        $this->_parser = new \Mustache_Engine(array(
            'cache' => $pixie->root_dir.$pixie->config->get('pixtache.cache_dir','/runtime/cache/templates').'/',
            'loader' => new \Mustache_Loader_FilesystemLoader($this->path_dir),
            'partials_loader' => new \Mustache_Loader_FilesystemLoader($this->path_dir),
            'logger' => new \Mustache_Logger_StreamLogger('php://stderr'),
        ));
	}
	
	/**
	 * Sets the template to use for rendering
	 *
	 * @param string   $name The name of the template to use
	 * @throws \Exception If specified template is not found
	 */
	public function set_template($name) {
		$this->name = $name;

		$template_dir = $this->pixie->config->get('pixtache.template_dir','views');

        $file = $this->pixie->find_file($template_dir, $name, $this->_extension);
			
		if ($file == false)
			throw new \Exception("View {$name} not found.");
			
		$this->path = $file;
        $this->path_dir = dirname($file);
	}

	/**
	 * Renders the template, all dynamically set properties
	 * will be available inside the view file as variables.
	 *
	 * @return string Rendered template
	 * @see \PHPixie\View::render()
	 */
	public function render() {
        $tpl = $this->_parser->loadTemplate($this->name);
		// extract($this->helper->get_aliases());
		// extract($this->_data);
        $out = $tpl->render($this->_data);

        //$out = '<pre>'.print_r($this, true).'</pre>'.$out;
		return $out;
	}
	
}
