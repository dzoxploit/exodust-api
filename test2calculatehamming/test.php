<?PHP

function hammingDistance($n1, $n2)
{
    $x = $n1 ^ $n2;
    $setBits = 0;
 
    while ($x > 0)
    {
        $setBits += $x & 1;
        $x >>= 1;
    }
 
    return $setBits;
}


 
// Driver code
$x = 1;
$y = 4;
echo("the output is ".hammingDistance($x, $y));

$format = '(%1$2d = %1$04b)';

$test = 1 + 4;

echo "<br/>";

$result = $x & $test;
printf($format, $result, $x, '&', $test);

echo "<br/>";

$result2 = $y & $test;
printf($format, $result2, $y, '&', $test);


?>