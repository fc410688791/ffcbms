<?php

defined('BASEPATH') OR exit('不允许直接访问');

/**
 * Class Benchmark
 * 这类使您可以标记点，计算它们之间的时间差
 * 内存消耗也可以显示。
 */
class Benchmark {
	/**
	 * 记录程序运行到某个点的时间
	 * $marker：Benchmark类内部用于存放所有标记点的数组
	 *
	 * @var	array
	 */
	public $marker = array();

	/**
	 * 加入一个mark,这个可以在程序任意地方调用，加入一个mark
	 *
	 * mark($name)方法，设置标记点，并插入一下key为标记名，值为当前时间的元素到$this->marker数组
	 *
	 * @param	string	$name	Marker name
	 * @return	void
	 */
	public function mark($name)
	{
		$this->marker[$name] = microtime(TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * 计算时间
	 *
	 * 计算任意两个点之间的运行时间
	 *
	 * If the first parameter is empty this function instead returns the
	 * {elapsed_time} pseudo-variable. This permits the full system
	 * execution time to be shown in a template. The output class will
	 * swap the real value for this variable.
	 *
	 * @param	string	$point1		A particular marked point
	 * @param	string	$point2		A particular marked point
	 * @param	int	$decimals	Number of decimal places
	 *
	 * @return	string	Calculated elapsed time on success,
	 *			an '{elapsed_string}' if $point1 is empty
	 *			or an empty string if $point1 is not found.
	 */
	public function elapsed_time($point1 = '', $point2 = '', $decimals = 4)
	{
		if ($point1 === '')
		{
			return '{elapsed_time}';
		}

		if ( ! isset($this->marker[$point1]))
		{
			return '';
		}

		if ( ! isset($this->marker[$point2]))
		{
			$this->marker[$point2] = microtime(TRUE);
		}

		return number_format($this->marker[$point2] - $this->marker[$point1], $decimals);
	}

	// --------------------------------------------------------------------

	/**
	 * 显示内存占用
	 *
	 * Simply returns the {memory_usage} marker.
	 *
	 * This permits it to be put it anywhere in a template
	 * without the memory being calculated until the end.
	 * The output class will swap the real value for this variable.
	 *
	 * @return	string	'{memory_usage}'
	 */
	public function memory_usage()
	{
		return '{memory_usage}';
	}

}
