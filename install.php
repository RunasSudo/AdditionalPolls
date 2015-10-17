<?php
/******************************************************************************
* code.php                                                           *
*******************************************************************************
* SMF: Simple Machines Forum - Additional Pols                      *
* Open-Source Project Inspired by Zef Hemel (zef@zefhemel.com)                *
* =========================================================================== *
* Software Version:           1.0                                           *
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

$columns = array();
$request = db_query("SHOW COLUMNS FROM {$db_prefix}polls",__FILE__,__LINE__);
while ($row = mysql_fetch_array($request))
{
	$columns[] = $row[0];
}
mysql_free_result($request);

if (!in_array("ID_TOPIC", $columns))
{
	db_query("ALTER TABLE {$db_prefix}polls
		ADD COLUMN ID_TOPIC mediumint(8) unsigned NOT NULL
		AFTER ID_POLL",__FILE__,__LINE__);
}

$request = db_query("SELECT * FROM {$db_prefix}polls WHERE ID_TOPIC = 0 OR ID_TOPIC IS NULL",__FILE__,__LINE__);
	
while($row = mysql_fetch_array($request))
{
	$request2 = db_query("
		SELECT ID_TOPIC
		FROM {$db_prefix}topics
		WHERE ID_POLL = $row[ID_POLL]",__FILE__,__LINE__);	
	
	if(mysql_num_rows($request2) > 0)
	{
		list($topic) = mysql_fetch_row($request2);
		mysql_free_result($request2);
		
		db_query("
			UPDATE {$db_prefix}polls
			SET ID_TOPIC = $topic
			WHERE ID_POLL = $row[ID_POLL]",__FILE__,__LINE__);
	}
	else
	{
		db_query("
			DELETE FROM {$db_prefix}polls
			WHERE ID_POLL = $row[ID_POLL]",__FILE__,__LINE__);		
	}
}
mysql_free_result($request);
?>