<?php

namespace Exceptionist;

/**
 * Interface for handling exceptions with a report, in development environments
 */
interface ExceptionHandler
{
	/**
	 * Handles the provided exception by executing all the necessary actions.
	 * For instance displaying a debbuging report.
	 * 
	 * @param \Exception $exception
	 */
	public function handle(\Exception $exception);

	/**
	 * Returns a debugging report in HTML for the specified exception
	 * 
	 * @param \Exception $exception
	 */
	public function getReport(\Exception $exception);
}