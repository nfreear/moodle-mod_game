<?php // $Id: version.php,v 1.36 2010/07/26 00:07:14 bdaloukas Exp $
/**
 * Code fragment to define the version of game
 * This fragment is called by moodle_needs_upgrading() and /admin/index.php
 *
 * @author 
 * @version $Id: version.php,v 1.36 2010/07/26 00:07:14 bdaloukas Exp $
 * @package game
 **/

$module->version  = 2010071612;  // The current module version (Date: YYYYMMDDXX)
$module->requires = 2009041700;  // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)
