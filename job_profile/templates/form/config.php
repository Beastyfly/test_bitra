<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'script.min.css',
	'js' => 'script.min.js',
	'rel' => [
		'main.polyfill.core',
	],
	'skip_core' => true,
];