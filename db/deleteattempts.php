<?php

require( "../../../config.php");

require_login();
if( !isadmin( $USER->id)){
	error( "Only administrators can truncates tables of attempts");
}


$tables = array( "game_hangman", "game_cryptex", "game_cross", "game_millionaire",
					"game_sudoku",  "game_grades", "game_snakes",
					"game_bookquiz", "game_attempts", "game_queries", 'game_repetitions', 'game_hiddenpicture',
                    'game_export_html', 'game_export_javame');

foreach( $tables as $table){
    echo $table.' ';
	execute_sql( "truncate TABLE {$CFG->prefix}$table");
    echo '<br>';
}
