<?php  // $Id: lib.php,v 1.2 2008/07/22 06:24:19 bdaloukas Exp $
/**
 * Library of functions and constants for module game
 *
 * @author 
 * @version $Id: lib.php,v 1.2 2008/07/22 06:24:19 bdaloukas Exp $
 * @package game
 **/


/// CONSTANTS ///////////////////////////////////////////////////////////////////

/**#@+
 * The different review options are stored in the bits of $game->review
 * These constants help to extract the options
 */
/**
 * The first 6 bits refer to the time immediately after the attempt
 */
define('GAME_REVIEW_IMMEDIATELY', 0x3f);
/**
 * the next 6 bits refer to the time after the attempt but while the quiz is open
 */
define('GAME_REVIEW_OPEN', 0xfc0);
/**
 * the final 6 bits refer to the time after the quiz closes
 */
define('GAME_REVIEW_CLOSED', 0x3f000);

// within each group of 6 bits we determine what should be shown
define('GAME_REVIEW_RESPONSES',   1*0x1041); // Show responses
define('GAME_REVIEW_SCORES',      2*0x1041); // Show scores
define('GAME_REVIEW_FEEDBACK',    4*0x1041); // Show feedback
define('GAME_REVIEW_ANSWERS',     8*0x1041); // Show correct answers
// Some handling of worked solutions is already in the code but not yet fully supported
// and not switched on in the user interface.
define('GAME_REVIEW_SOLUTIONS',  16*0x1041); // Show solutions
define('GAME_REVIEW_GENERALFEEDBACK', 32*0x1041); // Show general feedback
/**#@-*/


/**
 * Given an object containing all the necessary data, 
 * (defined by the form in mod.html) this function 
 * will create a new instance and return the id number 
 * of the new instance.
 *
 * @param object $instance An object from the form in mod.html
 * @return int The id of the newly inserted game record
 **/

function game_add_instance($game) {
    
    $game->timemodified = time();
	
    # May have to add extra stuff in here #
    
    $game->id = insert_record("game", $game);
    
    
    return $game->id;
}

/**
 * Given an object containing all the necessary data, 
 * (defined by the form in mod.html) this function 
 * will update an existing instance with new data.
 *
 * @param object $instance An object from the form in mod.html
 * @return boolean Success/Fail
 **/
function game_update_instance($game) {

    $game->timemodified = time();
    $game->id = $game->instance;

    if( !isset( $game->glossarycategoryid)){
        $game->glossarycategoryid = 0;
    }
    
    if( !isset( $game->glossarycategoryid2)){
        $game->glossarycategoryid2 = 0;
    }
    	
    # May have to add extra stuff in here #

    return update_record("game", $game);
}

/**
 * Given an ID of an instance of this module, 
 * this function will permanently delete the instance 
 * and any data that depends on it. 
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 **/
function game_delete_instance($gameid) {
    global $CFG;
       
    $result = true;

    # Delete any dependent records here #
	
	if( ($recs = get_records_select( 'game_attempts', "gameid='$gameid'")) != false){
	    $ids = '';
	    $count = 0;
	    $aids = array();
		foreach( $recs as $rec){
		    $ids .= ','.$rec->id;
		    if( ++$count > 10){
		        $count = 0;
		        $aids[] = $ids;
		        $ids = '';
		    }
		}
		if( $ids != ''){
    		$aids[] = $ids;
        }
        
		foreach( $aids as $ids){
		    if( $result == false){
		        break;
		    }
	        $tables = array( 'game_hangman', 'game_cross', 'game_cryptex', 'game_millionaire', 'game_bookquiz', 'game_sudoku', 'game_snakes');
	        foreach( $tables as $t){
	            $sql = "DELETE FROM {$CFG->prefix}$t WHERE id IN (".substr( $ids, 1).')';
		        if (! execute_sql( $sql, false)) {
			        $result = false;
			        break;
                }
            }
		}
	}
		    
    $tables = array( 'game_attempts', 'game_grades', 'game_export_javame', 'game_bookquiz_questions', 'game_queries');
    foreach( $tables as $t){
        if( $result == false){
            break;
        }
		    
        if (! delete_records( $t, "gameid", $gameid)) {
            $result = false;
		}
	}
	
	if( $result){
        if (!delete_records( 'game', "id", $gameid)) {
            $result = false;
        }
    }
        
    return $result;
}

/**
 * Return a small object with summary information about what a 
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 **/
function game_user_outline($course, $user, $mod, $game) {
    return $return;
}

/**
 * Print a detailed representation of what a user has done with 
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function game_user_complete($course, $user, $mod, $game) {
    return true;
}

/**
 * Given a course and a time, this module should find recent activity 
 * that has occurred in game activities and print it out. 
 * Return true if there was output, or false is there was none. 
 *
 * @uses $CFG
 * @return boolean
 * @todo Finish documenting this function
 **/
function game_print_recent_activity($course, $isteacher, $timestart) {
    global $CFG;

    return false;  //  True if anything was printed, otherwise false 
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such 
 * as sending out mail, toggling flags etc ... 
 *
 * @uses $CFG
 * @return boolean
 * @todo Finish documenting this function
 **/
function game_cron () {
    global $CFG;

    return true;
}

/**
 * Must return an array of grades for a given instance of this module, 
 * indexed by user.  It also returns a maximum allowed grade.
 * 
 * Example:
 *    $return->grades = array of grades;
 *    $return->maxgrade = maximum allowed grade;
 *
 *    return $return;
 *
 * @param int $gameid ID of an instance of this module
 * @return mixed Null or object with an array of grades and with the maximum grade
 **/
function game_grades($gameid) {
/// Must return an array of grades, indexed by user, and a max grade.

    $game = get_record('game', 'id', intval($gameid));
    if (empty($game) || empty($game->grade)) {
        return NULL;
    }

    $return = new stdClass;
    $return->grades = get_records_menu('game_grades', 'gameid', $game->id, '', "userid, score * {$game->grade}");
    $return->maxgrade = $game->grade;

    return $return;
}


/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of game. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $gameid ID of an instance of this module
 * @return mixed boolean/array of students
 **/
function game_get_participants($gameid) {
    return false;
}

/**
 * This function returns if a scale is being used by one game
 * it it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $gameid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 **/
function game_scale_used ($gameid,$scaleid) {
    $return = false;

    //$rec = get_record("game","id","$gameid","scale","-$scaleid");
    //
    //if (!empty($rec)  && !empty($scaleid)) {
    //    $return = true;
    //}
   
    return $return;
}


//////////////////////////////////////////////////////////////////////////////////////
/// Any other game functions go here.  Each of them must have a name that 
/// starts with game_



?>
