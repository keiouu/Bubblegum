<?php

/**
 * A processor is defined by:
 *    -> An object that takes an input, modifys it, and then outputs a result
 */
abstract class Processor
{	
	/**
	 * Take $data and modify it
	 * 
	 * @param mixed $data The data to modify
	 */
	abstract function modify($data);
}
