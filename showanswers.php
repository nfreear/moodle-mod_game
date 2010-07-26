<?php  // $Id: showanswers.php,v 1.1 2010/07/26 00:16:34 bdaloukas Exp $
/**
 * This page shows the answers of the current game
 * 
 * @author  bdaloukas
 * @version $Id: showanswers.php,v 1.1 2010/07/26 00:16:34 bdaloukas Exp $
 * @package game
 **/
 
    require_once("../../config.php");
    require_once( "header.php");

    if (!has_capability('mod/game:viewreports', $context)){
		error( get_string( 'only_teachers', 'game'));
	}

    $PAGE->navbar->add(get_string('showanswers', 'game'));

    $existsbook = ($DB->get_record( 'modules', array( 'name' => 'book'), 'id,id'));
    showanswers( $game, $existsbook);

    echo $OUTPUT->footer();

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
    global $DB;

    if( $game->gamekind != 'bookquiz'){
        $select = ' category='.$game->questioncategoryid;

        if( $game->subcategories){
            $cats = question_categorylist( $game->questioncategoryid);
            if( strpos( $cats, ',') > 0){
                $select = ' category in ('.$cats.')';
            }
        }
    }else
    {
        $context = get_context_instance(50, $COURSE->id);
        $select = " contextid in ($context->id)";
        $select2 = '';
        if( $recs = $DB->get_records_select( 'question_categories', $select, null, 'id,id')){
            foreach( $recs as $rec){
                $select2 .= ','.$rec->id;
            }
        }
        $select = ' AND category IN ('.substr( $select2, 1).')';
    }
    
    $select .= ' AND hidden = 0 ';
    $select .= showanswers_appendselect( $game);
    
    $showcategories = ($game->gamekind == 'bookquiz');
    $order = ($showcategories ? 'category,questiontext' : 'questiontext');
    showanswers_question_select( $game, '{question}', $select, '*', $order, $showcategories, $game->course);
}


function showanswers_quiz( $game)
{
    global $CFG;

	$select = "quiz='$game->quizid' ".
			  " AND qqi.question=q.id".
			  " AND q.hidden=0".
              showanswers_appendselect( $game);
	$table = "{question} q,{quiz_question_instances} qqi";
	
    showanswers_question_select( $game, $table, $select, "q.*", 'category,questiontext', false, $game->course);
}


function showanswers_question_select( $game, $table, $select, $fields='*', $order='questiontext', $showcategoryname=false, $courseid=0)
{
    global $CFG, $DB;

    $sql = "SELECT $fields FROM $table WHERE $select ORDER BY $order";
    if( ($questions = $DB->get_records_sql( $sql)) === false){
        return;
    }
	
	$categorynames = array();
	if( $showcategoryname){
	    $select = '';
    	$recs = $DB->get_records( 'question_categories', null, '', '*', 0, 1);
	    foreach( $recs as $rec){
	    	if( array_key_exists( 'course', $rec)){
	    		$select = "course=$courseid";
	    	}else{
	    		$context = get_context_instance(50, $courseid);
	        		$select = " contextid in ($context->id)";
	    	}
	    	break;
    	}

		if( ($categories = $DB->get_records_select( 'question_categories', $select, null, '', 'id,name'))){
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
	        $recs = $DB->get_records( 'question_answers', array( 'question' => $question->id), 'fraction DESC', 'id,answer,feedback');
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
            $recs = $DB->get_records( 'question_answers', array( 'question' => $question->id));
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
    global $CFG, $DB;
    
	$table = '{glossary_entries} ge';
    $select = "glossaryid={$game->glossaryid}";
    if( $game->glossarycategoryid){
		$select .= " AND gec.entryid = ge.id ".
					    " AND gec.categoryid = {$game->glossarycategoryid}";
		$table .= ",{glossary_entries_categories} gec";		
	}
    $sql = "SELECT id,definition,concept FROM $table WHERE $select ORDER BY definition";
    if( ($questions = $DB->get_records_sql( $sql)) === false){
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
    
	$select = "gbq.questioncategoryid=q.category ".
			  " AND gbq.bookid = $game->bookid".
			  " AND bc.id = gbq.chapterid";
	$table = "{question} q,{game_bookquiz_questions} gbq,{book_chapters} bc";
	
    showanswers_question_select( $game, $table, $select, "q.*", "bc.pagenum,questiontext");
}
