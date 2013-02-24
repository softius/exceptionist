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

		// Variables for exception stack trace
		$trace = array();
		foreach ($exception->getTrace() as $trace_entry) {
			$reader->setFile($trace_entry['file']);
			$trace[] = array(
				'exception_file' => $this->minimizeFilepath($trace_entry['file']),
				'exception_fileline' => $trace_entry['line']-1,
				'exception_codeblock' => $reader->extract($trace_entry['line']-$this->options['code_block_length']/2, $trace_entry['line']+$this->options['code_block_length']/2),
			);
		}

		$this->getReporter()->setVar('exception_trace', $trace);
		
		// renders and returns report content
		return $this->getReporter()->getContent();
	}

	protected function minimizeFilepath($file)
	{
		return str_replace($this->options['project_root'], null, $file);
	}
}