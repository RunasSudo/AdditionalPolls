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

db_extend('packages');

$smcFunc['db_add_column'] (
	'{db_prefix}polls',
	array
	(
		'name' => 'id_topic',
		'type' => 'mediumint',
		'size' => 8,
		'null' => false,
		'default' => 0,
		'auto' => false,
		'unsigned' => true,
	)
);

$smcFunc['db_change_column'] (
	'{db_prefix}polls',
	'id_topic',
	array
	(
		'unsigned' => true,
	)
);

// Let's get all our indexes.
$indexes = $smcFunc['db_list_indexes']('{db_prefix}polls', true);
// Do we already have it?
foreach ($indexes as $index)
{
	if ($index['name'] == 'id_topic')
	{
		// If we want to overwrite simply remove the current one then continue.
		$smcFunc['db_remove_index']('{db_prefix}polls', 'id_topic');
	}
}

$smcFunc['db_add_index'] (
	'{db_prefix}polls',
	array(
		'columns' => array('id_topic'),
	)
);

$request = $smcFunc['db_query'] ('', '
	SELECT p.id_poll
	FROM {db_prefix}polls AS p
	WHERE p.id_topic = 0 OR p.id_topic IS NULL'
);

while($row = $smcFunc['db_fetch_assoc']($request))
{
	$request2 = $smcFunc['db_query'] ('', '
		SELECT t.id_topic
		FROM {db_prefix}topics AS t
		WHERE t.id_poll = {int:id_poll}',
		array(
			'id_poll' => (int)$row['id_poll'],
		)
	);	
	
	if($smcFunc['db_num_rows']($request2) > 0)
	{
		list($topic) = $smcFunc['db_fetch_row']($request2);
		$smcFunc['db_free_result']($request2);
		
		$smcFunc['db_query'] ('', '
			UPDATE {db_prefix}polls
			SET id_topic = {int:id_topic}
			WHERE id_poll = {int:id_poll}',
            array(
                'id_topic' => (int)$topic,
				'id_poll' => (int)$row['id_poll'],
            )
        );
	}
	else
	{
		$smcFunc['db_query'] ('', '
			DELETE FROM {db_prefix}polls
			WHERE id_poll = {int:id_poll}',
			array (
				'id_poll' => $row['id_poll'],
			)
		);		
	}
}
$smcFunc['db_free_result']($request);
?>