<?php
/**
 * Allows to get property value accumulated from the same properties of parent classes.
 * 
 * This trait provides an ability to get specified static property values gathered (accumulated) from all parent classes.
 * If property's getter method is implemented PropertyValueAccumulation::getAccumulated() method will use it to get poperty's value.
 * $args array will be passed as arguments to the getter method.
 * You can change accumulating order passing 1 (straight) or 0 (reverse) as PropertyValueAccumulation::getAccumulated() method's third argument.
 * Also you can specify whether to accumulate only unique values passing 1 as PropertyValueAccumulation::getAccumulated() method's fourth argument.
 */
trait PropertyValueAccumulation
{
	/**
	 * Returns accumulated value.
	 * 
	 * @param string $name name of the property whose accumulated value this method returns.
	 * @param array $args optional arguments that will be passed to getter method if such is defined.
	 * @param integer|bool $order optional accumulating order (0 - from child to parent, 1 - "natural" order, from parent to child).
	 * @param integer|bool $uniqueValues optional specifies whether to return only unique accumulated values
	 * 
	 * @return array accumulated value.
	 */
	public static function getAccumulated($name, array $args = [], $order = 1, $uniqueValues = 0)
	{
		$accumulated = $order ? 
		array_merge(self::getListLegacy($name, $args, $order), self::getListIncrement($name, $args)) :
		array_merge(self::getListIncrement($name, $args), self::getListLegacy($name, $args, $order));

		if ($uniqueValues) {
			$accumulated = array_unique($accumulated);
		}

		return $accumulated;
	}

	/**
	 * Returns value accumulated till parent of current context class. 
	 * Recursive invocation of PropertyValueAccumulation::getAccumulated() happens in this method.
	 * 
	 * @access private   
	 * @see PropertyValueAccumulation::getAccumulated() all arguments are passed from there.
	 * 
	 * @param string $name name of the property whose accumulated value this method returns.
	 * @param array $args optional arguments that will be passed to getter method if such is defined.
	 * @param integer|bool $order optional accumulating order (0 - from child to parent, 1 - "natural" order, from parent to child).
	 * 
	 * @return array accumulated value.
	 */
	private static function getListLegacy($name, array $args = [], $order = 1)
	{
		$parent = get_parent_class(get_called_class());
		return ($parent !== false && method_exists($parent, 'getAccumulated')) ? 
			call_user_func_array([$parent, 'getAccumulated'], [$name, $args, $order]) :
			[];
	}

	/**
	 * Returns value of specified property in current context (using late static binding).
	 * 
	 * @access private   
	 * @see PropertyValueAccumulation::getAccumulated() all arguments are passed from there.
	 * 
	 * @param string $name name of the property whose accumulated value this method returns.
	 * @param array $args optional arguments that will be passed to getter method if such is defined.
	 * 
	 * @return mixed current value.
	 */
	private static function getListIncrement($name, array $args = [])
	{	
		$class = get_called_class();
		$getter = 'get'.ucfirst($name);
		$result = [];

		if (method_exists($class, $getter)) {
			$result = call_user_func_array([$class, $getter], $args);
		} else if (property_exists($class, $name)) {
			$result = $class::$$name;
		}
		return is_array($result) ? $result : [$result];
	}
}
