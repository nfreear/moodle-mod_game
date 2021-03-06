<?php  // $Id: view.php,v 1.10 2010/07/28 06:59:36 bdaloukas Exp $

// This page prints a particular instance of game

    require_once(dirname(__FILE__) . '/../../config.php');
    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->dirroot.'/mod/game/locallib.php');

    $id = optional_param('id', 0, PARAM_INT); // Course Module ID, or

    if (! $cm = get_coursemodule_from_id('game', $id)) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record('course', array('id' => $cm->course))) {
        print_error('coursemisconf');
    }
    if (! $game = $DB->get_record('game', array('id' => $cm->instance))) {
        print_error('invalidcoursemodule');
    }

/// Check login and get context.
    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/game:view', $context);

/// Cache some other capabilites we use several times.
    $canattempt = true;

    $timenow = time();

/// Log this request.
    add_to_log($course->id, 'game', 'view', "view.php?id=$cm->id", $game->id, $cm->id);

/// Initialize $PAGE, compute blocks
    $PAGE->set_url('/mod/game/view.php', array('id' => $cm->id));

    $edit = optional_param('edit', -1, PARAM_BOOL);
    if ($edit != -1 && $PAGE->user_allowed_editing()) {
        $USER->editing = $edit;
    }

    $PAGE->requires->yui2_lib('event');

    $title = $course->shortname . ': ' . format_string($game->name);

    if ($PAGE->user_allowed_editing() && !empty($CFG->showblocksonmodpages)) {
        $buttons = '<table><tr><td><form method="get" action="view.php"><div>'.
            '<input type="hidden" name="id" value="'.$cm->id.'" />'.
            '<input type="hidden" name="edit" value="'.($PAGE->user_is_editing()?'off':'on').'" />'.
            '<input type="submit" value="'.get_string($PAGE->user_is_editing()?'blockseditoff':'blocksediton').'" /></div></form></td></tr></table>';
        $PAGE->set_button($buttons);
    }

    $PAGE->set_title($title);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();

/// Print game name and description
    echo $OUTPUT->heading(format_string($game->name));

/// Display information about this game.

    echo $OUTPUT->box_start('quizinfo');
    if ($game->attempts != 1) {
        echo get_string('gradingmethod', 'quiz', game_get_grading_option_name($game->grademethod));
    }
    echo $OUTPUT->box_end();

/// Show number of attempts summary to those who can view reports.
    if (has_capability('mod/game:viewreports', $context)) {
        if ($strattemptnum = game_num_attempt_summary($game, $cm)) {
            //echo '<div class="gameattemptcounts"><a href="report.php?mode=overview&amp;id=' .
            //        $cm->id . '">' . $strattemptnum . "</a></div>\n";
            echo $strattemptnum;
        }
    }

/// Get this user's attempts.
    $attempts = game_get_user_attempts($game->id, $USER->id);
    $lastfinishedattempt = end($attempts);
    $unfinished = false;
    if ($unfinishedattempt = game_get_user_attempt_unfinished($game->id, $USER->id)) {
        $attempts[] = $unfinishedattempt;
        $unfinished = true;
    }
    $numattempts = count($attempts);

/// Work out the final grade, checking whether it was overridden in the gradebook.
    $mygrade = game_get_best_grade($game, $USER->id);
    $mygradeoverridden = false;
    $gradebookfeedback = '';

    $grading_info = grade_get_grades($course->id, 'mod', 'game', $game->id, $USER->id);
    if (!empty($grading_info->items)) {
        $item = $grading_info->items[0];
        if (isset($item->grades[$USER->id])) {
            $grade = $item->grades[$USER->id];

            if ($grade->overridden) {
                $mygrade = $grade->grade + 0; // Convert to number.
                $mygradeoverridden = true;
            }
            if (!empty($grade->str_feedback)) {
                $gradebookfeedback = $grade->str_feedback;
            }
        }
    }

/// Print table with existing attempts
    if ($attempts) {

        echo $OUTPUT->heading(get_string('summaryofattempts', 'quiz'));

        // Work out which columns we need, taking account what data is available in each attempt.
        list($someoptions, $alloptions) = game_get_combined_reviewoptions($game, $attempts, $context);

        $attemptcolumn = $game->attempts != 1;

        $gradecolumn = $someoptions->scores && ($game->grade > 0);
        //$markcolumn = $gradecolumn && ($game->grade != $game->sumgrades);
        $overallstats = $alloptions->scores;

        // Prepare table header
        $table = new html_table();
        $table->attributes['class'] = 'generaltable gameattemptsummary';
        $table->head = array();
        $table->align = array();
        $table->size = array();
        if ($attemptcolumn) {
            $table->head[] = get_string('attempt', 'game');
            $table->align[] = 'center';
            $table->size[] = '';
        }
        $table->head[] = get_string('timecompleted', 'game');
        $table->align[] = 'left';
        $table->size[] = '';

        if ($gradecolumn) {
            $table->head[] = get_string('grade') . ' / ' . game_format_grade( $game, $game->grade);
            $table->align[] = 'center';
            $table->size[] = '';
        }

        $table->head[] = get_string('timetaken', 'game');
        $table->align[] = 'left';
        $table->size[] = '';

        // One row for each attempt
        foreach ($attempts as $attempt) {
            $attemptoptions = game_get_reviewoptions($game, $attempt, $context);
            $row = array();

            // Add the attempt number, making it a link, if appropriate.
            if ($attemptcolumn) {
                if ($attempt->preview) {
                    $row[] = get_string('preview', 'game');
                } else {
                    $row[] = $attempt->attempt;
                }
            }

            // prepare strings for time taken and date completed
            $timetaken = '';
            $datecompleted = '';
            if ($attempt->timefinish > 0) {
                // attempt has finished
                $timetaken = format_time($attempt->timefinish - $attempt->timestart);
                $datecompleted = userdate($attempt->timefinish);
            } else
            {
                // The a is still in progress.
                $timetaken = format_time($timenow - $attempt->timestart);
                $datecompleted = '';
            }
            $row[] = $datecompleted;

            // Ouside the if because we may be showing feedback but not grades. bdaloukas
            $attemptgrade = game_score_to_grade($attempt->score, $game);

            if ($gradecolumn) {
                if ($attemptoptions->scores && $attempt->timefinish > 0) {
                    $formattedgrade = game_format_grade($game, $attemptgrade);
                    // highlight the highest grade if appropriate
                    if ($overallstats && !$attempt->preview && $numattempts > 1 && !is_null($mygrade) &&
                            $attemptgrade == $mygrade && $game->grademethod == QUIZ_GRADEHIGHEST) {
                        $table->rowclasses[$attempt->attempt] = 'bestrow';
                    }

                    $row[] = $formattedgrade;
                } else {
                    $row[] = '';
                }
            }

            $row[] = $timetaken;

            if ($attempt->preview) {
                $table->data['preview'] = $row;
            } else {
                $table->data[$attempt->attempt] = $row;
            }
        } // End of loop over attempts.
        echo html_writer::table($table);
    }

/// Print information about the student's best score for this game if possible.


    if ($numattempts && $gradecolumn && !is_null($mygrade)) {
        $resultinfo = '';

        if ($overallstats) {
            $a = new stdClass;
            $a->grade = game_format_grade($game, $mygrade);
            $a->maxgrade = game_format_grade($game, $game->grade);
            $a = get_string('outofshort', 'quiz', $a);
            $resultinfo .= $OUTPUT->heading(get_string('yourfinalgradeis', 'game', $a), 2, 'main');
        }

        if ($mygradeoverridden) {
            $resultinfo .= '<p class="overriddennotice">'.get_string('overriddennotice', 'grades')."</p>\n";
        }
        if ($gradebookfeedback) {
            $resultinfo .= $OUTPUT->heading(get_string('comment', 'game'), 3, 'main');
            $resultinfo .= '<p class="gameteacherfeedback">'.$gradebookfeedback."</p>\n";
        }

        if ($resultinfo) {
            echo $OUTPUT->box($resultinfo, 'generalbox', 'feedback');
        }
    }

/// Determine if we should be showing a start/continue attempt button,
/// or a button to go back to the course page.
    echo $OUTPUT->box_start('gameattempt');
    $buttontext = ''; // This will be set something if as start/continue attempt button should appear.

    if ($unfinished) {
        if ($canattempt) {
            $buttontext = get_string('continueattemptgame', 'game');
        }
    } else {
        if ($canattempt) {
            if ($numattempts == 0) {
                $buttontext = get_string('attemptgamenow', 'game');
            } else {
                $buttontext = get_string('reattemptgame', 'game');
            }
        }
    }

/// Now actually print the appropriate button.
    if ($buttontext) {

        global $OUTPUT;

        $strconfirmstartattempt = '';

    /// Show the start button, in a div that is initially hidden.
        echo '<div id="gamestartbuttondiv">';
        $url = new moodle_url($CFG->wwwroot.'/mod/game/attempt.php', array('id' => $id));
        $button = new single_button($url, $buttontext);
        echo $OUTPUT->render($button);
        echo "</div>\n";
    } else {
        echo $OUTPUT->continue_button($CFG->wwwroot . '/course/view.php?id=' . $course->id);
    }
    echo $OUTPUT->box_end();

    echo $OUTPUT->footer();
