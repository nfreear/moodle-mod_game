<?PHP // $Id: game.php,v 1.2 2010/07/16 21:05:24 bdaloukas Exp $ 
      // game.php - created with Moodle 1.9 (2007101509)
      // local modifications from http://localhost


$string['allattempts'] = 'Alle forsøk';
$string['allstudents'] = 'Alle studenter';
$string['analysis'] = 'Vis karakterdetaljer';
$string['and'] = 'og';
$string['attempt'] = 'Forsøk $a';
$string['attemptduration'] = 'Tid brukt';
$string['attemptfirst'] = 'Første forsøk';
$string['attemptgamenow'] = 'Prøv å spille nå';
$string['attemptlast'] = 'Siste forsøk';
$string['attempts'] = 'Forsøk';
$string['attemptsall'] = 'Alle forsøk';
$string['attemptsallowed'] = 'Det er tillatt å prøve';
$string['attemptsfirst'] = 'Første forsøk';
$string['attemptshighest'] = 'Høyeste karakter';
$string['attemptslast'] = 'Siste forsøk';
$string['attemptsonly'] = 'Bare vis studenter som har forsøkt';
$string['bookquiz_categories'] = 'Kategorier';
$string['bookquiz_chapters'] = 'Kapittel';
$string['bookquiz_empty'] = 'Boken er tom';
$string['bookquiz_import_odt'] = 'Importer fra en Open Office tekstfil (odt)';
$string['bookquiz_not_select_book'] = 'Du har ikke valgt bok';
$string['bookquiz_numquestions'] = 'Spørsmål';
$string['bookquiz_questions'] = 'Koble spørsmålskategorier til kapitler i bok';
$string['bookquiz_subchapter'] = 'Lag underkapittel';
$string['bottomtext'] = 'Bunntekst';
$string['completedon'] = 'Ferdig på';
$string['confirmstartattemptlimit'] = 'Spillet er begrenset til $a forsøk. Du holder på å starte et nytt forsøk. Vil du fortsette?';
$string['confirmstartattempttimelimit'] = 'Dette spillet har en tidsgrense og er begrenset til $a forsøk. Du holder på å starte et nytt forsøk. Vil du fortsette?';
$string['confirmstarttimelimit'] = 'Spillet har en tidsgrense. Er du sikker på at du vil starte?';
$string['continueattemptgame'] = 'Fortsette et tidligere forsøk av spillet';
$string['cross_across'] = 'Horisontal/bortover';
$string['cross_checkbutton'] = 'Sjekk kryssord';
$string['cross_congratulations'] = 'Gratulerer';
$string['cross_correct'] = 'Riktig';
$string['cross_corrects'] = 'Rettinger';
$string['cross_down'] = 'Nedover';
$string['cross_endofgamebutton'] = 'Avslutt kryssordspillet';
$string['cross_error'] = 'Feil';
$string['cross_error_containsbadchars'] = 'Ordet inneholder ulovlige tegn';
$string['cross_error_wordlength1'] = 'Det korrekte ordet inneholder';
$string['cross_error_wordlength2'] = 'tegn';
$string['cross_errors'] = 'feil';
$string['cross_found_many'] = 'Funnet';
$string['cross_found_one'] = 'Funnet';
$string['cross_incomplete_word'] = 'Ingen fullførte ord';
$string['cross_incomplete_words'] = 'Ikke fullførte ord';
$string['cross_layout'] = 'Layout';
$string['cross_layout0'] = 'Fraser på bunnen på tvers';
$string['cross_layout1'] = 'Fraser til høyre på tvers';
$string['cross_maxcols'] = 'Maksimum antall kolonner i kryssordet';
$string['cross_maxwords'] = 'Maksimum antall ord i kryssordet';
$string['cross_new'] = 'Nytt spill';
$string['cross_noerrors_but1'] = 'Ingen feil funnet men';
$string['cross_noerrors_but2_many'] = 'ord ikke fullført';
$string['cross_noerrors_but2_one'] = 'ord ikke fullført';
$string['cross_nowords'] = 'Ingen ord funnet';
$string['cross_pleasewait'] = 'Vær vennlig å vent mens kryssordet lastes';
$string['cross_space'] = 'Mellomrom';
$string['cross_spaces'] = 'Mellomrom';
$string['cross_welcome'] = '<h3>Velkommen!</h3><p>Klikk et ord for å begyne.</p>';
$string['cross_win'] = 'Gratulerer!!!';
$string['cross_word'] = 'ord';
$string['cross_words'] = 'ord';
$string['cryptex_giveanswer'] = 'Gi svar';
$string['cryptex_maxcols'] = 'Max antall kol/rader i cryptex';
$string['cryptex_maxwords'] = 'Max antall ord i cryptex';
$string['cryptex_nowords'] = 'Ingen ord funnet';
$string['cryptex_win'] = 'Gratulerer!!!';
$string['deleteattemptcheck'] = 'Er du sikkker på at du vil slette disse forsøkene helt?';
$string['displayoptions'] = 'Vis valg';
$string['downloadods'] = 'Last ned i ODS format';
$string['feedback'] = 'Tilbakemelding';
$string['feedbacks'] = 'Tilbakemelding ved riktig svar';
$string['finish'] = 'Spill slutt';
$string['formatdatetime'] = '%%d %%b %%Y, %%l: %%M %%p';
$string['game'] = 'Spill';
$string['game_bookquiz'] = 'Bok med spørsmål';
$string['game_cross'] = 'Kryssord';
$string['game_cryptex'] = 'Cryptex';
$string['game_hangman'] = 'Hangman';
$string['game_hiddenpicture'] = 'Skjult bilde';
$string['game_millionaire'] = 'Millionær';
$string['game_snakes'] = 'Slange og stiger';
$string['game_sudoku'] = 'Sudoku';
$string['gameclosed'] = 'Spillet er stengt $a';
$string['gamenotavailable'] = 'Spillet vil ikke være tilgjengelig før: $a';
$string['gametimelimit'] = 'Tidsgrense: $a';
$string['grade'] = 'Karakter';
$string['gradeaverage'] = 'Gjennomsnittelig karakter';
$string['gradehighest'] = 'Høyeste karakter';
$string['grademethod'] = 'Karaktermetode';
$string['gradesofar'] = '$a->method: $a->mygrade / $a->gamegrade';
$string['hangman_allowspaces'] = 'Tillatt mellomrom i ord';
$string['hangman_allowsub'] = 'Tillat symbolet i ord';
$string['hangman_correct_phrase'] = 'Korrekt setning var:';
$string['hangman_correct_word'] = 'Korrekt ord var:';
$string['hangman_countwords'] = 'Hvor mange ord har hvert spill?';
$string['hangman_grade'] = 'Karakter';
$string['hangman_gradeinstance'] = 'Karakter i hele spillet';
$string['hangman_imageset'] = 'Velg bilder for hangman';
$string['hangman_language'] = 'Språk for ordene';
$string['hangman_letters'] = 'Bokstaver:';
$string['hangman_loose'] = '<BIG><B>Spillet slutt</B></BIG>';
$string['hangman_maxtries'] = 'Antall ord per spill';
$string['hangman_nowords'] = 'Ingen ord funnet';
$string['hangman_restletters'] = 'Du har <b>$a</b> forsøk';
$string['hangman_restletters_many'] = 'Du har <b>$a</b> forsøk';
$string['hangman_restletters_one'] = 'Du har <b>BARE 1</b> forsøk';
$string['hangman_showcorrectanswer'] = 'Vis korrekt svar når du er ferdig';
$string['hangman_showfirst'] = 'Vis første bokstaven i hangman';
$string['hangman_showlast'] = 'Vis siste bokstaven i hangman';
$string['hangman_showquestion'] = 'Vis spørsmålet?';
$string['hangman_win'] = 'Gratulerer';
$string['hangman_wrongnum'] = 'Feil: %%d av %%d';
$string['hiddenpicture_across'] = 'Ruter horisontalt';
$string['hiddenpicture_down'] = 'Ruter nedover';
$string['hiddenpicture_finishattemptbutton'] = 'Slutt på skjultbildespillet';
$string['hiddenpicture_grade'] = 'Karakter';
$string['hiddenpicture_mainsubmit'] = 'Karakter hovedsvar';
$string['hiddenpicture_nocols'] = 'Du må angi antall kolonner horisontalt';
$string['hiddenpicture_nomainquestion'] = 'Det er ingen ordbokinnlegg i ordboken $a->name med vedlagt bilde';
$string['hiddenpicture_norows'] = 'Må angi antall kolonner vertikalt';
$string['hiddenpicture_pictureglossary'] = 'Ordbok for hovedspørsmål';
$string['hiddenpicture_pictureglossarycategories'] = 'Katgori av ordbok for hovedspørsmål';
$string['hiddenpicture_submit'] = 'Karaktersvar';
$string['hiddenpicture_win'] = 'Gratulerer';
$string['hideanswers'] = 'Skjul svarene';
$string['info'] = 'Info';
$string['lastip'] = 'IP student';
$string['letter'] = 'bokstav';
$string['letters'] = 'bokstaver';
$string['lettersall'] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZÆØÅ';
$string['marks'] = 'Markeringer';
$string['messageloose'] = 'Melding når studenten taper';
$string['messagewin'] = 'Melding når studenten vinner';
$string['millionaire_info_people'] = 'Folk sier';
$string['millionaire_info_telephone'] = 'Jeg tror korrekt svar er';
$string['millionaire_info_wrong_answer'] = 'Svaret ditt er feil</br>Riktig svar er:';
$string['millionaire_letters_answers'] = 'ABCD';
$string['millionaire_must_select_questioncategory'] = 'Du må velge en spørsmålskategori';
$string['millionaire_must_select_quiz'] = 'Du må velge en prøve';
$string['millionaire_no_questions'] = 'Ingen spørsmål funnet';
$string['millionaire_nowords'] = 'Ingen ord funnet';
$string['millionaire_sourcemodule_must_quiz_question'] = 'I \"Vil du bli millionær\" må kilden være $a eller spørsmål og ikke';
$string['millionaire_win'] = 'Gratulerer!!!';
$string['modulename'] = 'Spill';
$string['modulenameplural'] = 'Spill';
$string['must_select_glossary'] = 'Du må velge en ordbok';
$string['must_select_questioncategory'] = 'Du må velge en spørsmålskategori';
$string['must_select_quiz'] = 'Du må velge en  prøve';
$string['nextgame'] = 'Nytt spill';
$string['nextword'] = 'Neste ord';
$string['noattempts'] = 'Ingen forsøk er gjort på denne prøven';
$string['noattemptsonly'] = 'Vis $a uten noen forsøk';
$string['nomoreattempts'] = 'Ingen flere forsøk er tillatt';
$string['noscript'] = 'JavaSkript må være slått på for å fortsette';
$string['numattempts'] = '$a->studentnum $a->studentstring har gjort $a->attemptnum forsøk';
$string['only_teachers'] = 'Bare lærer kan se denne siden';
$string['outof'] = '$a->grade av makismalt $a->maxgrade';
$string['overview'] = 'Oversikt';
$string['pagesize'] = 'Spørsmål per side';
$string['popupblockerwarning'] = 'Popupvarsler';
$string['preview'] = 'Forhåndsvisning';
$string['qidtitle'] = 'SP#';
$string['qnametitle'] = 'Spørsmålsnavn';
$string['qtexttitle'] = 'Spørsmålstekst';
$string['reattemptgame'] = 'Prøv spillet igjen';
$string['regrade'] = 'Gi ny karakter på alle forsøk';
$string['reportoverview'] = 'Oversikt';
$string['reportresponses'] = 'Detaljert tilbakemeldinger';
$string['results'] = 'Resulat';
$string['review'] = 'Rapport';
$string['reviewofattempt'] = 'Rapport fra forsøk $a';
$string['rpercenttitle'] = 'R %%';
$string['score'] = 'Score';
$string['selectall'] = 'Velg alle';
$string['selectgame'] = 'Velg spill';
$string['selectnone'] = 'Ikke velg noen';
$string['showanswers'] = 'Vis svar';
$string['showdetailedmarks'] = 'Vis markeringsdetaljer';
$string['showsolution'] = 'løsning';
$string['snakes_background'] = 'Bakgrunn';
$string['snakes_new'] = 'Nytt spill';
$string['snakes_win'] = 'Gratulerer';
$string['sourcemodule'] = 'Spørsmålskilde';
$string['sourcemodule_book'] = 'Velg en bok';
$string['sourcemodule_glossary'] = 'Velg ordbok';
$string['sourcemodule_glossarycategory'] = 'Velg ordbokskategori';
$string['sourcemodule_question'] = 'Spørsmål';
$string['sourcemodule_questioncategory'] = 'Velg spørsmålskategori';
$string['sourcemodule_quiz'] = 'Velg prøve';
$string['startagain'] = 'Start på nytt';
$string['startedon'] = 'Startet';
$string['stddevtitle'] = 'SD';
$string['sudoku_create'] = 'Lage database med nye sudokus';
$string['sudoku_create_count'] = 'Antall sudokus som vil lages';
$string['sudoku_create_start'] = 'Start med å lage sudokus';
$string['sudoku_creating'] = 'Lag <b>$a</b>sudoku';
$string['sudoku_emptydatabase'] = 'Sudokudatabasen er tom. Kjør $a for å lage .';
$string['sudoku_finishattemptbutton'] = 'Slutt på sudokuspill';
$string['sudoku_guessnumber'] = 'Gjett riktig tall';
$string['sudoku_maxquestions'] = 'Maks antall spørsmål';
$string['sudoku_no_questions'] = 'Ingen spørsmål funnet';
$string['sudoku_noentriesfound'] = 'Ingen ord funnet i ordboken';
$string['sudoku_submit'] = 'Gi svarene karakter';
$string['sudoku_win'] = 'Gratulerer!!!';
$string['temporaryblocked'] = 'Du har midlertidig ikke tilgang til å prøve spillet på nytt <br/>Du vil få en ny mulighet:';
$string['timecompleted'] = 'Ferdig';
$string['timefinish'] = 'Spill slutt';
$string['timelastattempt'] = 'Siste forsøk';
$string['timestart'] = 'Start';
$string['timetaken'] = 'Tid brukt';
$string['tries'] = 'Forsøk';
$string['unfinished'] = 'åpne';
$string['withselected'] = 'Med valgte';
$string['yourfinalgradeis'] = 'Din endelige karakter for spillet er $a.';
