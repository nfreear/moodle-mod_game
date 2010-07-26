<?php  // $Id: mod_form.php,v 1.8 2010/07/26 00:07:13 bdaloukas Exp $
/**
 * Form for creating and modifying a game 
 *
 * @package   game
 * @author    Alastair Munro <alastair@catalyst.net.nz>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_game_mod_form extends moodleform_mod {
    
    function definition() {
        global $CFG, $DB, $COURSE;

        $mform =& $this->_form;
        $id = $this->_instance;

        if(!empty($this->_instance)){
            if($g = $DB->get_record('game', array('id' => $id))){
                $gamekind = $g->gamekind;
            }
            else{
                error('incorrect game');
            }
        } 
        else {     
            $gamekind = required_param('type', PARAM_ALPHA);
        }

        //Hidden elements
        $mform->addElement('hidden', 'gamekind', $gamekind);
        $mform->setDefault('gamekind',$gamekind);
        $mform->addElement('hidden', 'type', $gamekind);
        $mform->setDefault('type', $gamekind);

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', 'Name', array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)){
            $mform->setType('name', PARAM_TEXT);
        }
        else{
            $mform->setType('name', PARAM_CLEAN);
        }
        if( !isset( $g))
            $mform->setDefault('name', get_string( 'game_'.$gamekind, 'game'));
        $mform->addRule('name', null, 'required', null, 'client');

        $hasglossary = ($gamekind == 'hangman' || $gamekind == 'cross' || $gamekind == 'cryptex' || $gamekind == 'sudoku' || $gamekind == 'hiddenpicture' || $gamekind == 'snakes');

        $questionsourceoptions = array();
        if($hasglossary)
            $questionsourceoptions['glossary'] = get_string('modulename', 'glossary');
        $questionsourceoptions['question'] = get_string('sourcemodule_question', 'game');
        if( $gamekind != 'bookquiz')
            $questionsourceoptions['quiz'] = get_string('modulename', 'quiz');
        $mform->addElement('select', 'sourcemodule', get_string('sourcemodule','game'), $questionsourceoptions);

        if($hasglossary){
            $a = array();
            if($recs = $DB->get_records('glossary', array( 'course' => $COURSE->id), 'id,name')){
                foreach($recs as $rec){
                    $a[$rec->id] = $rec->name;
                }                                            
            }
            $mform->addElement('select', 'glossaryid', get_string('sourcemodule_glossary', 'game'), $a);
            $mform->disabledIf('glossaryid', 'sourcemodule', 'neq', 'glossary');

            if( count( $a) == 0)
                $select = 'glossaryid=-1';
            else if( count( $a) == 1)
                $select = 'glossaryid='.$rec->id;
            else
            {
                $select = '';
                foreach($recs as $rec){
                    $select .= ','.$rec->id;
                }
                $select = 'g.id IN ('.substr( $select, 1).')';
            }
            $select .= ' AND g.id=gc.glossaryid';
            $table = "{glossary} g, {glossary_categories} gc";
            $a = array();
            $a[ ] = '';
            $sql = "SELECT gc.id,gc.name,g.name as name2 FROM $table WHERE $select ORDER BY g.name,gc.name";
            if($recs = $DB->get_records_sql( $sql)){
                foreach($recs as $rec){
                    $a[$rec->id] = $rec->name2.' -> '.$rec->name;
                }
            }
            $mform->addElement('select', 'glossarycategoryid', get_string('sourcemodule_glossarycategory', 'game'), $a);
            $mform->disabledIf('glossarycategoryid', 'sourcemodule', 'neq', 'glossary');
        }

        
        //*********************
        // Question Category - Short Answer

        if( $gamekind != 'bookquiz'){
            $context = get_context_instance(50, $COURSE->id);
            $select = " contextid in ($context->id)";

            $a = array();
            if($recs = $DB->get_records_select('question_categories', $select, null, 'id,name')){
                foreach($recs as $rec){
                    $s = $rec->name;
                    if(($count = $DB->count_records('question', array( 'category' => $rec->id))) != 0){
                        $s .= " ($count)";
                    }
                    $a[$rec->id] = $s;
                }
            }
            
            $mform->addElement('select', 'questioncategoryid', get_string('sourcemodule_questioncategory', 'game'), $a);
            $mform->disabledIf('questioncategoryid', 'sourcemodule', 'neq', 'question');
        }

        if($gamekind == 'hangman' || $gamekind == 'cross' || $gamekind == 'cryptex' || $gamekind == 'sudoku' || $gamekind == 'hiddenpicture' || $gamekind == 'snakes'){
            $a = array();
            $sql = "SELECT DISTINCT cat.id, cat.name FROM {question_categories} cat LEFT JOIN mdl_question qst ON cat.id=qst.category WHERE cat.contextid=8 AND qst.qtype='shortanswer'";
            if($recs = $DB->get_records_sql($sql)){
                foreach($recs as $rec){
                    $s = $rec->name;
                    if(($count = $DB->count_records('question', array( 'category' => $rec->id))) != 0){
                        $s .= " ($count)";
                    }
                    $a[$rec->id] = $s;
                }
            }
            $mform->addElement('select', 'questioncategoryid', get_string('sourcemodule_questioncategory', 'game'), $a);
            $mform->disabledIf('questioncategoryid', 'sourcemodule', 'neq', 'question');
        }


        //***********************
        // Quiz Category
        
        if( $gamekind != 'bookquiz'){
            $a = array();
            if( $recs = $DB->get_records('quiz', array( 'course' => $COURSE->id), 'id,name')){
                foreach( $recs as $rec){
                    $a[$rec->id] = $rec->name;
                }
            }
            $mform->addElement('select', 'quizid', get_string('sourcemodule_quiz', 'game'), $a);
            $mform->disabledIf('quizid', 'sourcemodule', 'neq', 'quiz');
        }


        //***********************
        // Book
        if($gamekind == 'bookquiz'){
            $a = array();
            if($recs = $DB->get_records('book', array( 'course' => $COURSE->id), 'id,name')){
                foreach($recs as $rec){
                    $a[$rec->id] = $rec->name;
                }                                            
            }
            $mform->addElement('select', 'bookid', get_string('sourcemodule_book', 'game'), $a);
        }

//---------------------------------------------------------------------------
// Grade options 

        $mform->addElement('header', 'gradeoptions', get_string('grades', 'grades'));
        $mform->addElement('text', 'grade', get_string( 'grademax', 'grades'));
        $mform->setType('numwords', PARAM_INT);
        $gradingtypeoptions = array();
        $gradingtypeoptions[0] = get_string('gradehighest','game');
        $gradingtypeoptions[1] = get_string('gradeaverage','game');
        $gradingtypeoptions[2] = get_string('attemptfirst','game');
        $gradingtypeoptions[3] = get_string('attemptlast','game');
        $mform->addElement('select', 'grademethod', get_string('grademethod','game'), $gradingtypeoptions);
        
//---------------------------------------------------------------------------
// Hangman options

        if($gamekind == 'hangman'){
            $mform->addElement('header', 'hangman', get_string( 'hangman_options', 'game'));
            $mform->addElement('text', 'param4', get_string('hangman_maxtries', 'game'));
            $mform->setType('param4', PARAM_INT);
            $mform->addElement('selectyesno', 'param1', get_string('hangman_showfirst', 'game'));
            $mform->addElement('selectyesno', 'param2', get_string('hangman_showlast', 'game'));
            $mform->addElement('selectyesno', 'param7', get_string('hangman_allowspaces','game'));
            $mform->addElement('selectyesno', 'param8', get_string('hangman_allowsub', 'game'));

            $a = array( 1 => 1);
            $mform->addElement('select', 'param', get_string('hangman_imageset','game'), $a);

            $mform->addElement('selectyesno', 'param5', get_string('hangman_showquestion', 'game'));
            $mform->addElement('selectyesno', 'param6', get_string('hangman_showcorrectanswer', 'game'));

            $a = array();
            $a = get_string_manager()->get_list_of_translations();
		    $a[ ''] = '----------';
            ksort( $a);
            $mform->addElement('select', 'language', get_string('hangman_language','game'), $a);
        }

//---------------------------------------------------------------------------
// Crossword options

        if($gamekind == 'cross'){ 
            $mform->addElement('header', 'cross', get_string( 'cross_options', 'game'));
            $mform->addElement('text', 'param1', get_string('cross_maxcols', 'game'));
            $mform->setType('param1', PARAM_INT);
            $mform->addElement('text', 'param2', get_string('cross_maxwords', 'game'));
            $mform->setType('param2', PARAM_INT);
            $mform->addElement('selectyesno', 'param7', get_string('hangman_allowspaces','game'));
            $crosslayoutoptions = array();
            $crosslayoutoptions[0] = get_string('cross_layout0', 'game');
            $crosslayoutoptions[1] = get_string('cross_layout1', 'game');
            $mform->addElement('select','param3', get_string('cross_layout', 'game'), $crosslayoutoptions);
        }

//---------------------------------------------------------------------------
// Cryptex options

        if($gamekind == 'cryptex'){
            $mform->addElement('header', 'cryptex', get_string( 'cryptex_options', 'game'));
            $mform->addElement('text', 'param1', get_string('cryptex_maxcols', 'game'));
            $mform->setType('param1', PARAM_INT);
            $mform->addElement('text', 'param2', get_string('cryptex_maxwords', 'game'));
            $mform->setType('param2', PARAM_INT);
            $mform->addElement('selectyesno', 'param7', get_string('hangman_allowspaces','game'));
            $mform->addElement('text', 'param8', get_string('cryptex_maxtries','game'));
            $mform->setType('param8', PARAM_INT);
        }
        
//---------------------------------------------------------------------------
// Millionaire options

        if($gamekind == 'millionaire'){
            global $OUTPUT, $PAGE;

            $mform->addElement('header', 'millionaire', get_string( 'millionaire_options', 'game'));
            $mform->addElement('text', 'param8', get_string('millionaire_background', 'game'));
            $mform->setDefault('param8', '#408080');


            //$mform->addElement('colorpicker', 'param8', get_string('millionaire_background', 'game'));
            //$mform->registerRule('color','regex','/^#([a-fA-F0-9]{6})$/');
            //$mform->addRule('config_bgcolor','Enter a valid RGB color - # and then 6 characters','color');

            $mform->addElement('selectyesno', 'shuffle', get_string('millionaire_shuffle','game'));
        }

//---------------------------------------------------------------------------
// Sudoku options

        if($gamekind == 'sudoku'){
            $mform->addElement('header', 'sudoku', get_string( 'sudoku_options', 'game'));
            $mform->addElement('text', 'param2', get_string('sudoku_maxquestions', 'game'));
        }

//---------------------------------------------------------------------------
// Snakes and Ladders options

        if($gamekind == 'snakes'){
            $mform->addElement('header', 'snakes', get_string( 'snakes_options', 'game'));
            $snakesandladdersbackground = array();
            if($recs = $DB->get_records( 'game_snakes_database', null, 'id,name')){
                foreach( $recs as $rec){
                    $snakesandladdersbackground[$rec->id] = $rec->name;
                }
            }
            if(count($snakesandladdersbackground) == 0){
                require("{$CFG->dirroot}/mod/game/db/importsnakes.php");

                if($recs = $DB->get_records('game_snakes_database', null, 'id,name')){
                    foreach($recs as $rec){
                        $snakesandladdersbackground[$rec->id] = $rec->name;
                    }
                }
            }
            $mform->addElement('select', 'param3', get_string('snakes_background', 'game'), $snakesandladdersbackground);
        }

//---------------------------------------------------------------------------
// Hidden Picture options

        if($gamekind == 'hiddenpicture'){
            $mform->addElement('header', 'hiddenpicture', get_string( 'hiddenpicture_options', 'game'));
            $mform->addElement('text', 'param1', get_string('hiddenpicture_across', 'game'));
            $mform->setType('param1', PARAM_INT);
            $mform->addElement('text', 'param2', get_string('hiddenpicture_down', 'game'));
            $mform->setType('param2', PARAM_INT);

            $a = array();
            if($recs = $DB->get_records('glossary', array( 'course' => $COURSE->id), 'id,name')){
                foreach($recs as $rec){
                    $cmg = get_coursemodule_from_instance('glossary', $rec->id, $COURSE->id);
                    $context = get_context_instance(CONTEXT_MODULE, $cmg->id);
                    if( $DB->record_exists( 'files', array( 'contextid' => $context->id))){
                        $a[$rec->id] = $rec->name;
                    }
                }                                            
            }
            $mform->addElement('select', 'glossaryid2', get_string('hiddenpicture_pictureglossary', 'game'), $a);

            $mform->addElement('text', 'param4', get_string('hiddenpicture_width', 'game'));
            $mform->setType('param4', PARAM_INT);
            $mform->setDefault('param4',3);
            $mform->addELement('text', 'param5', get_string('hiddenpicture_height', 'game'));
            $mform->setType('param5', PARAM_INT);
            $mform->setDefault('param5',3);
            $mform->addElement('selectyesno', 'param7', get_string('hangman_allowspaces','game'));
        }

//---------------------------------------------------------------------------
// Header/Footer options

        $mform->addElement('header', 'headerfooteroptions', 'Header/Footer Options');
        $mform->addElement('htmleditor', 'toptext', get_string('toptext','game'));
        $mform->addElement('htmleditor', 'bottomtext', get_string('bottomtext','game'));

//---------------------------------------------------------------------------
        $features = new stdClass;
        $this->standard_coursemodule_elements($features);

//---------------------------------------------------------------------------
// buttons
        $this->add_action_buttons();
    }


    function validation($data, $files){
        $errors = parent::validation($data, $files);
        return $errors;
    }


    function set_data($default_values) {
        if( isset( $default_values->gamekind)){
            if( $default_values->gamekind == 'millionaire'){
                if( isset( $default_values->param8))
                    $default_values->param8 = '#'.strtoupper( dechex( $default_values->param8));
            }
        }

        parent::set_data($default_values);
    }
}
