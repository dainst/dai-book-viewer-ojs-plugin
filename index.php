<?php

/**
 * @defgroup plugins_viewableFile_daiBookViewer
 */

/**
 * @file plugins/viewableFile/daiBookViewer/index.php
 *
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_viewableFile_daiBookViewer
 * @brief Wrapper for pdf.js-based viewer.
 *
 */

require_once('DaiBookViewer.inc.php');
return new DaiBookViewer();

?>
