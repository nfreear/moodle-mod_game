<?php  // $Id: export.php,v 1.1 2008/07/21 21:10:36 bdaloukas Exp $
/**
 * This page edits the bottom text of a game
 * 
 * @author  bdaloukas
 * @version $Id: export.php,v 1.1 2008/07/21 21:10:36 bdaloukas Exp $
 * @package game
 **/
 
    require_once("../../config.php");
    require_once("lib.php");
    require_once("locallib.php");
    require_once("exportjavame.php");

	if( !isteacherinanycourse( $USER->id)){
		error( get_string( 'only_teachers', 'game'));
	}

    if( array_key_exists( 'action', $_POST)){
        $update = (int )$_POST[ 'update'];
	    $gameid = get_field_select("course_modules", "instance", "id=$update");
        game_OnExport( $gameid);
        die;
    }  

    $update = (int )$_GET[ 'update'];
	$gameid = get_field_select("course_modules", "instance", "id=$update");
    
	$_GET[ 'id'] = $update;
	require_once( "header.php");
		    
    echo '<form name="form" method="post" action="export.php">';
    
    $game = get_record_select( 'game', "id=$gameid", 'id,name');
    
?>    
<br/>

<table>
<tr><td colspan=2><center><b><?php echo get_string('export', 'game'); ?></td></tr>
<tr>
<td>Kind:</td>
<td>
<select id="menuvisible" name="visible" >
   <option value="0" selected="selected">JavaME</option>
   <option value="1"></option>
</select>
</td>
</tr>
</table>


<!-- These hidden variables are always the same -->
<input type="hidden" name="update"        value="<?php  p($update) ?>" />
<input type="hidden" name="action"        value="update" /><br>
<input type="submit" value="<?php  print_string('export', 'game') ?>" />
</center>
</form>
<?php  
        
    print_footer();
    
    function game_OnExport( $gameid){
        game_OnExportJavaME( $gameid);
    }

?>
