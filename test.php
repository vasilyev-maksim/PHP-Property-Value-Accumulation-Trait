<?php
require ('PropertyValueAccumulation.php');

class A{
	use PropertyValueAccumulation;

	protected static $arrayProp = ['a','b'];
	protected static $arrayPropWithGetter = [1,2];
	protected static $strProp = 'x';

	protected static function getArrayPropWithGetter($arg)
	{
		return array_map(function($item) use ($arg) {
			return $item.'_'.$arg;
		}, static::$arrayPropWithGetter);
	}
}

class B extends A{
	protected static $arrayProp = ['c','d'];
	protected static $arrayPropWithGetter = [3,4];
	protected static $strProp = 'y';
}

class C extends B{
	protected static $arrayProp = ['e','f'];
	protected static $arrayPropWithGetter = [5,6];
	protected static $strProp = 'z';

	protected static function getArrayPropWithGetter($arg)
	{
		return array_map(function($item) use ($arg) {
			return $item.'_'.strtoupper($arg);
		}, A::getArrayPropWithGetter($arg));
	}
}

echo "<pre>";
print_r(C::getAccumulated('arrayProp'));
print_r(C::getAccumulated('strProp',0));
print_r(C::getAccumulated('arrayPropWithGetter',1,['test']));

/*
---------------------------
Output:

Array
(
   [0] => a
   [1] => b
   [2] => c
   [3] => d
   [4] => e
   [5] => f
)
Array
(
   [0] => z
   [1] => y
   [2] => x
)
Array
(
   [0] => 1_test
   [1] => 2_test
   [2] => 3_test
   [3] => 4_test
   [4] => 1_test_TEST
   [5] => 2_test_TEST
)
*/
