<?php

/**
 * Class-QuickSpoiler.php
 *
 * @package Quick Spoiler
 * @link https://custom.simplemachines.org/mods/index.php?mod=2940
 * @author Bugo https://dragomano.ru/mods/quick-spoiler
 * @copyright 2011-2021 Bugo
 * @license https://opensource.org/licenses/BSD-3-Clause BSD
 *
 * @version 1.4
 */

if (!defined('SMF'))
	die('Hacking attempt...');

class QuickSpoiler
{
	/**
	 * Used hooks
	 *
	 * @return void
	 */
	public static function hooks()
	{
		add_integration_function('integrate_load_theme', __CLASS__ . '::loadTheme', false, __FILE__);
		add_integration_function('integrate_load_permissions', __CLASS__ . '::loadPermissions', false, __FILE__);
		add_integration_function('integrate_bbc_codes', __CLASS__ . '::bbcCodes', false, __FILE__);
		add_integration_function('integrate_bbc_buttons', __CLASS__ . '::bbcButtons', false, __FILE__);
		add_integration_function('integrate_prepare_display_context', __CLASS__ . '::prepareDisplayContext', false, __FILE__);
		add_integration_function('integrate_general_mod_settings', __CLASS__ . '::generalModSettings', false, __FILE__);
	}

	/**
	 * Languages, css & js
	 *
	 * @return void
	 */
	public static function loadTheme()
	{
		global $context;

		loadLanguage('QuickSpoiler/');

		if (isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'showoperations')
			return;

		loadCSSFile('quick_spoiler.css');

		if ($context['right_to_left'])
			addInlineCss('
		.sp-head {
			padding-right: 10px;
			text-align: right;
		}
		.sp-foot {
			text-align: left;
		}');

		if (!in_array($context['current_action'], array('helpadmin', 'printpage')))
			loadJavaScriptFile('quick_spoiler.js', array('minimize' => true));
	}

	/**
	 * Spoiler permissions
	 *
	 * @param array $permissionGroups
	 * @param array $permissionList
	 * @return void
	 */
	public static function loadPermissions(&$permissionGroups, &$permissionList)
	{
		$permissionList['membergroup']['view_spoiler'] = array(false, 'general', 'view_basic_info');
	}

	/**
	 * Spoiler tag
	 *
	 * @param array $codes
	 * @return void
	 */
	public static function bbcCodes(&$codes)
	{
		global $sourcedir, $modSettings, $txt;

		if (!function_exists('allowedTo'))
			require_once($sourcedir . '/Security.php');

		loadLanguage('QuickSpoiler/');

		// Remove another spoiler tag
		foreach ($codes as $tag => $dump) {
			if ($dump['tag'] == 'spoiler')
				unset($codes[$tag]);
		}

		$style = !empty($modSettings['qs_bgcolor']) ? $modSettings['qs_bgcolor'] : 'default';

		// Our spoiler tag
		if (allowedTo('view_spoiler')) {
			$codes[] = array(
				'tag'         => 'spoiler',
				'before'      => '<details class="sp-wrap sp-wrap-' . $style . '"><summary class="sp-head">' . (!empty($modSettings['qs_title']) ? $modSettings['qs_title'] : $txt['quick_spoiler']) . '</summary><div class="sp-body">',
				'after'       => '<div class="sp-foot">' . $txt['qs_footer'] . '</div></div></details>',
				'block_level' => true
			);
			$codes[] = array(
				'tag'         => 'spoiler',
				'type'        => 'parsed_equals',
				'before'      => '<details class="sp-wrap sp-wrap-' . $style . '"><summary class="sp-head">$1</summary><div class="sp-body">',
				'after'       => '<div class="sp-foot">' . $txt['qs_footer'] . '</div></div></details>',
				'block_level' => true
			);
		} else {
			$codes[] = array(
				'tag'      => 'spoiler',
				'type'     => 'unparsed_content',
				'content'  => '<div class="errorbox centertext">' . $txt['qs_no_spoiler_sorry'] . '</div>',
				'validate' => function (&$tag, &$data) {
					unset($data);
				},
				'disabled_content' => '<div class="errorbox centertext">' . $txt['qs_no_spoiler_sorry'] . '</div>'
			);
			$codes[] = array(
				'tag'      => 'spoiler',
				'type'     => 'unparsed_equals_content',
				'content'  => '<div class="errorbox centertext">' . $txt['qs_no_spoiler_sorry'] . '</div>',
				'validate' => function (&$tag, &$data) {
					unset($data);
				},
				'disabled_content' => '<div class="errorbox centertext">' . $txt['qs_no_spoiler_sorry'] . '</div>'
			);
		}
	}

	/**
	 * Add spoiler button
	 *
	 * @param array $buttons
	 * @return void
	 */
	public static function bbcButtons(&$buttons)
	{
		global $txt;

		if (allowedTo('view_spoiler')) {
			$buttons[count($buttons) - 1][] = array(
				'image'       => 'spoiler',
				'code'        => 'spoiler',
				'before'      => '[spoiler]',
				'after'       => '[/spoiler]',
				'description' => $txt['quick_spoiler']
			);
		}
	}

	/**
	 * Remove [/spoiler] tails for nested spoilers
	 *
	 * @param array $output
	 * @return void
	 */
	public static function prepareDisplayContext(&$output)
	{
		if (allowedTo('view_spoiler'))
			return;

		if (strpos($output['body'], '[/spoiler]') !== false)
			$output['body'] = strtr($output['body'], array('[/spoiler]' => ''));
	}

	/**
	 * Spoiler settings
	 *
	 * @param array $config_vars
	 * @return void
	 */
	public static function generalModSettings(&$config_vars)
	{
		global $modSettings, $txt;

		if (isset($config_vars[0]))
			$config_vars[] = array('title', 'qs_settings');

		if (empty($modSettings['qs_title']))
			updateSettings(array('qs_title' => $txt['quick_spoiler']));

		$config_vars[] = array('text', 'qs_title');
		$config_vars[] = array('select', 'qs_bgcolor', $txt['qs_colors']);
		$config_vars[] = array('permissions', 'view_spoiler');
	}
}
