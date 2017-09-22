**How to use**

1) Include 'Overloadable.class.php' in your project.
1) Create Class i.e: Test.
2) Put this method into it:
```
function __call($method, $args) {
	return Overloadable::call($this, $method, $args);
}
```
3) Add overloading methods with same name except prefix __ and args letters i.e:
 - my_func__()
 - my_func__i(int)
 - my_func__is(int, str)
 - my_func__ao(array, obj)
 - my_func__dc(double, closure)

Args symbols:

- i: Integer
- d: double
- s: String
- b: Boolean
- a: Array
- o: Object
- c: Closure
- r: Resource
- n: NULL

Now your class would be like this:
```
class test 
{
	private $name = 'test-1';
	
	// Call Overloadable class 
	// you must copy this method in your class to activate overloading
	function __call($method, $args) {
		return Overloadable::call($this, $method, $args);
	}
	
	// func(closure)
	// New in version 3.8
	function func__c(Closure $callback) {
		$r = pre("func__c(".print_r($callback, 1).");", 'print_r(Closure)', false)."\n";
		$r .= $callback($this->name);
		return $r;
	}
	
	// func(NULL)
	function func__n($null){
		return '[[ NULL ]]';
	}
	
    // myFunction(void)
    function myFunction__() {
        return 'myFunction(void)';
    }
	
    // myFunction(integer)
    function myFunction__i($int) {
        return 'myFunction(integer='.$int.')';
    }
    // myFunction(string)
    function myFunction__s($string) {
        return 'myFunction(string='.$string.')';
    }    
    // myFunction(string)
    function myFunction__so($string, $object) {
        $r = 'myFunction(string='.$string.', object='.get_class($object).')';
		$object->x++;
        $r .= '<pre>Object: ';
        $r .= print_r($object, true);
        $r .= '</pre>';
		return $r;
    }
    // anotherFunction(array)
    function anotherFunction__a($array) {
        $r = 'anotherFunction('.print_r($array, 1).')'."\n";
        $array[0]++;        // change the reference value
        $array['val']++;    // change the reference value
		$r .= 'array[0]='.$array[0]."\n";
		$r .= 'array[val]='.$array['val']."\n";
		return $r;
    }
    // anotherFunction(string, integer)
    function anotherFunction__si($key, $value) {
        $r = 'anotherFunction(string='.$key.', integer='.$value.')';
        // Get a reference
        $a2 =& Overloadable::refAccess($key); // => $a2 =& $GLOBALS['val'];
        $a2 *= 3;   // change the reference value
		$r .= "\nkey=$key, value=$value ($$key * 3) => $$key=$a2;";
		return $r;
    }

}
```
The function "pre" used for formatting outputs:
```
function pre($mixed, $title=null, $print=true){
	$output = "";
	if(empty($mixed)){$output .= "<div><h3>-->Empty $title<--</h3></div>";
		if($print){echo $output; return;}else{return $output;}
	}
	$output .= "<fieldset>";
	if($title){$output .= "<legend><h2>$title</h2></legend>";}
	$output .= '<pre>'. print_r($mixed, true) .'</pre>';
	$output .= "</fieldset>";
	if($print){echo $output;}else{return $output;}
}
```
Now how to use your class "Test class"
```

<?php
// Some data to work with:
$val  = 10;
class obj {
    public $x=10;
}
//----------------------------------------------------------
// Start
$t = new test;

// Call first method with no args:
pre( $t->myFunction() , 'myFunction()'); 
// Output: myFunction(void)

pre( $t->func(NULL) , 'Null');
//Output: [[ NULL ]]

pre( $t->myFunction($val), 'myFunction__i');
// Output: myFunction(integer=10)

pre( $t->myFunction("hello"), 'myFunction__s');
// Output: myFunction(string=hello)

pre( $t->myFunction("str", new obj()),  'myFunction__so');
/* Output: 
myFunction(string=str, object=obj)
Object: obj Object
(
    [x] => 11
)
*/

```
**## Passing by Reference:**


```
pre( '$val='.$val,  '$val' );
// Output: $val=10

pre( $t->anotherFunction(array(&$val, 'val'=>&$val)), 'anotherFunction__a' );
// Output: 	anotherFunction(Array ( [0] => 10 [val] => 10 ) )
//			array[0]=12
//			array[val]=12

pre( '$val='.$val, '$val' );
// Output: $val=12

pre( $t->anotherFunction('val', $val), 'anotherFunction__si' );
// Output: 	anotherFunction(string=val, integer=12)
// 			key=val, value=12 ($val * 3) => $val=36;

pre('$val='.$val, '$val');
// Output: $val=36

```
**Closure**
```
// Closure example
$f = $t->func( function($n){ return strtoupper($n); } );
/*Output: print_r(Closure)
func__c(Closure Object
(
    [parameter] => Array
        (
            [$n] => 
        )

)
);*/

pre( $f, 'Closure' );
//Output: TEST-1
```


 
