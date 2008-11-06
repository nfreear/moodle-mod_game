<?php  // $Id: exporthtml.php,v 1.1 2008/11/06 23:16:45 bdaloukas Exp $
/**
 * This page export the game to html
 * 
 * @author  bdaloukas
 * @version $Id: exporthtml.php,v 1.1 2008/11/06 23:16:45 bdaloukas Exp $
 * @package game
 **/
        
    function game_OnExportHTML( $gameid, $html, $update){
        $game = get_record_select( 'game', "id=$gameid");          
        
        if( $game->gamekind == 'cross'){
            game_OnExportHTML_cross( $game, $html, $update);
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
    
    function game_export_printheader( $title)
    {
        $ret = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n";
        $ret .= '<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="el" xml:lang="el">'."\n";
        $ret .= "<head>\n";
        $ret .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'."\n";
        $ret .= '<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">'."\n";
        $ret .= "<title>$title</title>\n";
        $ret .= "</head>\n";
        $ret .= "<body>";              
        
        return $ret;
    }    

?>
