<?php  // $Id: exportjavame.php,v 1.4 2008/07/23 06:09:20 bdaloukas Exp $
/**
 * This page export the game to javame for mobile phones
 * 
 * @author  bdaloukas
 * @version $Id: exportjavame.php,v 1.4 2008/07/23 06:09:20 bdaloukas Exp $
 * @package game
 **/
    
    function game_OnExportJavaME( $gameid, $javame){
        global $CFG;
        
        if( $javame->filename == ''){
            $javame->filename = 'moodlehangman';
        }
        
        $game = get_record_select( 'game', "id=$gameid");
        
        $courseid = $game->course;
        $course = get_record_select( 'course', "id=$courseid");
        
        $destdir = game_export_createtempdir();
                
        $src = $CFG->dirroot.'/mod/game/export/javame/hangman/simple';
                
		$handle = opendir( $src);
		while (false!==($item = readdir($handle))) {
			if($item != '.' && $item != '..') {
				if(!is_dir($src.'/'.$item)) {
				    $itemdest = $item;
				    
				    if( substr( $item, -5) == '.java'){
				        continue;   //don't copy the java source code files
				    }
				    
				    if( substr( $itemdest, -8) == '-1.class'){
				        $itemdest = substr( $itemdest, 0, -8).'$1.class';
				    }
				
					copy( $src.'/'.$item, $destdir.'/'.$itemdest);
				}
			}
		}
		
		mkdir( $destdir.'/META-INF');
		
		game_exportjavame_exportdata( $destdir, $game);
		
		game_create_manifest_mf( $destdir.'/META-INF', $javame);
		
		$filejar = game_create_jar( $destdir, $course, $javame);
		if( $filejar == ''){
    		$filezip = game_create_zip( $destdir, $course, $javame);
        }else{
            $filezip = '';
        }

        if( $destdir != ''){
            remove_dir( $destdir);
        }
        
        if( $filezip != ''){
            echo "unzip the $filezip in a directory and when you are in this directory use the command <br><b>jar cvfm {$javame->filename}.jar META-INF/MANIFEST.MF<br></b> to produce the jar files<br><br>";
        }
        
        $file = ($filejar != '' ? $filejar : $filezip);
        echo "<a href=\"{$CFG->wwwroot}/file.php/$courseid/$file\">{$javame->name}</a>";
    }
    
    function game_exportjavame_exportdata( $destdir, $game){
        global $CFG;
        
		mkdir( $destdir.'/hangman');
        
        $src = $CFG->dirroot.'/mod/game/hangman/1';
                
		$handle = opendir( $src);
		while (false!==($item = readdir($handle))) {
			if($item != '.' && $item != '..') {
				if(!is_dir($src.'/'.$item)) {
				    if( substr( $item, -4) == '.jpg'){
    					copy( $src.'/'.$item, $destdir.'/hangman/'.$item);
                    }
				}
			}
		}

        $lang = $game->language;
        if( $lang == '')
            $lang = current_language();
		copy( $CFG->dirroot.'/mod/game/export/javame/hangman/simple/lang/'.$lang.'/language.txt',  $destdir.'/hangman/language.txt');
		        
        $map = game_exmportjavame_getanswers( $game);
        if( $map == false){
            error( 'No Questions');
        }
        
        $fp = fopen( $destdir.'/hangman/hangman.txt',"w");
            fputs( $fp, "1.txt=hangman\r\n");
        fclose( $fp);
        
        $fp = fopen( $destdir.'/hangman/1.txt',"w");
            foreach( $map as $line){
                $s = game_upper( $line->answer) . '=' . $line->question;
                fputs( $fp, "$s\r\n");
            }
        fclose( $fp);
    }
    
    function game_exmportjavame_getanswers( $game){
        $map = array();
        
        switch( $game->sourcemodule){
        case 'question':
            return game_exmportjavame_getanswers_question( $game);
        case 'glossary':
            return game_exmportjavame_getanswers_glossary( $game);
        case 'quiz':
            return game_exmportjavame_getanswers_quiz( $game);
        }
        
        return false;
    }
    
    function game_exmportjavame_getanswers_question( $game){
        $select = 'hidden = 0 AND category='.$game->questioncategoryid;
    
        $select .= game_showanswers_appendselect( $game);
    
        return game_exmportjavame_getanswers_question_select( $game, 'question', $select, '*', 'questiontext', false, $game->course);        
    }
    
    function game_exmportjavame_getanswers_quiz( $game)
    {
        global $CFG;

	    $select = "quiz='$game->quizid' ".
			  " AND {$CFG->prefix}quiz_question_instances.question={$CFG->prefix}question.id".
			  " AND {$CFG->prefix}question.hidden=0".
              game_showanswers_appendselect( $game);
    	$table = "question,{$CFG->prefix}quiz_question_instances";
	
        return game_exmportjavame_getanswers_question_select( $game, $table, $select, "{$CFG->prefix}question.*", 'category,questiontext', true, $game->course);
    }
    
    function game_exmportjavame_getanswers_question_select( $game, $table, $select, $fields='*', $courseid=0)
    {
        global $CFG;
    
        if( ($questions = get_records_select( $table, $select, '', $fields)) === false){
            return;
        }
	    
        $line = 0;
        $map = array();
        
        foreach( $questions as $question){
            unset( $ret);
            $ret->qtype = $question->qtype;
            $ret->question = $question->questiontext;
        
            switch( $question->qtype){
            case 'shortanswer':
	            $rec = get_record_select( 'question_answers', "question=$question->id", "id,answer,feedback");
	            $ret->answer = $rec->answer;
	            $ret->feedback = $rec->feedback;
	            $map[] = $ret;
                break;
            default:
                break;
            }
        }
        
        return $map;
    }
    
    function game_exmportjavame_getanswers_glossary( $game)
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
            return false;
        }
    
        $map = array();
        foreach( $questions as $question){
            unset( $ret);
            $ret->qtype = 'shortanswer';
            $ret->question = strip_tags( $question->definition);
            $ret->answer = $question->concept;
            
            $map[] = $ret;
        }
        
        return $map;
    }
            
    function game_create_manifest_mf( $dir, $javame){
                        
        $fp = fopen( $dir.'/MANIFEST.MF',"w");
        fputs( $fp, "Manifest-Version: 1.0\r\n");
        fputs( $fp, "Ant-Version: Apache Ant 1.7.0\r\n");
        fputs( $fp, "Created-By: {$javame->createdby}\r\n");
        fputs( $fp, "MIDlet-1: MoodleHangman,,hangman\r\n");
        fputs( $fp, "MIDlet-Vendor: {$javame->vendor}\r\n");
        fputs( $fp, "MIDlet-Name: {$javame->vendor}\r\n");
        fputs( $fp, "MIDlet-Description: {$javame->description}\r\n");
        fputs( $fp, "MIDlet-Version: {$javame->version}\r\n");
        fputs( $fp, "MicroEdition-Configuration: CLDC-1.0\r\n");
        fputs( $fp, "MicroEdition-Profile: MIDP-2.0\r\n");
        
        fclose( $fp);
    }
    
    function game_create_jar( $srcdir, $course, $javame){
        global $CFG;
        
        $dir = $CFG->dataroot . '/' . $course->id;
        $filejar = $dir . "/{$javame->filename}.jar";
        if (!file_exists( $dir)){
            mkdir( $dir);
        }

        if (file_exists( $filejar)){
            unlink( $filejar);
        }
    
        $cmd = "cd $srcdir;jar cvfm $filejar META-INF/MANIFEST.MF *";
        exec( $cmd);

        return (file_exists( $filejar) ? "{$javame->filename}.jar" : '');
    }

    function game_create_zip( $srcdir, $course, $javame){
        global $CFG;
        
        $dir = $CFG->dataroot . '/' . $course->id;
        $filezip = $dir . "/{$javame->filename}.zip";

        if (file_exists( $filezip)){
            unlink( $filezip);
        }
        
        $srcfiles = get_directory_list( $srcdir, '', true, true, true);
        $fullsrcfiles = array();
        foreach( $srcfiles as $file){
            $fullsrcfiles[] = $srcdir.'/'.$file;
        }
                
        zip_files( $fullsrcfiles, $filezip);
            
        return (file_exists( $filezip) ? "{$javame->filename}.zip" : '');
    }
    
    
    function game_export_createtempdir(){
        global $CFG;
        
        // create a random upload directory in temp
	    $newdir = $CFG->dataroot."/temp/game";
        if (!file_exists( $newdir)) 
		    mkdir( $newdir);

        $i = 1;
        srand( (double)microtime()*1000000); 
        while(true)
        {
            $r_basedir = "game/$i-".rand(0,10000);
            $newdir = $CFG->dataroot.'/temp/'.$r_basedir;
            if (!file_exists( $newdir)) 
            {
    		    mkdir( $newdir);
                return $newdir;
            }
            $i++;
        }
        return '';
    }
    
function game_showanswers_appendselect( $form)
{
    switch( $form->gamekind){
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

?>
