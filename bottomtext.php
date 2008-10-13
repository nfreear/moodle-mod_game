<?php  // $Id: bottomtext.php,v 1.2 2008/10/13 20:59:40 bdaloukas Exp $
/**
 * This page edits the bottom text of a game
 * 
 * @author  bdaloukas
 * @version $Id: bottomtext.php,v 1.2 2008/10/13 20:59:40 bdaloukas Exp $
 * @package game
 **/
 
    require_once("../../config.php");
    require_once("lib.php");
    require_once("locallib.php");
    
    if( array_key_exists( 'action', $_POST)){
        game_bottomtext_onupdate();
        die;
    }

	$update = (int )$_GET[ 'update'];
	$_GET[ 'id'] = $update;
	require_once( "header.php");

  	$sesskey = $_GET[ 'sesskey'];
	
	if( !isteacherinanycourse( $USER->id)){
		error( get_string( 'only_teachers', 'game'));
	}
	
	$gameid = get_field_select("course_modules", "instance", "id=$update");
    $usehtmleditor = true;
    
    echo '<form name="form" method="post" action="bottomtext.php">';
    
    $game = get_record_select( 'game', "id=$gameid", 'id,bottomtext,course');
    print_textarea($usehtmleditor, 20, 60, 630, 300, 'bottomtext', $game->bottomtext, $game->course);
    use_html_editor();

?>    
<br/>
<!-- These hidden variables are always the same -->
<input type="hidden" name="update"        value="<?php  p($update) ?>" />
<input type="hidden" name="sesskey"        value="<?php  p($sesskey) ?>" />
<input type="hidden" name="action"        value="update" />
<input type="submit" value="<?php  print_string("savechanges") ?>" />
</center>
</form>
<?php  
        
    print_footer();
    
    
    
    
function game_bottomtext_onupdate(){
    global $CFG;
    
  	$update = $_POST[ 'update'];
  	$sesskey = $_POST[ 'sesskey'];

	$gameid = get_field_select("course_modules", "instance", "id=$update");
	
	$game->id = $gameid;
	$game->bottomtext = $_POST[ 'bottomtext'];

    if( !update_record( 'game', $game)){
        error( "game_bottomtext_onupdate: Can't update game id=$game->id");
    }
    
    redirect( "{$CFG->wwwroot}/course/mod.php?update=$update&sesskey=$sesskey&sr=1");
}


?>
