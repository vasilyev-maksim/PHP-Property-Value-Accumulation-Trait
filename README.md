# Accumulated property value retrieve functionality providing trait on PHP
This trait provides an ability to get static property value accumulated from the same properties of parent classes.

*PropertyValueAccumulation::getAccumulated()* is the only public method to use.
```
array getAccumulated(string $name [, array $args = [] [,int $order = 1 [,int $uniqueValues = 0]]])
```
If property's getter method is implemented *getAccumulated* method will use it to get poperty's value.
*$args* array will be passed as arguments to the getter method.
You can change accumulating order passing 1 (straight) or 0 (reverse) third argument.
Also you can specify whether to accumulate only unique values passing 1 as fourth argument.

## Example
```
class A{
	use PropertyValueAccumulation;

	protected static $arrayProp = ['a','b','c'];
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

print_r(C::getAccumulated('arrayProp', [], 1, 1));
print_r(C::getAccumulated('strProp', [], 0));
print_r(C::getAccumulated('arrayPropWithGetter', ['test'], 1));
```
### Output:
```
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
```
You can find the same code in *test.php* file.