<?php

require 'overloadable.class.php';

//----------------------------------------------------------
#> 2. create new class
//----------------------------------------------------------

class test 
{
    private $name = 'test-1';

    #> 3. Add __call 'magic method' to your class

    // Call Overloadable class 
    // you must copy this method in your class to activate overloading
    function __call($method, $args) {
        return Overloadable::call($this, $method, $args);
    }

    #> 4. Add your methods with __ and arg type as one letter ie:(__i, __s, __is) and so on.
    #> methodname__i = methodname($integer)
    #> methodname__s = methodname($string)
    #> methodname__is = methodname($integer, $string)

    // func(void)
    function func__() {
        pre('func(void)', __function__);
    }
    // func(integer)
    function func__i($int) {
        pre('func(integer '.$int.')', __function__);
    }
    // func(string)
    function func__s($string) {
        pre('func(string '.$string.')', __function__);
    }    
    // func(string, object)
    function func__so($string, $object) {
        pre('func(string '.$string.', '.print_r($object, 1).')', __function__);
        //pre($object, 'Object: ');
    }
    // func(closure)
    function func__c(Closure $callback) {

        pre("func(".
            print_r(
                array( $callback, $callback($this->name) ), 
                1
            ).");", __function__.'(Closure)'
        );

    }   
    // anotherFunction(array)
    function anotherFunction__a($array) {
        pre('anotherFunction('.print_r($array, 1).')', __function__);
        $array[0]++;        // change the reference value
        $array['val']++;    // change the reference value
    }
    // anotherFunction(string)
    function anotherFunction__s($key) {
        pre('anotherFunction(string '.$key.')', __function__);
        // Get a reference
        $a2 =& Overloadable::refAccess($key); // $a2 =& $GLOBALS['val'];
        $a2 *= 3;   // change the reference value
    }

}

//----------------------------------------------------------
// Some data to work with:
$val  = 10;
class obj {
    private $x=10;
}

//----------------------------------------------------------
#> 5. create your object

// Start
$t = new test;

#> 6. Call your method

// Call first method with no args:
$t->func(); 
// Output: func(void)

$t->func($val);
// Output: func(integer 10)

$t->func("hello");
// Output: func(string hello)

$t->func("str", new obj());
/* Output: 
func(string str, obj Object
(
    [x:obj:private] => 10
)
)
*/

// call method with closure function
$t->func(function($n){
    return strtoupper($n);
});

/* Output:
func(Array
(
    [0] => Closure Object
        (
            [parameter] => Array
                (
                    [$n] => 
                )

        )

    [1] => TEST-1
)
);
*/

## Passing by Reference:

echo '<br><br>$val='.$val;
// Output: $val=10

$t->anotherFunction(array(&$val, 'val'=>&$val));
/* Output:
anotherFunction(Array
(
    [0] => 10
    [val] => 10
)
)
*/

echo 'Result: $val='.$val;
// Output: $val=12

$t->anotherFunction('val');
// Output: anotherFunction(string val)

echo 'Result: $val='.$val;
// Output: $val=36







// Helper function
//----------------------------------------------------------
function pre($mixed, $title=null){
    $output = "<fieldset>";
    $output .= $title ? "<legend><h2>$title</h2></legend>" : "";
    $output .= '<pre>'. print_r($mixed, 1). '</pre>';
    $output .= "</fieldset>";
    echo $output;
}
//----------------------------------------------------------
