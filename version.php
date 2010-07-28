<?php // $Id: version.php,v 1.38 2010/07/28 06:59:36 bdaloukas Exp $
/**
 * Code fragment to define the version of game
 * This fragment is called by moodle_needs_upgrading() and /admin/index.php
 *
 * @author 
 * @version $Id: version.php,v 1.38 2010/07/28 06:59:36 bdaloukas Exp $
 * @package game
 **/

$module->version  = 2010072801;  // The current module version (Date: YYYYMMDDXX)
$module->requires = 2009041700;  // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)
