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
				'project_root' => dirname(dirname(__DIR__)),
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
		$builder = new ReportBuilder();
		$builder->build($this->getReporter(), $exception, $this->options);
		
		// renders and returns report content
		return $this->getReporter()->getContent();
	}

}