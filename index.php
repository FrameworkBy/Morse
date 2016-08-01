<html>
<head>
    <title>Morse Code Converter / Канвертар Азбукі Морзэ</title>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" media="all" href="../css/general_style.css"/>
    <link
        href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,300,600,700&subset=latin,cyrillic'
        rel='stylesheet' type='text/css'/>
    <script type="text/javascript" src="../sortTable.js"></script>
    <script src="http://code.jquery.com/jquery-latest.js"></script>

</head>

<?php
include_once 'MorseCodeConverter.php';
include_once '../analyticstracking.php';
?>

<script language="javascript">
    var input_text_default = "<?php echo str_replace("\"", '\"', str_replace("\n", '\\n', MorseCodeConverter::INPUT_TEXT_DEFAULT)); ?>";
</script>

<script language="JavaScript">
    audio_option = 'html5'; //вариант аудиоплеера: html5, flash
    $(document).ready(function () {
        try {
            if ($('#player audio')[0].canPlayType('audio/wav') != 'maybe') //тег audio не сработал или не поддерживает формат
            {
                audio_option = 'flash';
                $('#player embed').appendTo('#player');
                $('#player audio').remove();
            }
        }
        catch (error) {
            // console.log(error);
            audio_option = 'flash';
        }
        $('button#TtsButton').click(function () {
            $.ajax({
                type: "POST",
                url: "http://corpus.by/tts3/api.php",
                data: {
                    'text': $('textarea#output_text_id').val(),
                    'lang': $('select#language').val()
                },
                success: function (msg) {
                    msg = msg.replace(String.fromCharCode(65279), "");
                    var result = jQuery.parseJSON(msg);
                    alert(result);
                    audio_url = result.audio;
                    $('#player').css('opacity', 1.0);
                    switch (audio_option) {
                        case 'html5':
                            $('#player audio source').remove();
                            $('#player audio').append(
                                $('<source>', {
                                    src: audio_url,
                                    type: "audio/wav"
                                })
                            );
                            $('#player audio').load();
                            break;
                        case 'flash':
                            $('#player embed').replaceWith($('<embed>', {
                                src: "wavplayer.swf?gui=mini&h=20&w=300&sound=" + audio_url,
                                bgcolor: "#ffffff",
                                width: "40",
                                height: "40",
                                allowScriptAccess: "always",
                                type: "application/x-shockwave-flash",
                                pluginspage: "http://www.macromedia.com/go/getflashplayer"
                            }));
                            break;
                    }

                    $('#audioFileLink').replaceWith(
                        $('<a>', {
                            href: audio_url,
                            html: "Download"
                        })
                    );

                },
                error: function () {
                    $('#player').html('Произошла ошибка, попробуйте еще раз.');
                }
            });
        });
    });

</script>

<body>
<div class="global">
    <div class="header">
        <h1 class="main-caption">Канвертар азбукі Морзэ</h1>
    </div>
    <form enctype="multipart/form-data" method="post" action="">
        <?php
        if (isset($_POST['mainButton'])) {
            if (isset($_POST['text']) && isset($_POST['language'])) {
                $MorseCodeConverter = new MorseCodeConverter($_POST['language'], $_POST['speed']);
                $MorseCodeConverter = new MorseCodeConverter($_POST['language'], $_POST['speed']);
                list ($result, $unknown) = $MorseCodeConverter->start($_POST['text']);
            }
        }
        if (isset($_POST['TestButton'])) {
            $MorseCodeConverter = new MorseCodeConverter('mor', 'medium');
            if ($MorseCodeConverter->AutoTest() == True) {
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
                    <input type="submit" class="symbol-button" value='&#8634;'
                           onclick="document.getElementById('input_text_id').value=input_text_default; document.getElementById('output_text_id').value='';">
                </td>
                <td width="5%" align="right">
                    <input type="submit" class="symbol-button" value='x'
                           onclick="document.getElementById('input_text_id').value=''; document.getElementById('output_text_id').value='';">
                </td>
            </tr>
            <tr>
                <td colspan=3>
						<textarea id="input_text_id" name="text" class="main-textarea"><?php
                            if (isset($_POST['text'])) {
                                echo $_POST['text'];
                            } else {
                                echo MorseCodeConverter::INPUT_TEXT_DEFAULT;
                            }
                            ?></textarea>
                </td>
            </tr>
            <tr>
                <td>
                    <select name="language" id = "language">
                        <option
                            value="mor"<?php if (isset($_POST['language'])) echo ($_POST['language'] == 'mor') ? 'selected' : ''; ?>>
                            Морзе
                        </option>
                        <option
                            value="rus"<?php if (isset($_POST['language'])) echo ($_POST['language'] == 'rus') ? 'selected' : ''; ?>>
                            Русский
                        </option>
                        <option
                            value="bel"<?php if (isset($_POST['language'])) echo ($_POST['language'] == 'bel') ? 'selected' : ''; ?>>
                            Беларуская
                        </option>
                        <option
                            value="eng"<?php if (isset($_POST['language'])) echo ($_POST['language'] == 'eng') ? 'selected' : ''; ?>>
                            English
                        </option>
                    </select>
                    <select name="speed">
                        <option
                            value="slow"<?php echo (isset($_POST['speed']) && $_POST['speed'] == 'slow') ? 'selected' : ''; ?>>
                            Slow
                        </option>
                        <option
                            value="medium"<?php echo (isset($_POST['speed']) && $_POST['speed'] == 'medium' || !isset($_POST['speed'])) ? 'selected' : ''; ?>>
                            Medium
                        </option>
                        <option
                            value="high"<?php echo (isset($_POST['speed']) && $_POST['speed'] == 'high') ? 'selected' : ''; ?>>
                            High
                        </option>
                    </select>
                    <input type="submit" name="mainButton" value="Канвертаваць" class="blue-button">
                    <input type="checkbox" name="loga"
                           value="on" <?php if (isset($_POST['loga'])) echo ($_POST['loga'] == 'on') ? 'checked' : ''; ?>>
                    Show log information
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    if (isset($_POST['mainButton']) && file_exists('cache/out/' . $MorseCodeConverter->getFilePath())) {
                        ?>
                        <h2 class="sub-caption-smaller">Listen to the generated speech</h2>
                        <p>
                            <audio controls>
                                <source
                                    src=<?php echo 'cache/out/' . $MorseCodeConverter->getFilePath(); ?> type="audio/wav"/>
                                Your browser does not support the audio element.
                            </audio>
                        <p>or <a type="audio/wav"
                                 href=<?php echo 'cache/out/' . $MorseCodeConverter->getFilePath(); ?> download>
                                download the generated speech file.</a></p>
                        </p>
                        <?php
                    }
                    ?>
                </td>
            </tr>

            <?php
            if (isset($_POST['loga']) && $_POST['loga'] == 'on'){
            if (isset($_POST['mainButton'])) {
                ?>

                <tr>
                    <td colspan=3>
							<textarea id="output_text_id" name="morze" class="main-textarea" readonly><?php
                                if (isset($_POST['language']) && $_POST['language'] == 'bel' && !empty($result)) echo $result;
                                if (isset($_POST['language']) && $_POST['language'] == 'rus' && !empty($result)) echo $result;
                                if (isset($_POST['language']) && $_POST['language'] == 'eng' && !empty($result)) echo $result;
                                if (isset($_POST['language']) && $_POST['language'] == 'mor' && !empty($result)) echo $result;
                                ?></textarea>
                    </td>
                </tr>
                <?php
            }
            ?>

            <tr>
                <?php
                if (!empty($unknown))
                {
                ?>
                <td>
                    <?php
                    $str = 'Невядомыя сімвалы' . PHP_EOL;
                    echo '<b>' . $str . '</b>'; ?>
                </td>
            <tr>

                <td colspan=3>
								<textarea id="unknown_symbols_id" name="Unknown" class="main-textarea" readonly><?php
                                    if (!empty($unknown) && isset($_POST['language'])) {
                                        echo $unknown;
                                    }
                                    ?></textarea>
                </td>
                <?php
                }
                ?>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="TestButton" class="blue-button" value='Адкалібраваць'>
                    <?php
                    if ($_POST['language'] != 'mor') {
                        ?>
                        <button type="submit" name="TtsButton" id="TtsButton" class="blue-button"> Агучыць</button>
                        <?php
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    if (isset($testResult)) echo $testResult;
                    }
                    ?>
                <td>
            </tr>
            </tr>
            <tr>
                <td>
                </td>
            </tr>
        </table>
    </form>
    <div class="divider"></div>
    <h2 class="sub-caption-smaller">
        We will be happy to receive your suggestions, offers and opinions to <a href="mailto:corpus.by@gmail.com">corpus.by@gmail.com</a><br/>
        Our other prototypes are here: <a href="../">www.Corpus.by</a>, <a href="http://ssrlab.by">www.ssrlab.by</a>.
    </h2>
    <div class="footer">
        <span>&copy; SSRLab, UIIP NAS Belarus, 2016</span>
    </div>
    <br/>
</div>
</body>
</html>