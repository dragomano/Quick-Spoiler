<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
	exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

$smcFunc['db_query']('', "DELETE FROM {db_prefix}permissions WHERE permission LIKE 'view_spoiler'");

$hooks = array(
	'integrate_pre_include' => '$sourcedir/Class-QuickSpoiler.php',
	'integrate_pre_load'    => 'QuickSpoiler::hooks'
);

if (!empty($context['uninstalling']))
	$call = 'remove_integration_function';
else
	$call = 'add_integration_function';

foreach ($hooks as $hook => $function)
	$call($hook, $function);
