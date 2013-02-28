<?php

namespace Exceptionist;

class ReportBuilder
{
	/**
	 * Exception reporter to be used when generating the report
	 * @var \Exceptionist\ExceptionReporter
	 */
	private $reporter;

	/**
	 * @var \Exceptionist\PhpReader
	 */
	private $php_reader;

	/**
	 * Builds a new report for the provided exception using the specified reporter
	 * @param \Exceptionist\ExceptionReporter $reporter
	 * @param \Exception $exception
	 * @param array $options
	 */
	public function build($reporter, \Exception $exception, $options = array())
	{
		$this->setReporter($reporter);
		$this->prepareExceptionSource($exception, $options);
		$this->prepareStackTrace($exception, $options);
	}

	/**
	 * Prepares a set of report variables related to exception source
	 * @param \Exception $exception
	 * @parram array $options
	 */
	protected function prepareExceptionSource(\Exception $exception, $options = array())
	{
		// Variables for exception source
		$this->getReporter()
				->setVar('exception_class', get_class($exception))
				->setVar('exception_hashcode', ((0 != $exception->getCode()) ? '#'.$exception->getCode() : null))
				->setVar('exception_message', $exception->getMessage())
				->setVar('exception_file', $this->minimizeFilepath($exception->getFile(), $options['project_root']))
				->setVar('exception_fileline', $exception->getLine()-1);
	
		$this->getReader()->setFile($exception->getFile());
		$this->getReporter()->setVar(
			'exception_codeblock',
			$this->getReader()->extract($exception->getLine()-$options['code_block_length']/2, $exception->getLine()+$options['code_block_length']/2)
		);
	}


	/**
	 * Prepares a set of report variables related to stack trace
	 * @param \Exception $exception
	 * @parram array $options
	 */
	protected function prepareStackTrace(\Exception $exception, $options = array())
	{
		// Variables for exception stack trace
		$stacktrace = array();
		foreach ($exception->getTrace() as $trace_entry) {
			$item = array();

			if (array_key_exists('file', $trace_entry) && array_key_exists('line', $trace_entry)) {
				$this->getReader()->setFile($trace_entry['file']);
				$item = array(
					'exception_file' => $this->minimizeFilepath($trace_entry['file'], $options['project_root']),
					'exception_fileline' => $trace_entry['line']-1,
					'exception_codeblock' => $this->getReader()->extract($trace_entry['line']-$options['code_block_length']/2, $trace_entry['line']+$options['code_block_length']/2),
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

	/**
	 * Prepares a set of report variables related to stack trace itme
	 * @parram array $trace_time
	 */
	protected function prepareArguments($trace_item)
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

	private function minimizeFilepath($file, $prefix)
	{
		return str_replace($prefix, null, $file);
	}

	/**
	 * Initializes and returns an instance of PhpReader
	 */
	private function getReader()
	{
		if (null == $this->php_reader) {
			$this->php_reader = new PhpReader();
		}

		return $this->php_reader;
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
}