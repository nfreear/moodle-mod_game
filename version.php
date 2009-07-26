<?php // $Id: version.php,v 1.24 2009/07/26 18:01:49 bdaloukas Exp $
/**
 * Code fragment to define the version of game
 * This fragment is called by moodle_needs_upgrading() and /admin/index.php
 *
 * @author 
 * @version $Id: version.php,v 1.24 2009/07/26 18:01:49 bdaloukas Exp $
 * @package game
 **/

$module->version  = 2009072601;  // The current module version (Date: YYYYMMDDXX)
$module->release = 'v2'.substr( $module->version, 4);
$module->cron     = 0;           // Period for cron to check this module (secs)

?>
