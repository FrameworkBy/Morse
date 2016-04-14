<?php
include_once 'MorseCodeConverter.php';

$MorseCodeConverter = new MorseCodeConverter('mor');
$errors = 0;
$iserror = false;
$filePath = dirname(__FILE__) . "/tablemorze.txt";
$handle = fopen($filePath, 'r') OR die("fail open 'tablemorze.txt'");
if ($handle) {
    $var = 0;
    while (($buffer = fgets($handle, 4096)) !== false) {

        if (substr($buffer, 0, 1) != "#") {
            $symbol_str = preg_split("/\t/", $buffer);
            $arrText[$var] = $symbol_str[0];
            $arrMorse[$var] = $symbol_str[1];
            $var++;
        }
    }
}
fclose($handle);
if (count($arrText) != count($arrMorse)) {
    echo "Different size";
    return;
}
//echo "size = ", count($arrMorse), "<br>";
for ($i = 1; $i < count($arrText); $i++) {
    $result = '';
    $newArrResult = '';
    $newArrMorse='';
    $arrTextdeb = preg_split('//u', $arrText[$i], -1, PREG_SPLIT_NO_EMPTY);
    foreach ($arrTextdeb as $ar) {
        $MorseCodeConverter->setText($ar);
        $result .= $MorseCodeConverter->run();
        $result .= ' ';
    }
    $newArrMorse = mb_substr($arrMorse[$i], 0, mb_strlen($arrMorse[$i]), "utf-8");
    $newArrResult = mb_substr($result, 0, mb_strlen($result) - 1, "utf-8");

    if (strcmp($newArrMorse, $newArrResult) != 0)
    {
        $errors++;
        $iserror = true;
        echo "Error: ";;
        foreach($arrTextdeb as $txt)
            echo $txt;
        echo "<br>";
    }
}
if ($iserror == true)
{
    echo "Number of errors: ", $errors, "<br>";
}


?>