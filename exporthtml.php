<?php  // $Id: exporthtml.php,v 1.3 2009/07/28 16:50:08 bdaloukas Exp $
/**
 * This page export the game to html for games: cross, hangman
 * 
 * @author  bdaloukas
 * @version $Id: exporthtml.php,v 1.3 2009/07/28 16:50:08 bdaloukas Exp $
 * @package game
 **/
 
    require_once( "exportjavame.php");
        
    function game_OnExportHTML( $gameid, $html, $update){
        $game = get_record_select( 'game', "id=$gameid");          
        
        switch( $game->gamekind){
        case 'cross':
            game_OnExportHTML_cross( $game, $html, $update);
            break;
        case 'hangman':
            game_OnExportHTML_hangman( $game, $html, $update);
            break;
        }
    }
    
    function game_OnExportHTML_cross( $game, $html, $update){
    
        global $CFG;
        
        if( $html->filename == ''){
            $html->filename = 'cross';
        }
        
        $filename = $html->filename . '.htm';
        
        require( "cross/play.php");
        $attempt = false;
        game_getattempt( $game, $crossrec);
        
        $ret = game_export_printheader( $html->title);
        
        echo "$ret<br>";
        
        ob_start();

        game_cross_play( $update, $game, $attempt, $crossrec, '', true, false, false, false, $html->checkbutton, true, $html->printbutton);

        $output_string = ob_get_contents();
        ob_end_clean();
                
        $courseid = $game->course;
        $course = get_record_select( 'course', "id=$courseid");
        
        $destdir = "$CFG->dataroot/$courseid/export";
        if( !file_exists( $destdir)){
            mkdir( $destdir);
        }
        
        $filename = $html->filename . '.htm';
        
        file_put_contents( $destdir.'/'.$filename, $ret . "\r\n" . $output_string);
                        
        echo "$ret<a href=\"{$CFG->wwwroot}/file.php/$courseid/export/$filename\">{$filename}</a>";
    }
    
    function game_export_printheader( $title, $showbody=true)
    {
        $ret = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n";
        $ret .= '<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="el" xml:lang="el">'."\n";
        $ret .= "<head>\n";
        $ret .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'."\n";
        $ret .= '<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">'."\n";
        $ret .= "<title>$title</title>\n";
        $ret .= "</head>\n";
        if( $showbody)
            $ret .= "<body>";              
        
        return $ret;
    }    
    
    function game_export_html_encrypt( $input)
    {
	    $textlib = textlib_get_instance();
	
        $temp = '';
        
        $length = $textlib->strlen($input);
        
        for($i = 0; $i < $length; $i++)
            $temp .= '%' . utf8_encode( $textlib->substr( $input, $i, 1));
            
        return $temp;
    }
    
    function game_OnExportHTML_hangman( $game, $html, $update){
    
        global $CFG;
        
        if( $html->filename == ''){
            $html->filename = 'hangman';
        }
        
        $inputsize = ( $html->inputsize < 5 ? 15 : $html->inputsize);
        $filename = $html->filename . '.htm';
        
        $ret = game_export_printheader( $html->title, false);
        $ret .= "\r<body onload=\"reset()\">\r";

        ob_start();
                        
        //Here is the code of hangman        
?>
<script type="text/javascript">


// Hangman for moodle by Vasilis Daloukas
// The script is based on HangMan II script- By Chris Fortey (http://www.c-g-f.net/)

var can_play = true;
<?php
        $map = game_exmportjavame_getanswers( $game, false);
        if( $map == false){
            error( 'No Questions');
        }
        
        $questions = '';
        $words = '';
        $lang = '';
        $allletters = '';
        foreach( $map as $line)
        {
            $answer = game_upper( $line->answer);
            if( $lang == ''){
                $lang = $game->language;
                if( $lang == '')
                    $lang = game_detectlanguage( $answer);
                if( $lang == '')
                    $lang = current_language();
                $allletters = game_getallletters( $answer, $lang);
            }  
        
            if( game_getallletters( $answer, $lang) != $allletters)
                continue;
                
            if( $questions != '')
                $questions .= ', ';
            if( $words != '')
                $words .= ', ';
            $questions .= '"'.$line->question.'"';
            $words .= '"'.base64_encode( $answer).'"';
        }
        echo "var questions = new Array($questions);\r";
        echo "var words = new Array($words);\r";
?>

var to_guess = "";
var display_word = "";
var used_letters = "";
var wrong_guesses = 0;


function selectLetter(l)
{
    if (can_play == false)
    {
    }

    if (used_letters.indexOf(l) != -1)
    {
        return;
    }
	
    used_letters += l;
    document.game.usedLetters.value = used_letters;
	
    if (to_guess.indexOf(l) != -1)
    {
        // correct letter guess
        pos = 0;
        temp_mask = display_word;
        while (to_guess.indexOf(l, pos) != -1)
        {
            pos = to_guess.indexOf(l, pos);			
            end = pos + 1;

            start_text = temp_mask.substring(0, pos);
            end_text = temp_mask.substring(end, temp_mask.length);

            temp_mask = start_text + l + end_text;
            pos = end;
        }

        display_word = temp_mask;
        document.game.displayWord.value = display_word;
		
        if (display_word.indexOf("#") == -1)
        {
            // won
            alert( "<?php echo game_get_string_lang( 'hangman_win', 'game', $lang); ?>");
            can_play = false;
            reset();
        }
    }
    else
    {
        // incortect letter guess
        wrong_guesses += 1;
        eval("document.hm.src=\"hangman_" + wrong_guesses + ".jpg\"");
		
        if (wrong_guesses == 7)
        {
            // lost
            alert( "<?php echo strip_tags( game_get_string_lang( 'hangman_loose', 'game', $lang)); ?>");
            can_play = false;
            reset();
        }
    }
}

function stripHTML(oldString) {

  return oldString.replace(/<&#91;^>&#93;*>/g, "");
  
}

function reset()
{
    selectWord();

    document.game.usedLetters.value = "";
    used_letters = "";
    wrong_guesses = 0;
    document.hm.src="hangman_0.jpg";
}

function selectWord()
{
    can_play = true;
    random_number = Math.round(Math.random() * (words.length - 1));
    to_guess =  Base64.decode( words[random_number]);    to_question = questions[random_number];	
    // display masked word
    masked_word = createMask(to_guess);
    document.game.displayWord.value = masked_word;
    display_word = masked_word;
    
    document.getElementById('question').innerHTML=to_question;
}

function createMask(m)
{
    mask = "";
    word_lenght = m.length;


    for (i = 0; i < word_lenght; i ++)
    {
        mask += "#";
    }

    return mask;
}

/**
*
*  Base64 encode / decode
*  http://www.webtoolkit.info/
*
**/
 
var Base64 = {
 
	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
 
	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;
 
		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
 
		while (i < input.length) {
 
			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));
 
			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;
 
			output = output + String.fromCharCode(chr1);
 
			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}
 		}
 
		output = Base64._utf8_decode(output);
 
		return output;
 
	}, 
 
	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
 
		while ( i < utftext.length ) {
 
			c = utftext.charCodeAt(i);
 
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
 
		}
 
		return string;
	}
 
}
</script>
</head>

<div id="question"></div>
<img src="hangman_0.jpg" name="hm"> <a href="javascript:reset();"><?php echo game_get_string_lang( 'html_hangman_new', 'game', $lang); ?> </a>
<form name="game">
<input type="text" name="displayWord" size="<?php echo $inputsize;?>"><READONLY><br>
<input type="text" name="usedLetters" size="<?php echo $inputsize;?>"><READONLY>
</form><br>

<?php
        $textlib = textlib_get_instance();

        $len = $textlib->strlen( $allletters);
        $next = $len / 4;
        for( $i=0; $i < $len; $i++)
        {
            if( $i > $next)
            {
                echo ' ';
                $next += $len/4;
            }
            $letter = $textlib->substr( $allletters, $i, 1);
            echo "<a href=\"javascript:selectLetter('$letter');\">$letter</a>";
        }
?>

</body>
<?php
        //End of hangman code        
        $output_string = ob_get_contents();
        ob_end_clean();
                        
        $courseid = $game->course;
        $course = get_record_select( 'course', "id=$courseid");
        
        $destdir = game_export_createtempdir();
        
        $filename = $html->filename . '.htm';
        
        file_put_contents( $destdir.'/'.$filename, $ret . "\r\n" . $output_string);
        
        $src = $CFG->dirroot.'/mod/game/hangman/1';                
		$handle = opendir( $src);
		while (false!==($item = readdir($handle))) {
			if($item != '.' && $item != '..') {
				if(!is_dir($src.'/'.$item)) {
				    $itemdest = $item;
				    
				    if( strpos( $item, '.') === false)
				        continue;
				
					copy( $src.'/'.$item, $destdir.'/'.$itemdest);
				}
			}
		}
		
		$filezip = game_create_zip( $destdir, $courseid, $html->filename.'.zip');
		
        if( $destdir != ''){
            remove_dir( $destdir);
        }		
                        
        echo "$ret<a href=\"{$CFG->wwwroot}/file.php/$courseid/export/$filezip\">{$filezip}</a>";
    }


?>
