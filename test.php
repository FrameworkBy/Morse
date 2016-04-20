<?php
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');
include_once 'MorseCodeConverter.php';

$MorseCodeConverter = new MorseCodeConverter('mor');

$br ="<br>\n";
$errors = 0;
$errorList = '';
$isError = false;
$filePath = dirname(__FILE__) . "/tablemorze.txt";
$allTestsCntExist = -1;
$handle = fopen($filePath, 'r') OR die("fail open 'tablemorze.txt'");
if ($handle) {
    $var = 0;
    while (($buffer = fgets($handle, 4096)) !== false) {
        $allTestsCntExist++;
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
//echo "size = ", count($arrMorse), $br;
for ($i = 1; $i < count($arrText); $i++) {
    $isError = false;
    $result = '';
    $newArrResult = '';
    $newArrMorse='';
    $morseHave = '';
    $morseShouldBe = '';
    $coincidedPart = '';
    $wordHave = '';
    $arrTextdeb = preg_split('//u', $arrText[$i], -1, PREG_SPLIT_NO_EMPTY);
    $elementsMorse = explode(" ", $arrMorse[$i]);

    if (count($elementsMorse) != count($arrTextdeb))
    {
      if (count($elementsMorse) < count($arrTextdeb)){
          $temp = count($elementsMorse);
      }else {
          $temp = count($arrTextdeb);
      }
    } else $temp = count($elementsMorse);

    for ($j = 0; $j < $temp; $j++)
    {
        $MorseCodeConverter->setText($arrTextdeb[$j]);
        $result = $MorseCodeConverter->run();
        $newArrResult = mb_substr($result, 0, mb_strlen($result), "utf-8");
        $newArrMorse = mb_substr($elementsMorse[$j], 0, mb_strlen($elementsMorse[$j]), "utf-8");
        if (strcmp($newArrMorse, $newArrResult) != 0)
        {
            $charactersMismatchIndex = $j;
            $isError = true;
            $errors++;
            break;
        }
    }

    if ($isError == true)
    {
        $wordHave = $arrText[$i];
        $mismatchSymbol =mb_substr($wordHave, $charactersMismatchIndex, 1, "utf-8");
        $startPart = mb_substr($wordHave, 0, $charactersMismatchIndex,"utf-8");
        $endPart = mb_substr($wordHave, $charactersMismatchIndex + 1, mb_strlen($wordHave), "utf-8");
        $mismatchSymbol = "<font color='red'>$mismatchSymbol</font>";
        $wordHave = $startPart . $mismatchSymbol . $endPart;
        for ($k = 0; $k < $charactersMismatchIndex; $k++) {
            $coincidedPart .= mb_substr($arrText[$i], $k, 1, "utf-8");
        }

        $morseShouldBe = $arrMorse[$i];
        foreach ($arrTextdeb as $temptxt)
        {
            $MorseCodeConverter->setText($temptxt);
            $morseHave .= $MorseCodeConverter->run();
            $morseHave .= ' ';
        }

        $errorList .= "Error(line № $i)" . $br;
        $errorList .= "Word in: $wordHave" . $br;
        $errorList .= "Coincided part: $coincidedPart" . $br;
        $errorList .= "Must be : $morseShouldBe" . $br;
        $errorList .= "Now out: $morseHave" . $br . $br . $br;
    }
}
$allTestsCnt = $i - 1;
$resultStatistics .= "Усяго тэстаў: <b>$allTestsCntExist</b>.$br";
$successfulTestsCnt = $allTestsCnt - $errors;
$successfulTestsPercentage = round($successfulTestsCnt / $allTestsCnt * 100, 2);
$percentageOfTests = round($allTestsCnt / $allTestsCntExist * 100, 2);
$resultStatistics .= "Актываваных тэстаў: <b>$percentageOfTests % ($allTestsCnt з $allTestsCntExist)</b>.$br";
$resultStatistics .= "Сярод актываваных тэстаў паспяхова пройдзена: <b>$successfulTestsPercentage % ($successfulTestsCnt з $allTestsCnt)</b>.$br$br$br";
$resultStatistics .= $errorList;
echo $resultStatistics;

?>