<?php

/**
 * Class-QuickSpoiler.php
 *
 * @package Quick Spoiler
 * @link https://custom.simplemachines.org/mods/index.php?mod=2940
 * @author Bugo https://dragomano.ru/mods/quick-spoiler
 * @copyright 2011-2019 Bugo
 * @license https://creativecommons.org/licenses/by-sa/4.0/ CC BY-SA 4.0
 *
 * @version 1.2.2
 */

if (!defined('SMF'))
	die('Hacking attempt...');

class QuickSpoiler
{
	/**
	 * Подключаем необходимые хуки
	 *
	 * @return void
	 */
	public static function hooks()
	{
		add_integration_function('integrate_load_theme', 'QuickSpoiler::loadTheme', false);
		add_integration_function('integrate_load_permissions', 'QuickSpoiler::loadPermissions', false);
		add_integration_function('integrate_bbc_codes', 'QuickSpoiler::bbcCodes', false);
		add_integration_function('integrate_bbc_buttons', 'QuickSpoiler::bbcButtons', false);
		add_integration_function('integrate_buffer', 'QuickSpoiler::buffer', false);
		add_integration_function('integrate_general_mod_settings', 'QuickSpoiler::generalModSettings', false);
	}

	public static function loadTheme()
	{
		global $context, $settings;

		loadLanguage('QuickSpoiler/');

		if (isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'showoperations')
			return;

		loadTemplate(false, 'quick_spoiler');

		if (!in_array($context['current_action'], array('helpadmin', 'printpage')) && (defined('WIRELESS') && !WIRELESS)) {
			$context['insert_after_template'] .= '
		<script type="text/javascript">window.jQuery || document.write(unescape(\'%3Cscript src="//code.jquery.com/jquery.min.js"%3E%3C/script%3E\'))</script>
		<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/quick_spoiler.js"></script>';
		}
	}

	public static function loadPermissions(&$permissionGroups, &$permissionList)
	{
		$permissionList['membergroup']['view_spoiler'] = array(false, 'general', 'view_basic_info');
	}

	public static function bbcCodes(&$codes)
	{
		global $modSettings, $txt;

		// Remove another spoiler tag
		foreach ($codes as $tag => $dump) {
			if ($dump['tag'] == 'spoiler')
				unset($codes[$tag]);
		}

		$style = !empty($modSettings['qs_bgcolor']) ? $modSettings['qs_bgcolor'] : 'default';

		$state = 'folded';

		$head_class = $state == 'folded' ? '' : ' unfolded';
		$body_class = $state == 'folded' ? ' folded' : '';

		// Our spoiler tag
		if (allowedTo('view_spoiler')) {
			$codes[] = array(
				'tag'         => 'spoiler',
				'before'      => '<div class="sp-wrap sp-wrap-' . $style . '"><div class="sp-head' . $head_class . '">' . (!empty($modSettings['qs_title']) ? $modSettings['qs_title'] : $txt['quick_spoiler']) . '</div><div class="sp-body' . $body_class . '">',
				'after'       => '<div class="sp-foot">' . $txt['qs_footer'] . '</div></div></div>',
				'block_level' => true
			);
			$codes[] = array(
				'tag'         => 'spoiler',
				'type'        => 'parsed_equals',
				'before'      => '<div class="sp-wrap sp-wrap-' . $style . '"><div class="sp-head' . $head_class . '">$1</div><div class="sp-body' . $body_class . '">',
				'after'       => '<div class="sp-foot">' . $txt['qs_footer'] . '</div></div></div>',
				'block_level' => true
			);
		} else {
			$codes[] = array(
				'tag'         => 'spoiler',
				'type'        => 'unparsed_content',
				'content'     => '<div class="sp-wrap sp-wrap-' . $style . ' centertext">' . $txt['qs_no_spoiler_sorry'] . '</div>',
				'block_level' => false
			);
			$codes[] = array(
				'tag'         => 'spoiler',
				'type'        => 'unparsed_equals_content',
				'content'     => '<div class="sp-wrap sp-wrap-' . $style . ' centertext">' . $txt['qs_no_spoiler_sorry'] . '</div>',
				'block_level' => false
			);
		}
	}

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

	public static function buffer($buffer)
	{
		global $context, $settings;

		if (isset($_REQUEST['xml']) || $context['current_action'] == 'printpage')
			return $buffer;

		$spoiler = 'sImage: ' . JavaScriptEscape($settings['images_url'] . '/bbc/spoiler.gif');
		$default = 'sImage: ' . JavaScriptEscape($settings['default_images_url'] . '/bbc/spoiler.gif');
		$replacements[$spoiler] = $default;

		if (!allowedTo('view_spoiler')) {
			$find = '[/spoiler]';
			$repl = '';
			$replacements[$find] = $repl;
		}

		return str_replace(array_keys($replacements), array_values($replacements), $buffer);
	}

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