<?php  // $Id: preview.php,v 1.3 2010/07/16 21:05:23 bdaloukas Exp $
/**
 * This page shows info about an user's attempt of game
 * 
 * @author  bdaloukas
 * @version $Id: preview.php,v 1.3 2010/07/16 21:05:23 bdaloukas Exp $
 * @package game
 **/
 
    require_once("../../config.php");
    require_once("$CFG->dirroot/lib/questionlib.php");
    require_once("$CFG->dirroot/question/type/shortanswer/questiontype.php");
    require_once("lib.php");
    require_once("locallib.php");

    require_once( "header.php");

    require_once( "hangman/play.php");
    require_once( "cross/play.php");
    require_once( "cryptex/play.php");
    require_once( "millionaire/play.php");
    require_once( "sudoku/play.php");
    require_once( "bookquiz/play.php");
    
    if( !isteacher( $game->course, $USER->id)){
    	error( get_string( 'only_teachers', 'game'));
    }

    $currenttab = required_param('action', PARAM_TEXT);

    include('tabs.php');

    switch( $currenttab){
    case 'showattempts':
        game_showattempts($game);
        print_footer();
        die;
    case 'delete':        		
		$attemptid = required_param('attemptid', PARAM_INT); 	
		game_ondeleteattempt($game, $attemptid);
        print_footer();
        die;
    case 'answers':
        showanswers( $game, false);
        print_footer();
        die;
    }

    $attemptid = required_param('attemptid', PARAM_INT);
    $update = required_param('update', PARAM_INT);
		
	$attempt = get_record_select( 'game_attempts', "id=$attemptid");
	$game = get_record_select( 'game', "id=$attempt->gameid");
	$detail = get_record_select( 'game_'.$game->gamekind, "id=$attemptid");
    $solution = ($currenttab == 'solution');

	switch( $game->gamekind)
	{
	case 'cross':
		game_cross_play( $update, $game, $attempt, $detail, '', true, $solution, false, false, false, false, true);
		break;
	case 'sudoku':
		game_sudoku_play( $update, $game, $attempt, $detail, true, $solution);
		break;
	case 'hangman':
		game_hangman_play( $update, $game, $attempt, $detail, true, $solution);
		break;
	case 'cryptex':
		$crossm = get_record_select( 'game_cross', "id=$attemptid");
		game_cryptex_play( $update, $game, $attempt, $detail, $crossm, false, true, $solution);
		break;
	}

    function game_showattempts($game){
        global $CFG;

        $gamekind = $game->gamekind;
        $update = get_coursemodule_from_instance( 'game', $game->id, $game->course)->id;

        //Here are user attempts
        $table = "game_attempts as ga, {$CFG->prefix}user u, {$CFG->prefix}game as g";
        $select = "ga.userid=u.id AND ga.gameid={$game->id} AND g.id={$game->id}";
        $fields = "ga.id, u.lastname, u.firstname, ga.attempts,".
          "timestart, timefinish, timelastattempt, score, ga.lastip, ga.lastremotehost";
            
        $count = count_records_select( $table, $select, 'COUNT( *)');
        $limitfrom = 0;
        $maxlines = 20;
        if (array_key_exists( 'limitfrom', $_GET)) {
            $limitfrom = $_GET[ 'limitfrom'];
        }
        $recslimitfrom = $recslimitnum = '';
        if( $count > $maxlines){
            $recslimitfrom = ( $limitfrom ? $limitfrom * $maxlines : '');
            $recslimitnum = $maxlines;

            for($i=0; $i*$maxlines < $count; $i++){
                if( $i == $limitfrom){
                    echo ($i+1).' ';
                }else
                {
                    echo "<A HREF=\"{$CFG->wwwroot}/mod/game/preview.php?action=showattempts&amp;update=$update&amp;q={$game->id}&amp;limitfrom=$i&\">".($i+1)."</a>";
                    echo ' &nbsp;';
                }
            }
            echo "<br>";
        }

        if( ($recs = get_records_select( $table, $select, 'timelastattempt DESC,timestart DESC', $fields, $recslimitfrom, $recslimitnum)) != false){
            echo '<table border="1">';
            echo '<tr><td>'.get_string( 'delete').'</td><td>'.get_string('user').'</td>';
            echo '<td>'.get_string('lastip', 'game').'</td>';
            echo '<td>'.get_string('timestart', 'game').'</td>';
            echo '<td>'.get_string('timelastattempt', 'game').'</td>';
            echo '<td>'.get_string('timefinish', 'game').'</td>';
            echo '<td>'.get_string('score', 'game').'</td>';
            echo '<td>'.get_string('attempts', 'game').'</td>';
            echo '<td>'.get_string('preview', 'game').'</td>';
            echo '<td>'.get_string('showsolution', 'game').'</td>';
            echo "</tr>\r\n";
        	$strftimedate = get_string('formatdatetime', 'game');

            foreach( $recs as $rec){
                echo '<tr>';
                echo '<td><center>';
                if( $rec->timefinish == 0){
                    echo "\r\n<a href=\"{$CFG->wwwroot}/mod/game/preview.php?attemptid={$rec->id}&amp;q={$game->id}&amp;action=delete\">";
                    echo '<img src="'.$CFG->wwwroot.'/pix/t/delete.gif" alt="'.get_string( 'delete').'" /></a>';
                }
                echo '</center></td>';
                echo '<td><center>'.$rec->firstname. ' '.$rec->lastname.'</center></td>';
                echo '<td><center>'.(strlen( $rec->lastremotehost) > 0 ? $rec->lastremotehost : $rec->lastip).'</center></td>';
                echo '<td><center>'.( $rec->timestart != 0 ? userdate($rec->timestart, $strftimedate) : '')."</center></td>\r\n";
                echo '<td><center>'.( $rec->timelastattempt != 0 ? userdate($rec->timelastattempt, $strftimedate) : '').'</center></td>';
                echo '<td><center>'.( $rec->timefinish != 0 ? userdate($rec->timefinish, $strftimedate) : '').'</center></td>';
                echo '<td><center>'.round($rec->score * 100).'</center></td>';
                echo '<td><center>'.$rec->attempts.'</center></td>';
                echo '<td><center>';
	        	//Preview
	        	if( ($gamekind == 'cross') or ($gamekind == 'sudoku') or ($gamekind == 'hangman') or ($gamekind == 'cryptex')){
	        		echo "\r\n<a href=\"{$CFG->wwwroot}/mod/game/preview.php?action=preview&amp;attemptid={$rec->id}&amp;gamekind=$gamekind";
	        		echo '&amp;update='.$update."&amp;q={$game->id}\">";
                    echo '<img src="'.$CFG->wwwroot.'/pix/t/preview.gif" alt="'.get_string( 'preview', 'game').'" /></a>';
	        	}
                echo '</center></td>';

	    	    //Show solution
                echo '<td><center>';
	    	    if( ($gamekind == 'cross') or ($gamekind == 'sudoku') or ($gamekind == 'hangman') or ($gamekind == 'cryptex') ){
	    		    echo "\r\n<a href=\"{$CFG->wwwroot}/mod/game/preview.php?action=solution&amp;attemptid={$rec->id}&amp;gamekind={$gamekind}&amp;update=$update&amp;solution=1&amp;q={$game->id}\">";
	    		    echo '<img src="'.$CFG->wwwroot.'/pix/t/preview.gif" alt="'.get_string( 'showsolution', 'game').'" /></a>';
    	    	}
                echo '</center></td>';
                echo "</tr>\r\n";
            }
            echo "</table>\r\n";
        }
    }

	function game_ondeleteattempt( $game, $attemptid)
	{
		global $CFG;
		
		$attempt = get_record_select( 'game_attempts', 'id='.$attemptid);
		$game = get_record_select( 'game', 'id='.$attempt->gameid);
				
		switch( $game->gamekind)
		{
		case 'bookquiz':
			delete_records( 'game_bookquiz_chapters', 'attemptid', $attemptid);
			break;
		}
		delete_records( 'game_queries', 'attemptid', $attemptid);
		delete_records( 'game_attempts', 'id', $attemptid);
		
		$url = $CFG->wwwroot.'/mod/game/preview.php?action=showattempts&q='.$game->id;
        redirect( $url);
	}

function showanswers( $game, $existsbook)
{
    if( $game->gamekind == 'bookquiz' and $existsbook){
        showanswers_bookquiz( $game);
        return;
    }
    
    switch( $game->sourcemodule){
    case 'question':
        showanswers_question( $game);
        break;
    case 'glossary':
        showanswers_glossary( $game);
        break;
    case 'quiz':
        showanswers_quiz( $game);
        break;
    }
}

function showanswers_appendselect( $game)
{
    switch( $game->gamekind){
    case 'hangman':
    case 'cross':
    case 'crypto':
        return " AND qtype='shortanswer'";
    case 'millionaire':
        return " AND qtype = 'multichoice'";
    case 'sudoku':
    case 'bookquiz':
    case 'snakes':
        return " AND qtype in ('shortanswer', 'truefalse', 'multichoice')";
    }
    
    return '';
}

function showanswers_question( $game)
{
    
    $select = ' category='.$game->questioncategoryid;
        
    if( $game->subcategories){
        $cats = question_categorylist( $game->questioncategoryid);
        if( strpos( $cats, ',') > 0){
            $select = ' category in ('.$cats.')';
        }
    }
    
    $select .= ' AND hidden = 0 ';
    $select .= showanswers_appendselect( $game);
    
    showanswers_question_select( $game, 'question', $select, '*', 'questiontext', false, $game->course);
}


function showanswers_quiz( $game)
{
    global $CFG;

	$select = "quiz='$game->quizid' ".
			  " AND {$CFG->prefix}quiz_question_instances.question={$CFG->prefix}question.id".
			  " AND {$CFG->prefix}question.hidden=0".
              showanswers_appendselect( $game);
	$table = "question,{$CFG->prefix}quiz_question_instances";
	
    showanswers_question_select( $game, $table, $select, "{$CFG->prefix}question.*", 'category,questiontext', true, $game->course);
}


function showanswers_question_select( $game, $table, $select, $fields='*', $order='questiontext', $showcategoryname=false, $courseid=0)
{
    global $CFG;
    
    if( ($questions = get_records_select( $table, $select, $order, $fields)) === false){
        return;
    }
	
	$categorynames = array();
	if( $showcategoryname){
	    $select = '';
    	$recs = get_records_select( 'question_categories', '', '', '*', 0, 1);
	    foreach( $recs as $rec){
	    	if( array_key_exists( 'course', $rec)){
	    		$select = "course=$courseid";
	    	}else{
	    		$context = get_context_instance(50, $courseid);
	        		$select = " contextid in ($context->id)";
	    	}
	    	break;
    	}

		if( ($categories = get_records_select( 'question_categories', $select, '', 'id,name'))){
			foreach( $categories as $rec){
				$categorynames[ $rec->id] = $rec->name;
			}
		}
	}
    
    echo '<table border="1">';
    echo '<tr><td></td>';
	if( $showcategoryname){
		echo '<td><b>'.get_string( 'categories', 'quiz').'</b></td>';
	}
    echo '<td><b>'.get_string( 'questions', 'quiz').'</b></td>';
    echo '<td><b>'.get_string( 'answers', 'quiz').'</b></td>';
    echo '<td><b>'.get_string( 'feedbacks', 'game').'</b></td>';
    echo "</tr>\r\n";
    $line = 0;
    foreach( $questions as $question){
        echo '<tr>';
        echo '<td>'.(++$line);
        echo '</td>';

		if( $showcategoryname){
			echo '<td>';
			if( array_key_exists( $question->category, $categorynames)){
				echo $categorynames[ $question->category];
			}else{
				echo '&nbsp;';
			}
			echo '</td>';
		}

        echo '<td>';
        echo "<a title=\"Edit\" href=\"$CFG->wwwroot/question/question.php?inpopup=1&amp;id=$question->id&courseid=$courseid\"  target=\"_blank\"><img src=\"$CFG->wwwroot/pix/t/edit.gif\" alt=\"Edit\" /></a> ";
        echo $question->questiontext.'</td>';
        
        switch( $question->qtype){
        case 'shortanswer':
	        $recs = get_records_select( 'question_answers', "question=$question->id", 'fraction DESC', 'id,answer,feedback');
	        if( $recs == false){
	            $rec = false;
	        }else{
	            foreach( $recs as $rec)
	                break;
	        }
	        echo "<td>$rec->answer</td>";
	        if( $rec->feedback == '')
	            $rec->feedback = '&nbsp;';
	        echo "<td>$rec->feedback</td>";
            break;
        case 'multichoice':
        case 'truefalse':
            $recs = get_records_select( 'question_answers', "question=$question->id");
            $feedback = '';
            echo '<td>';
            $i = 0;
            foreach( $recs as $rec){
                if( $i++ > 0)
                    echo '<br>';
		        if( $rec->fraction == 1){
			        echo " <b>$rec->answer</b>";
	                if( $rec->feedback == '')
	                    $feedback .= '<br>';
	                else
                        $feedback .= "<b>$rec->feedback</b><br>";
			        
                }else
                {
			        echo " $rec->answer";
	                if( $rec->feedback == '')
	                    $feedback .= '<br>';
	                else
                        $feedback .= "<br>";
                }
            }
            echo '</td>';
	        if( $feedback == '')
	            $feedback = '&nbsp;';
	        echo "<td>$feedback</td>";
            break;
        default:
            echo "<td>$question->qtype</td>";
            break;
        }
        echo "</tr>\r\n";
    }
    echo "</table><br>\r\n\r\n";
}

function showanswers_glossary( $game)
{
    global $CFG;
    
	$table = 'glossary_entries';
    $select = "glossaryid={$game->glossaryid}";
    if( $game->glossarycategoryid){
		$select .= " AND {$CFG->prefix}glossary_entries_categories.entryid = {$CFG->prefix}glossary_entries.id ".
					    " AND {$CFG->prefix}glossary_entries_categories.categoryid = {$game->glossarycategoryid}";
		$table .= ",{$CFG->prefix}glossary_entries_categories";		
	}
 
    if( ($questions = get_records_select( $table, $select, 'definition', "{$CFG->prefix}glossary_entries.id,definition,concept")) === false){
        return;
    }
    
    echo '<table border="1">';
    echo '<tr><td></td>';
    echo '<td><b>'.get_string( 'questions', 'quiz').'</b></td>';
    echo '<td><b>'.get_string( 'answers', 'quiz').'</b></td>';
    echo "</tr>\r\n";
    $line = 0;
    foreach( $questions as $question){
        echo '<tr>';
        echo '<td>'.(++$line);
        echo '</td>';
        
        echo '<td>'.$question->definition.'</td>';
        echo '<td>'.$question->concept.'</td>';
        echo "</tr>\r\n";
    }
    echo "</table><br>\r\n\r\n";
}

function showanswers_bookquiz( $game)
{
    global $CFG;
    
	$select = "{$CFG->prefix}game_bookquiz_questions.questioncategoryid={$CFG->prefix}question.category ".
			  " AND {$CFG->prefix}game_bookquiz_questions.bookid = $game->bookid".
			  " AND {$CFG->prefix}book_chapters.id = {$CFG->prefix}game_bookquiz_questions.chapterid";
	$table = "question,{$CFG->prefix}game_bookquiz_questions,{$CFG->prefix}book_chapters";
	
    showanswers_question_select( $game, $table, $select, "{$CFG->prefix}question.*", "{$CFG->prefix}book_chapters.pagenum,questiontext");
}
