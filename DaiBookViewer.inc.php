<?php

/**
 * @file plugins/generic/pdfJsViewer/DaiBookViewer.inc.php
 *
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class DaiBookViewer
 *
 * @brief This plugin enables embedding of the pdf.js viewer for PDF display
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class DaiBookViewer extends GenericPlugin {
	/**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path, $mainContextId = null) {
		if (parent::register($category, $path, $mainContextId)) {
			if ($this->getEnabled($mainContextId)) {
				HookRegistry::register('ArticleHandler::view::galley', array($this, 'articleCallback'), HOOK_SEQUENCE_LAST);
				HookRegistry::register('IssueHandler::view::galley', array($this, 'issueCallback'), HOOK_SEQUENCE_LAST);
				HookRegistry::register('CatalogBookHandler::view', array($this, 'bookCallback'), HOOK_SEQUENCE_LATE);

				AppLocale::requireComponents(LOCALE_COMPONENT_APP_COMMON);
			}
			return true;
		}
		return false;
	}

	/**
	 * Install default settings on journal creation.
	 * @return string
	 */
	function getContextSpecificPluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	/**
	 * @copydoc Plugin::getDisplayName
	 */
	function getDisplayName() {
		return __('plugins.generic.daiBookViewer.name');
	}

	/**
	 * @copydoc Plugin::getDescription
	 */
	function getDescription() {
		return __('plugins.generic.daiBookViewer.description');
	}

	/**
	 * Callback that renders the article galley.
	 * @param $hookName string
	 * @param $args array
	 * @return boolean
	 */
	function articleCallback($hookName, $args) {
		$request =& $args[0];
		$issue =& $args[1];
		$galley =& $args[2];
		$article =& $args[3];

		$templateMgr = TemplateManager::getManager($request);
		if ($galley && $galley->getFileType() == 'application/pdf') {
			$application = Application::getApplication();
			$templateMgr->assign(array(
				'displayTemplateResource' => $this->getTemplateResource('display.tpl'),
				'pluginUrl' => $request->getBaseUrl() . '/' . $this->getPluginPath(),
				'galleyFile' => $galley->getFile(),
				'issue' => $issue,
				'article' => $article,
				'galley' => $galley,
				'jQueryUrl' => $this->_getJQueryUrl($request),
				'currentVersionString' => $application->getCurrentVersion()->getVersionString(false),
			));
			$templateMgr->display($this->getTemplateResource('articleGalley.tpl'));
			return true;
		}

		return false;
	}

	/**
	 * Callback that renders the issue galley.
	 * @param $hookName string
	 * @param $args array
	 * @return boolean
	 */
	function issueCallback($hookName, $args) {
		$request =& $args[0];
		$issue =& $args[1];
		$galley =& $args[2];

		$templateMgr = TemplateManager::getManager($request);
		if ($galley && $galley->getFileType() == 'application/pdf') {
			$application = Application::getApplication();
			$templateMgr->assign(array(
				'displayTemplateResource' => $this->getTemplateResource('display.tpl'),
				'pluginUrl' => $request->getBaseUrl() . '/' . $this->getPluginPath(),
				'galleyFile' => $galley->getFile(),
				'issue' => $issue,
				'galley' => $galley,
				'jQueryUrl' => $this->_getJQueryUrl($request),
				'currentVersionString' => $application->getCurrentVersion()->getVersionString(false),
			));
			$templateMgr->display($this->getTemplateResource('issueGalley.tpl'));
			return true;
		}

		return false;
	}

	/**
	 * Callback that renders the book pdf.
	 * @param $hookName string
	 * @param $args array
	 * @return boolean
	 */
	function bookCallback($hookName, $args) {
		$publishedMonograph =& $args[1];
		$publicationFormat =& $args[2];
		$submissionFile =& $args[3];

		if ($submissionFile->getFileType() == 'application/pdf') {
			$request = Application::getRequest();
			$router = $request->getRouter();
			$dispatcher = $request->getDispatcher();
			$templateMgr = TemplateManager::getManager($request);
			$templateMgr->assign(array(
				'pluginUrl' => $request->getBaseUrl() . DIRECTORY_SEPARATOR . $this->getPluginPath(),
				'pdfUrl' => str_replace("view", "download", $request->getRequestPath())
			));

			$templateMgr->display($this->getTemplateResource('display.tpl'));
			return true;
		}
		return false;
	}

	/**
	 * Get the URL for JQuery JS.
	 * @param $request PKPRequest
	 * @return string
	 */
	private function _getJQueryUrl($request) {
		$min = Config::getVar('general', 'enable_minified') ? '.min' : '';
		if (Config::getVar('general', 'enable_cdn')) {
			return '//ajax.googleapis.com/ajax/libs/jquery/' . CDN_JQUERY_VERSION . '/jquery' . $min . '.js';
		} else {
			return $request->getBaseUrl() . '/lib/pkp/lib/vendor/components/jquery/jquery' . $min . '.js';
		}
	}
}

?>
