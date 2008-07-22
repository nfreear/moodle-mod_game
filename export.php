<?php  // $Id: export.php,v 1.2 2008/07/22 06:24:19 bdaloukas Exp $
/**
 * This page edits the bottom text of a game
 * 
 * @author  bdaloukas
 * @version $Id: export.php,v 1.2 2008/07/22 06:24:19 bdaloukas Exp $
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
    $javame = game_getjavame( $game->id); 
    
    if( $javame->description == ''){
        $javame->description = 'MoodleHangman';            
    }
    if( $javame->name == ''){
        $javame->name = 'moodlehangman';
    }
    if( $javame->version == ''){
        $javame->version = '1.0';
    }
    if( $javame->createdby == ''){
        $javame->createdby = 'module Game';
    }    
    
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

<tr>
<td><?php echo get_string( 'javame_filename', 'game'); ?></td>
<td><input type="input" size="20" name="filename" value="<?php echo $javame->filename; ?>"/></td>
</tr>

<tr>
<td><?php echo get_string( 'javame_icon', 'game'); ?></td>
<td><input type="input" size="20" name="icon" value="<?php echo $javame->icon; ?>"/></td>
</tr>

<tr>
<td><?php echo get_string( 'javame_createdby', 'game'); ?></td>
<td><input type="input" size="20" name="createdby" value="<?php echo $javame->createdby; ?>"/></td>
</tr>


<tr>
<td><?php echo get_string( 'javame_vendor', 'game'); ?></td>
<td><input type="input" size="20" name="vendor" value="<?php echo $javame->vendor; ?>"/></td>
</tr>

<tr>
<td><?php echo get_string( 'javame_name', 'game'); ?></td>
<td><input type="input" size="20" name="name" value="<?php echo $javame->name; ?>"/></td>
</tr>

<tr>
<td><?php echo get_string( 'javame_description', 'game'); ?></td>
<td><input type="input" size="20" name="description" value="<?php echo $javame->description; ?>"/></td>
</tr>

<tr>
<td><?php echo get_string( 'javame_version', 'game'); ?></td>
<td><input type="input" size="20" name="version" value="<?php echo $javame->version; ?>"/></td>
</tr>





</table>


<!-- These hidden variables are always the same -->
<input type="hidden" name="update"        value="<?php  p($update) ?>" />
<input type="hidden" name="id"        value="<?php  p($javame->id) ?>" />
<input type="hidden" name="action"        value="update" /><br>
<input type="submit" value="<?php  print_string('export', 'game') ?>" />
</center>
</form>
<?php  
        
    print_footer();
    
    function game_OnExport( $gameid){        
        $javame->id = $_POST[ 'id'];
        $javame->filename = $_POST[ 'filename'];
        $javame->icon = $_POST[ 'icon'];
        $javame->createdby = $_POST[ 'createdby'];
        $javame->vendor = $_POST[ 'vendor'];
        $javame->name = $_POST[ 'name'];
        $javame->description = $_POST[ 'description'];
        $javame->version = $_POST[ 'version'];
        
		if (!(update_record( "game_export_javame", $javame))){
			error("game_export_javame: not updated id=$javame->id");
	    }
	            
        game_OnExportJavaME( $gameid, $javame);
    }
    
    function game_getjavame( $gameid){
        $rec = get_record_select( 'game_export_javame', "gameid=$gameid");
        if( $rec == false){           
            $rec->gameid = $gameid;     
            if (!insert_record( "game_export_javame", $rec)){
                error("Insert page: new page mdl_game_export_javame not inserted");
            }
            $rec = get_record_select( 'game_export_javame', "gameid=$gameid");
        }
        
        if( $rec->vendor == ''){
            $rec->vendor = 'module Game';
        }
        
        return $rec;
    }

?>
