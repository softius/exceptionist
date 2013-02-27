<?php

namespace Exceptionist;

/**
 * Generic implementation of ExceptionHandler
 */
class GenericExceptionHandler implements ExceptionHandler
{
	/**
	 * Exception reporter to be used when generating the report
	 * @var \Exceptionist\ExceptionReporter
	 */
	private $reporter;

	public function __construct($options = array())
	{
		$this->options = array_merge(
			array(
				'project_root' => null,
				'code_block_length' => 10,
				'template_script' => null,
			),
			$options
		);

		if (null != $this->options['template_script']) {
			$this->getReporter()->setTemplate($this->options['template_script']);
		}
	}
	
	/**
	 * Returns exception reporter
	 * @return \Exceptionist\ExceptionReporter
	 */
	public function getReporter() 
	{
		if (null == $this->reporter) {
			$this->reporter = new ExceptionReporter();
		}
		
		return $this->reporter;
	}
	
	/**
	 * Assigns exception reporter
	 * @param \Exceptionist\ExceptionReporter $report
	 */
	public function setReporter(\Exceptionist\ExceptionReporter $reporter)
	{
		$this->reporter = $reporter;
	}
	
	/**
	 * Provides a handler for exceptions, to be used with set_exception_handler
	 * It displays the report, as returned by getReport
	 * @param \Exception $exception any exception
	 * @see \Exceptionist\GenericExceptionHandler::getReport()
	 */
	public function handle(\Exception $exception)
	{
		echo $this->getReport($exception);
	}
	
	/**
	 * Returns a report for the provided exception
	 * @param \Exception $exception any exception
	 */
	public function getReport(\Exception $exception)
	{
		$this->prepareExceptionSource($exception);
		$this->prepareStackTrace($exception);
		
		// renders and returns report content
		return $this->getReporter()->getContent();
	}

	private function prepareExceptionSource($exception)
	{
		$reader = new PhpReader();
		
		// Variables for exception source
		$this->getReporter()
				->setVar('exception_class', get_class($exception))
				->setVar('exception_hashcode', ((0 != $exception->getCode()) ? '#'.$exception->getCode() : null))
				->setVar('exception_message', $exception->getMessage())
				->setVar('exception_file', $this->minimizeFilepath($exception->getFile()))
				->setVar('exception_fileline', $exception->getLine()-1);
	
		$reader->setFile($exception->getFile());
		$this->getReporter()->setVar(
			'exception_codeblock',
			$reader->extract($exception->getLine()-$this->options['code_block_length']/2, $exception->getLine()+$this->options['code_block_length']/2)
		);
	}

	private function prepareStackTrace($exception)
	{
		$reader = new PhpReader();

		// Variables for exception stack trace
		$stacktrace = array();
		foreach ($exception->getTrace() as $trace_entry) {
			$item = array();

			if (array_key_exists('file', $trace_entry) && array_key_exists('line', $trace_entry)) {
				$reader->setFile($trace_entry['file']);
				$item = array(
					'exception_file' => $this->minimizeFilepath($trace_entry['file']),
					'exception_fileline' => $trace_entry['line']-1,
					'exception_codeblock' => $reader->extract($trace_entry['line']-$this->options['code_block_length']/2, $trace_entry['line']+$this->options['code_block_length']/2),
				);
			}
			
			// Class::Method Vs Function
			if (array_key_exists('class', $trace_entry)) {
				$item['exception_call_signature'] = $trace_entry['class'] . $trace_entry['type'] . $trace_entry['function'] . '()';
			} elseif(array_key_exists('function', $trace_entry)) {
				$item['exception_call_signature'] = $trace_entry['function'] . '()';
			} else {
				$item['exception_call_signature'] = null;
			}

			$item['exception_args'] = $this->prepareArguments($trace_entry);

			$stacktrace[] = $item;
		}

		$this->getReporter()->setVar('exception_trace', $stacktrace);
	}

	private function prepareArguments($trace_item)
	{
		$reflection_args = null;
		if (class_exists('ReflectionMethod')) {
			if (empty($trace_item['class'])) {
				$reflection = new \ReflectionFunction($trace_item['function']);
			} else {
				$reflection = new \ReflectionMethod($trace_item['class'], $trace_item['function']);
			}
			$reflection_args = $reflection->getParameters();
		}

		$args = array();
		foreach ($trace_item['args'] as $k => $trace_arg) {
			if (is_object($trace_arg)) {
				$value = get_class($trace_arg);
			} else {
				$value = $trace_arg;
			}

			$args[] = array(
				'name' => (null !== $reflection_args && isset($reflection_args[$k])) ? $reflection_args[$k]->getName() : null,
				'value' => $value,
			);
		}

		return $args;
	}

	protected function minimizeFilepath($file)
	{
		return str_replace($this->options['project_root'], null, $file);
	}
}