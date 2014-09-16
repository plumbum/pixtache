<?php

namespace PHPixie\Pixtache;

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
	protected $_extension = 'tpl';
	
    protected $_tpl;

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

        $parser = new \Mustache_Engine(array(
            'loader' => new \Mustache_Loader_FilesystemLoader($this->path_dir,
                array('extension'=>'.'.$this->_extension)),
            'partials_loader' => new \Mustache_Loader_FilesystemLoader($this->path_dir.'/partials',
                array('extension'=>'.'.$this->_extension)),
            'logger' => new \Mustache_Logger_StreamLogger('php://stderr'),
            'escape' => function($value) {
                return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
            },
            'charset' => 'UTF-8',
            'strict_callables' => true,
            'pragmas' => [\Mustache_Engine::PRAGMA_FILTERS],

            'cache' => $pixie->root_dir.$pixie->config->get('pixtache.cache_dir','/runtime/cache/templates').'/',
            'cache_file_mode' => 0666, // Please, configure your umask instead of doing this :)
            'template_class_prefix' => '__pixtache_',
            'cache_lambda_templates' => true,
        ));
        $parser->addHelper('case', array(
            'lower' => function($v) { return strtolower((string) $v); },
            'upper' => function($v) { return strtoupper((string) $v); },
            'first' => function($v) { return ucfirst((string) $v); },
        ));

        $this->_parser = $parser;
	}
	
	/**
	 * Sets the template to use for rendering
	 *
	 * @param string   $name The name of the template to use
	 * @throws \Exception If specified template is not found
	 */
	public function set_template($name) {
		$this->name = $name;

		$this->_template_dir = $this->pixie->config->get('pixtache.template_dir','views');

        $file = $this->pixie->find_file($this->_template_dir, $name, $this->_extension);
			
		if ($file == false)
			throw new \Exception("View {$name} not found.");
			
		$this->path = $file;
        $this->path_dir = dirname($file);
	}

	/**
	 * Renders the template, all dynamically set properties
	 * will be available inside the view file as variables.
	 *
	 */
	public function render() {
        $tpl = $this->_parser->loadTemplate($this->name);
        $out = $tpl->render($this);
		return $out;
	}
	
    /* Access to array $_data as properties */
    public function __get($key) {
        if($skey = $this->check_prefix($key)) {
            return !empty($this->_data[$skey]);
        } else {
            if(isset($this->_data[$key])) {
                return $this->_data[$key];
            } else {
                // throw new \Exception("Undefined {$key} variable.");
            }
        }
    }

    public function __isset($key) {
        if($skey = $this->check_prefix($key)) {
            return isset($this->_data[$skey]);
        } else {
            return isset($this->_data[$key]);
        }
    }

    private function check_prefix($key, $prefix = 'is_') {
        $len = strlen($prefix);
        if(substr($key, 0, $len) == $prefix) {
            $skey = substr($key, $len);
            return $skey;
        } else {
            return NULL;
        }
    }

}
