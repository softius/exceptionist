<?php

namespace Exceptionist;

class PhpReader
{
	private $file;
	
	/**
	 * Assigns a file to be used as a source for this reader
	 * @param string $file
	 */
	public function setFile($file)
	{
		$this->file = $file;
	}
	
	/**
	 * Returns the source file currently assigned to this reader
	 * @return string
	 */
	public function getFile()
	{
		return $this->file;
	}
	
	/**
	 * Extracts a part of php file and returns it
	 * @param int $from
	 * @param int $to
	 * @return array
	 */
	public function extract($from, $to)
	{
		// @todo need a better way to achieve this
		$from = max($from, 1);
		$length = max($to-$from, 0);
		return array_slice(file($this->getFile()), $from, $length, true);
	}
}