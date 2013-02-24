<?php

namespace Exceptionist;

class ExceptionReporter
{
	protected $vars;
	protected $template;

	private $templates;

	const TEMPLATE_DETAILED = 0;
			
	public function __construct()
	{
		$this->vars = array();
		$this->template = null;

		$this->templates = array(
			dirname(dirname(__DIR__)) . '/templates/default.php',
		);
	}
	
	public function getTemplate()
	{
		if (null == $this->template) {
			return $this->templates[ExceptionReporter::TEMPLATE_DETAILED];
		}
		
		return $this->template;
	}
	
	public function setTemplate($template)
	{
		if (is_int($template)) {
			if (!array_key_exists($template, $this->templates)) {
				throw new \Exception('Template not found. Perhaps you should provide the full path?');
			} else {
				$this->templates[$template];
			}
		} else {
			$this->template = $template;
		}
	}

	public function setVar($name, $value) 
	{
		$this->vars[$name] = $value;
		return $this;
	}
	
	public function setVars(array $vars)
	{
		$this->vars = $vars;
	}
	
	public function getVars()
	{
		return $this->vars;
	}
	
	public function getContent()
	{
		$vars_table = $this->getVars();
		$report_filename = $this->getTemplate();
		
		$cont_func = function() use ($vars_table, $report_filename){
			ob_start();
			
			extract($vars_table);
			include $report_filename;
			
			return ob_get_clean();
		};
		return $cont_func();
	}
}
