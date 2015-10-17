<?php
/******************************************************************************
* code.php                                                           *
*******************************************************************************
* SMF: Simple Machines Forum - Additional Polls                      *
* Open-Source Project Inspired by Zef Hemel (zef@zefhemel.com)                *
* =========================================================================== *
* Software Version:           1.2                                           *
* Software by:                WWakerFAN		                                  *
* Support, News, Updates at:  http://www.simplemachines.org                   *
*******************************************************************************
* This program is free software; you may redistribute it and/or modify it     *
* under the terms of the provided license as published by Lewis Media.        *
*                                                                             *
* This program is distributed in the hope that it is and will be useful,      *
* but WITHOUT ANY WARRANTIES; without even any implied warranty of            *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                        *
*                                                                             *
* See the "license.txt" file for details of the Simple Machines license.      *
* The latest version can always be found at http://www.simplemachines.org.    *
******************************************************************************/
// If SSI.php is in the same place as this file, and SMF isn't defined, this is being run standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
// Hmm... no SSI.php and no SMF?
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

db_query("
	DELETE FROM {$db_prefix}polls
	WHERE ID_POLL NOT IN (SELECT t.ID_POLL FROM {$db_prefix}topics AS t WHERE t.ID_POLL = ID_POLL)", __FILE__, __LINE__);

db_query("
	ALTER TABLE {$db_prefix}polls
	DROP COLUMN id_topic", __FILE__, __LINE__);