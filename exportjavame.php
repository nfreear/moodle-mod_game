<?php  // $Id: exportjavame.php,v 1.6 2009/04/23 21:37:05 bdaloukas Exp $
/**
 * This page export the game to javame for mobile phones
 * 
 * @author  bdaloukas
 * @version $Id: exportjavame.php,v 1.6 2009/04/23 21:37:05 bdaloukas Exp $
 * @package game
 **/
    
    function game_OnExportJavaME( $gameid, $javame){
        global $CFG;
                
        $game = get_record_select( 'game', "id=$gameid");
        
        $courseid = $game->course;
        $course = get_record_select( 'course', "id=$courseid");
        
        $destdir = game_export_createtempdir();
                
        if( $javame->type == 'hangmanp'){
            $destmobiledir = 'hangmanp';
        }else{
            $destmobiledir = 'hangman';
        }
        $src = $CFG->dirroot.'/mod/game/export/javame/'.$destmobiledir.'/simple';
        
        if( $javame->filename == ''){
            $javame->filename = 'moodle'.$destmobiledir;
        }
                
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
		
		game_exportjavame_exportdata( $src, $destmobiledir, $destdir, $game, $javame->maxpicturewidth);
		
		game_create_manifest_mf( $destdir.'/META-INF', $javame, $destmobiledir);
		
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
?>        
<HTML><HEAD>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
</HEAD><BODY>
<?php
    echo "<a href=\"{$CFG->wwwroot}/file.php/$courseid/export/$file\">{$javame->name}</a>";
?>
</BODY>
</HTML>
<?php
        
    }
    
    function game_exportjavame_exportdata( $src, $destmobiledir, $destdir, $game, $maxwidth){
        global $CFG;
        
		mkdir( $destdir.'/'.$destmobiledir);
                        
		$handle = opendir( $src);
		while (false!==($item = readdir($handle))) {
			if($item != '.' && $item != '..') {
				if(!is_dir($src.'/'.$item)) {
				    if( substr( $item, -4) == '.jpg'){
    					copy( $src.'/'.$item, $destdir."/$destmobiledir/".$item);
                    }
				}
			}
		}

        $lang = $game->language;
        if( $lang == '')
            $lang = current_language();
		copy( $src. '/lang/'.$lang.'/language.txt',  $destdir."/$destmobiledir/language.txt");

        $export_attachment = ( $destmobiledir == 'hangmanp');
		        
        $map = game_exmportjavame_getanswers( $game, $export_attachment);
        if( $map == false){
            error( 'No Questions');
        }
        
        if( $destmobiledir == 'hangmanp'){
            game_exportjavame_exportdata_hangmanp( $src, $destmobiledir, $destdir, $game, $map, $maxwidth);
            return;
        }
        
        $fp = fopen( $destdir."/$destmobiledir/hangman.txt","w");
            fputs( $fp, "1.txt=$destmobiledir\r\n");
        fclose( $fp);
        
        $fp = fopen( $destdir."/$destmobiledir/1.txt","w");
            foreach( $map as $line){
                $s = game_upper( $line->answer) . '=' . $line->question;
                fputs( $fp, "$s\r\n");
            }
        fclose( $fp);
    }
    
    function game_exportjavame_exportdata_hangmanp( $src, $destmobiledir, $destdir, $game, $map, $maxwidth)
    {
        global $CFG;
        
        $fp = fopen( $destdir."/$destmobiledir/$destmobiledir.txt","w");
            fputs( $fp, "01=01\r\n");
        fclose( $fp);
        
        $destdirphoto = $destdir.'/'.$destmobiledir.'/01';
        mkdir( $destdirphoto);
        
        $fp = fopen( $destdirphoto.'/photo.txt',"w");
            foreach( $map as $line){
                
                $file = $line->attachment;
                $pos = strrpos( $file, '.');
                if( $pos != false){
                    $file = $line->id.substr( $file, $pos);
                    $src = $CFG->dataroot.'/'.$game->course.'/moddata/'.$line->attachment;
                    game_export_javame_smartcopyimage( $src, $destdirphoto.'/'.$file, $maxwidth);
                    
                    $s = $file . '=' . game_upper( $line->answer);
                    fputs( $fp, "$s\r\n");
                }
            }
        fclose( $fp);
    }
    
    function game_exmportjavame_getanswers( $game, $export_attachment){
        $map = array();
        
        switch( $game->sourcemodule){
        case 'question':
            return game_exmportjavame_getanswers_question( $game);
        case 'glossary':
            return game_exmportjavame_getanswers_glossary( $game, $export_attachment);
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
	                $src = $CFG->dirroot.'/mod/game/export/javame/hangman/simple';

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
    
    function game_exmportjavame_getanswers_glossary( $game, $export_attachment)
    {
        global $CFG;
    
    	$table = 'glossary_entries ge';
        $select = "glossaryid={$game->glossaryid}";
        if( $game->glossarycategoryid){
	    	$select .= " AND gec.entryid = ge.id ".
					   " AND gec.categoryid = {$game->glossarycategoryid}";
	    	$table .= ",{$CFG->prefix}glossary_entries_categories gec";		
    	}
    	
    	if( $export_attachment){
    	    $select .= " AND attachment <> ''";
    	}
 
        $fields = 'ge.id,definition,concept';
        if( $export_attachment){
            $fields .= ',attachment';
        }
        if( ($questions = get_records_select( $table, $select, 'definition', $fields)) === false){
            return false;
        }
    
        $map = array();
        foreach( $questions as $question){
            unset( $ret);
            $ret->id = $question->id;
            $ret->qtype = 'shortanswer';
            $ret->question = strip_tags( $question->definition);
            $ret->answer = $question->concept;
            
            if( $export_attachment){
                if( $question->attachment != ''){
                    $ret->attachment = "glossary/{$game->glossaryid}/$question->id/$question->attachment";
                }else{
                    $ret->attachment = '';
                }
            }
            
            $map[] = $ret;
        }
        
        return $map;
    }
            
    function game_create_manifest_mf( $dir, $javame, $destmobiledir){
                        
        $fp = fopen( $dir.'/MANIFEST.MF',"w");
        fputs( $fp, "Manifest-Version: 1.0\r\n");
        fputs( $fp, "Ant-Version: Apache Ant 1.7.0\r\n");
        fputs( $fp, "Created-By: {$javame->createdby}\r\n");
        fputs( $fp, "MIDlet-1: MoodleHangman,,$destmobiledir\r\n");
        fputs( $fp, "MIDlet-Vendor: {$javame->vendor}\r\n");
        fputs( $fp, "MIDlet-Name: {$javame->vendor}\r\n");
        fputs( $fp, "MIDlet-Description: {$javame->description}\r\n");
        fputs( $fp, "MIDlet-Version: {$javame->version}\r\n");
        fputs( $fp, "MicroEdition-Configuration: CLDC-1.0\r\n");
        fputs( $fp, "MicroEdition-Profile: MIDP-1.0\r\n");
        
        fclose( $fp);
    }
    
    function game_create_jar( $srcdir, $course, $javame){
        global $CFG;
        
        $dir = $CFG->dataroot . '/' . $course->id;
        $filejar = $dir . "/export/{$javame->filename}.jar";
        if (!file_exists( $dir)){
            mkdir( $dir);
        }

        if (!file_exists( $dir.'./export')){
            mkdir( $dir.'./export');
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
        $filezip = $dir . "/export/{$javame->filename}.zip";

        if (file_exists( $filezip)){
            unlink( $filezip);
        }
        if (!file_exists( $dir.'./export')){
            mkdir( $dir.'./export');
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

function game_export_javame_smartcopyimage( $filename, $dest, $maxwidth)
{
    if( $maxwidth == 0){
        copy( $filename, $dest);
        return;
    }    

    $size = getimagesize( $filename);
    if( $size == false){
        copy( $filename, $dest);
        return;
    }

    $mul = $maxwidth / $size[ 0];
    if( $mul > 1){
        copy( $filename, $dest);
        return;
    }

    $mime = $size[ 'mime'];
    switch( $mime){
    case 'image/png':
        $src_image = imageCreateFromPNG( $filename);
        break;
    case 'image/jpeg':
        $src_image = imagecreatefromjpeg( $filename);
        break;
    case 'image/gif':
        $src_image = imageCreateFromGIF( $filename);
        break;
    default:
        die('Aknown mime type $mime');
        return false;
    }
        
    $dst_w = $size[ 0] * $mul;
    $dst_h = $size[ 1] * $mul;
    $dst_image = imagecreatetruecolor( $dst_w, $dst_h);
    imagecopyresampled( $dst_image, $src_image, 0, 0, 0, 0, $dst_w, $dst_h, $size[ 0], $size[ 1]);
    
    imagejpeg( $dst_image, $dest);
} 
?>
