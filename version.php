<?php // $Id: version.php,v 1.40 2010/09/03 12:41:55 bdaloukas Exp $
/**
 * Code fragment to define the version of game
 * This fragment is called by moodle_needs_upgrading() and /admin/index.php
 *
 * @author 
 * @version $Id: version.php,v 1.40 2010/09/03 12:41:55 bdaloukas Exp $
 * @package game
 **/

$module->version  = 2010090301;  // The current module version (Date: YYYYMMDDXX)
$module->requires = 2009041700;  // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)
