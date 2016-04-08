﻿<?php
class MorseCodeConverter
{
    private $language = '';
    private $text = '';
    private $arr = array();
    private $filename = '';
    //const BR = "<br />\n";
    const INPUT_TEXT_DEFAULT = "Вас вітае канвертар коду Морзэ!";



    function __construct($language)
    {
        $this->language = $language;
        $this->readTable();
        mb_internal_encoding("UTF-8");
        mb_regex_encoding("UTF-8");
        $date_code = date('Y-m-d_H-i-s', time());
        $rand_code = rand(0, 1000);
        $ip = str_replace('.', '-', $_SERVER["REMOTE_ADDR"]);
        $path = dirname(__FILE__) . '/cache/';
        if(!file_exists($path)) mkdir($path);
        $path = dirname(__FILE__) . '/cache/in/';
        if(!file_exists($path)) mkdir($path);
        $path = dirname(__FILE__) . '/cache/out/';
        if(!file_exists($path)) mkdir($path);
        $path = dirname(__FILE__) . '/cache/email/';
        if(!file_exists($path)) mkdir($path);
        $this->filename = $date_code . '_'. $ip . '_' . $rand_code . '_out.wav';
    }

    private function readTable()
    {
        switch ($this->language){
            case 'rus':
                $filePath = dirname(__FILE__) . "/table_to_code_from_rus.txt";
                $handle = fopen($filePath, 'r') OR die("fail open 'table_to_code_from_rus.txt'");
                break;
            case 'eng':
                $filePath = dirname(__FILE__) . "/table_to_code_from_eng.txt";
                $handle = fopen($filePath, 'r') OR die("fail open 'table_to_code_from_eng.txt'");
                break;
            case 'bel':
                $filePath = dirname(__FILE__) . "/table_to_code_from_bel.txt";
                $handle = fopen($filePath, 'r') OR die("fail open 'table_to_code_from_bel.txt'");
                break;
            case 'mor':
                $filePath = dirname(__FILE__) . "/table_to_code_to_morze.txt";
                $handle = fopen($filePath, 'r') OR die("fail open 'table_to_code_to_morze.txt'");
                break;
        }

        if ($handle)
        {
            while (($buffer = fgets($handle, 4096)) !== false)
            {
                if(substr($buffer, 0, 1) != "#")
                {
                    $symbol_str = preg_split("/\t/", $buffer);
                    if ($this->language == 'mor'){
                        $this->arr[$symbol_str[2]] = $symbol_str[7];
                    } else {
						$this->arr[$symbol_str[7]] = $symbol_str[3];
					}
                }
            }
        }
        fclose($handle);
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function start($txt)
    {
        $result = '';
        if ($_POST['language'] == 'mor') {
            $newText = preg_split('//u', $_POST['text'], -1, PREG_SPLIT_NO_EMPTY);
            //print_r($newText);
            foreach ($newText as $lng) {
                if ($lng == ' ') {
                    continue;
                }
                $this->setText($lng);
                $result .= $this->run();
                $result.= ' ';
            }
            unset($lng);
        }
        else
        {
            $pieces = explode(" ", $_POST['text']);
            for ($i = 0; $i < count($pieces); $i++){
                $this->setText($pieces[$i]);
                $result.= $this->run();
            }
        }

        if ($_POST['language'] == 'mor')
        {
            $this->generateAudio($result);
        }

        return $result;
    }

    private function run()
	{
		if ($this->language != 'mor') {
			if(isset($this->arr[$this->text]))
			{
				return $this->arr[$this->text];
			}
			else
			{
				return '';
			}
			
		} else {
			if(isset($this->arr[$this->ordutf8($this->text)]))
			{
				return $this->arr[$this->ordutf8($this->text)];
			}
			else
			{
				return '';
			}
		}
    }


	private function ordutf8($string)
		{
			$offset = 0;
			$code = ord(substr($string, $offset, 1));
			if($code >= 128)											//otherwise 0xxxxxxx
			{
				if($code < 224) $bytesnumber = 2;						//110xxxxx
				elseif($code < 240) $bytesnumber = 3;					//1110xxxx
				elseif($code < 248) $bytesnumber = 4;					//11110xxx
				$codetemp = $code - 192 - ($bytesnumber > 2 ? 32 : 0) - ($bytesnumber > 3 ? 16 : 0);
				for ($i = 2; $i <= $bytesnumber; $i++)
				{
					$offset++;
					$code2 = ord(substr($string, $offset, 1)) - 128;	//10xxxxxx
					$codetemp = $codetemp*64 + $code2;
				}
				$code = $codetemp;
			}
			$offset += 1;
			if($offset >= strlen($string)) $offset = -1;
			$codehex = strtoupper(dechex($code));
			if(strlen($codehex) == 1) return "U+000$codehex";
			elseif(strlen($codehex) == 2) return "U+00$codehex";
			elseif(strlen($codehex) == 3) return "U+0$codehex";
			elseif(strlen($codehex) == 4) return "U+$codehex";
			else return $codehex;
		}

    private function generateAudio($text)
    {
        $tsize = 0;
		if(isset($text))
        {
            $filepath = dirname(__FILE__) . '/cache/out/' . $this->filename;
            $newFile = fopen($filepath, 'wb') OR die('fail open test.wav');
            fseek($newFile, 44);
            $newText = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);

            foreach($newText as $lng)
            {
                if($lng == '*') {
                    $wavFilepath = dirname(__FILE__) . "/base/dot.wav";
                }
                elseif ($lng == '-') {
                    $wavFilepath = dirname(__FILE__) . "/base/dash.wav";
                }
                elseif ($lng == ' ') {
                    $wavFilepath = dirname(__FILE__) . "/base/silence600.wav";
                }
                else {
                    echo '999', $lng, '<br>';
                    continue;
                }
                $fp = fopen($wavFilepath, 'rb');
                fseek($fp, 16);
                $sizeChunk1 = unpack('Vsize', fread($fp,4));
                fseek($fp, 24 + $sizeChunk1['size']);
                $size = unpack('Vsize', fread($fp,4));
                $tsize += $size['size'];
                $data = fread($fp, $size['size']);
                fwrite($newFile, $data);
                fclose($fp);

                if ($lng != ' ') {
                    $wavFilepath = dirname(__FILE__) . "/base/silence450.wav";
                    $fp = fopen($wavFilepath, 'rb');
                    fseek($fp, 16);
                    $sizeChunk1 = unpack('Vsize', fread($fp,4));
                    fseek($fp, 24 + $sizeChunk1['size']);
                    $size = unpack('Vsize', fread($fp,4));
                    $tsize += $size['size'];
                    $data = fread($fp, $size['size']);
                    fwrite($newFile, $data);
                    fclose($fp);
                }
            }
        }

            $wav = fopen($wavFilepath, 'rb');
            $header = fread($wav, 40);
            $header .= pack('V',$tsize);
            fseek($newFile, 0);
            fwrite($newFile, $header);
            fseek($newFile, 4);
            fwrite($newFile, pack('V',$tsize+36));
            fclose($newFile);
            fclose($wav);
        }


    public function getFilePath()
    {
        return $this->filename;
    }

    public function startAutoTest()
    {
        //$this->language = 'mor';
        $StringTxt = file_get_contents('./text2.txt', True);
        $StringMorze = file_get_contents('./morze2.txt', True);
        $result = '';

        $newText = preg_split('//u', $StringTxt, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($newText as $lng) {
            if ($lng == ' ') {
                continue;
            }
            $this->setText($lng);
            $result .= $this->run();
            $result.= ' ';
        }
        unset($lng);
        $resultLength = strlen($result);
        $morzeLength = strlen($StringMorze);
        $arrResult = mb_substr($result, 1, $resultLength, "utf-8");
        $arrMorze = mb_substr($StringMorze, 1, $morzeLength, "utf-8");
        $temp = strcmp($arrResult, $arrMorze);
        //echo $temp;
        if ($temp == 0){
            return true;
        }else return false;
        //echo $result, ' ', mb_strlen($result), "<br>";
        //echo $StringMorze, ' ', mb_strlen($StringMorze), "<br>";
    }
}
?>