<?php // $Id: version.php,v 1.35 2010/07/24 02:04:29 arborrow Exp $
/**
 * Code fragment to define the version of game
 * This fragment is called by moodle_needs_upgrading() and /admin/index.php
 *
 * @author 
 * @version $Id: version.php,v 1.35 2010/07/24 02:04:29 arborrow Exp $
 * @package game
 **/

$module->version  = 2010072202;  // The current module version (Date: YYYYMMDDXX)
$module->release = 'v2'.substr( $module->version, 4);
$module->cron     = 0;           // Period for cron to check this module (secs)

?>
