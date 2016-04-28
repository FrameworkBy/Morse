<html>
<head>
    <title>Morse Code Converter/Канвертар Азбукі Морзэ</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" media="all" href="../css/general_style.css"/>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,300,600,700&subset=latin,cyrillic' rel='stylesheet' type='text/css' />
    <script type="text/javascript" src="../sortTable.js"></script>
</head>

<style>
    table.sort {
        border-spacing:0.1em;
        margin-bottom:1em;
        margin-top:1em
    }
    table.sort td {
        border:1px solid #CCCCCC;
        padding:0.1em 1em
    }
    table.sort thead td {
        cursor:pointer;
        cursor:hand;
        font-weight:bold;
        text-align:center;
        vertical-align:middle
    }
    table.sort thead td.curcol {
        background-color:#3399CC;
        color:#FFFFFF
    }
    table.sort a {
        text-decoration: none
    }
</style>

<?php
include_once 'MorseCodeConverter.php';
//include_once '../analyticstracking.php';
?>

<script language="javascript">
    var input_text_default = "<?php echo str_replace("\"", '\"', str_replace("\n", '\\n', MorseCodeConverter::INPUT_TEXT_DEFAULT)); ?>";
</script>

<body>
<div class="global">
    <div class="header">
        <h1 class="main-caption">Морзе канвэртар</h1>
    </div>

    <form enctype="multipart/form-data" method="post" action="">
	 <?php
     if(isset($_POST['mainButton'])) {
         if (isset($_POST['text']) && isset($_POST['language'])) {
             $MorseCodeConverter = new MorseCodeConverter($_POST['language']);
            // $result = $MorseCodeConverter->start($_POST['text']);
             list ($result, $unknown) = $MorseCodeConverter->start($_POST['text']);

         }
     }
     if (isset($_POST['TestButton'])){
         $MorseCodeConverter = new MorseCodeConverter('mor');
         if ($MorseCodeConverter->AutoTest() == True){
             $testResult = '<font size = "5" color = "green">';
             $testResult .= 'Адкалібравана';
             $testResult .= "</font>";

         } else {
             $testResult = '<font size = "5" color = "red">Не адкалібравана</font>';
         }

     }
        ?>


<table width="100%">
            <tr>
                <td width="90%">
                    <h2 class="sub-caption-smaller"><!--Please input a text <br />-->Калі ласка, увядзіце тэкст</h2>
                </td>
                <td width="5%" align="right">
                    <input type="submit" class="symbol-button" value='&#8634;' onclick="document.getElementById('input_text_id').value=input_text_default; document.getElementById('output_text_id').value='';">
                </td>
                <td width="5%" align="right">
                    <input type="submit" class="symbol-button" value='x' onclick="document.getElementById('input_text_id').value=''; document.getElementById('output_text_id').value='';">
                </td>
            </tr>
      
        <tr>
			<td colspan = 3>
				<textarea id="input_text_id" name="text" class="main-textarea"><?php
                    if(isset($_POST['text']))
                    {
                        echo $_POST['text'];
                    }
                    else
                    {
                        echo MorseCodeConverter::INPUT_TEXT_DEFAULT;
                    }
                    ?></textarea>

			</td>
			</tr>

		

        
		
        <tr>
		  <td >

       
        <select name="language"  >
            <option  value="mor"<?php if(isset($_POST['language'])) echo($_POST['language'] == 'mor') ? 'selected' : ''; ?>>Морзе</option>
            <option value="rus"<?php if(isset($_POST['language'])) echo($_POST['language'] == 'rus') ? 'selected' : ''; ?>>Русский</option>
            <option value="bel"<?php if(isset($_POST['language'])) echo($_POST['language'] == 'bel') ? 'selected' : ''; ?>>Беларуская</option>
            <option value="eng"<?php if(isset($_POST['language'])) echo($_POST['language'] == 'eng') ? 'selected' : ''; ?>>English</option>
        </select>
            <input type="submit" name = "mainButton" value="Канвертаваць" class="blue-button">
            <input type="checkbox" name="loga"  value="on" <?php if(isset($_POST['loga'])) echo($_POST['loga'] == 'on') ? 'checked' : ''; ?>> Show log information
		  </td>

          
        </tr>

        
       
         

       
    <tr>
   
            <td >
            <?php
                if (isset($_POST['mainButton']) && file_exists('cache/out/' . $MorseCodeConverter->getFilePath())) {
                    ?>
        <h2 class="sub-caption-smaller">Listen to the generated speech</h2>

        
        
        <p>
        <audio controls>
        <source src=<?php echo 'cache/out/' . $MorseCodeConverter->getFilePath(); ?> type="audio/wav"/>
        Your browser does not support the audio element.
        </audio>
            <p>or <a type="audio/wav" href=<?php echo 'cache/out/' . $MorseCodeConverter->getFilePath(); ?> download> download the generated speech file.</a></p>
            </p>
 <?php
                }
                ?>
        
            </td>
    </tr>

   

    <tr>
            
             <?php
            if($_POST['loga'] == 'on'){

            
            ?>
            
             <tr>
            <?php 
            if (isset($_POST['mainButton'])) {
               ?>
            <td colspan = 3>
                <textarea id="output_text_id" name="morze" class="main-textarea" readonly><?php
                    if(isset($_POST['language']) && $_POST['language'] == 'bel' && !empty($result)) echo  $result;
                    if(isset($_POST['language']) && $_POST['language'] == 'rus' && !empty($result)) echo  $result;
                    if(isset($_POST['language']) && $_POST['language'] == 'eng' && !empty($result)) echo  $result;
                    if(isset($_POST['language']) && $_POST['language'] == 'mor' && !empty($result)) echo  $result;
                    ?></textarea>
                    
                    
            </td>
            <?php
        }
            ?>
        </tr>
        <tr> <td><input type="submit" name = "TestButton" class="blue-button" value='Адкалібраваць';> </td></tr>
        <tr><td><?php echo $unknown;?> </td></tr>
        <tr>  <td> 
        <?php
        echo $testResult;
        }
        ?><td></tr>

        
    </tr>
    

        <tr>
            <td>
                
            </td>
        </tr>

        <br /><br /><br /><br />
</table>
       
       
    </form>

    <div class="divider"></div>
    <h2 class="sub-caption-smaller">Our other prototypes are here: <a href="../">www.corpus.by</a>, <a href="http://ssrlab.by">www.ssrlab.by</a>.</h2>
    <div class="footer">
        <span>&copy; SSRLab, UIIP NAS Belarus, 2016</span>
    </div>
    <br />
</div>

</body>
</html>