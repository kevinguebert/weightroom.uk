<?php
if (!$user->is_logged_in())
{
	print_message('You are not loged in', '?page=login');
	exit;
}

require INCDIR . 'class_log.php';
$log = new log();

$log_date = (isset($_GET['date'])) ? $_GET['date'] : date("Y-m-d");

if (!isset($_GET['do']) || (isset($_GET['do']) && $_GET['do'] == 'view'))
{
	$user_id = (isset($_GET['user_id'])) ? $_GET['user_id'] : $user->user_id;

	if (!$user->is_valid_user($user_id))
	{
		print_message('No user exists', '?page=log');
		exit;
	}

	// deal with the follows
	if (isset($_GET['follow']))
	{
		if ($_GET['follow'] == 'false')
		{
			$user->deletefollower($user_id);
		}
		elseif ($_GET['follow'] == 'true')
		{
			$user->addfollower($user_id);
		}
	}

	$log_list = $log->load_log_list($user_id, $log_date);

	$log_data = $log->get_log_data($user_id, $log_date);

	// loop through the exercises
	$total_volume = $total_reps = $total_sets = $total_intensity = 0;
	$exercise_count = count($log_data);
	foreach ($log_data as $log_items)
	{
		$total_volume += $log_items['total_volume'];
		$total_reps += $log_items['total_reps'];
		$total_sets += $log_items['total_sets'];
		// get current pr
		$pr_data = $log->get_prs($user_id, $log_date, $log_items['exercise']);
		// build a reference for current 1rm
		$pr_weight = max($pr_data);
		$reps = array_search($pr_weight, $pr_data);
		$current_1rm = $log->generate_rm($pr_weight, $reps);
		$average_intensity = $log->get_average_intensity($log_items['total_volume'], $log_items['total_reps'], $log_items['sets'], $current_1rm);
		$total_intensity += $average_intensity;
		$template->assign_block_vars('items', array(
				'EXERCISE' => ucwords($log_items['exercise']),
				'VOLUME' => $log_items['total_volume'],
				'REPS' => $log_items['total_reps'],
				'SETS' => $log_items['total_sets'],
				'AVG_INT' => $average_intensity,
				'COMMENT' => trim($log_items['comment']),
				));
		foreach ($log_items['sets'] as $set)
		{
			$showunit = true;
			if ($set['is_time'] == 1)
			{
				$weight = format_time($set['logitem_time']);
				$showunit = false;
			}
			elseif ($set['is_bw'] == 0)
			{
				$weight = $set['logitem_weight'];
			}
			else
			{
				if ($set['logitem_weight'] != 0)
				{
					if ($set['logitem_weight'] < 0)
					{
						$weight = 'BW - ' . abs($set['logitem_weight']);
					}
					else
					{
						$weight = 'BW + ' . $set['logitem_weight'];
					}
				}
				else
				{
					$weight = 'BW';
					$showunit = false;
				}
			}
			$template->assign_block_vars('items.sets', array(
					'WEIGHT' => $weight,
					'REPS' => $set['logitem_reps'],
					'SETS' => $set['logitem_sets'],
					'RPES' => $set['logitem_rpes'],
					'IS_PR' => $set['is_pr'],
					'IS_TIME' => $set['is_time'],
					'SHOW_UNIT' => $showunit,
					'COMMENT' => trim($set['logitem_comment']),
					'EST1RM' => $set['est1rm'],
					));
		}
	}
	$log_ic = $log->load_log($user_id, $log_date, 'log_comment, log_id, log_weight');

	require INCDIR . 'class_comments.php';
	$log_comments = new comments();
	// deal with the comments
	$commenting = false;
	if (isset($_POST['log_id']))
	{
		$parent_id = (intval($_POST['parent_id']) == 0) ? NULL : $_POST['parent_id'];
		$log_comments->make_comment($parent_id, $_POST['comment'], $_POST['log_id'], $log_date, $user_id);
		$commenting = true;
	}

	$log_comments->load_log_comments($log_ic['log_id']);
	$log_comments->print_comments();

	// get user info
	$user_data = $user->get_user_data($user_id);
	//create badges
	$badges = '';
	if ($user_data['user_gender'] == 0)
		$badges .= '<img src="img/female.png" alt="Woman">';
	if ($user_data['user_gender'] == 1)
		$badges .= '<img src="img/male.png" alt="Man">';
	if ($user_data['user_beta'] == 1)
		$badges .= '<img src="img/bug.png" alt="Beta tester">';
	if ($user_data['user_admin'] == 1)
		$badges .= '<img src="img/star.png" alt="Adminnosaurus Rex">';

	$timestamp = strtotime($log_date . ' 00:00:00');
	$template->assign_vars(array(
		'WEEK_START' => $user->user_data['user_weekstart'],
		'LOG_DATES' => $log->build_log_list($log_list),
		'USER_ID' => $user_id,
		'USERNAME' => $user_data['user_name'],
		'USER_BW' => $log_ic['log_weight'],

		'B_NOSELF' => ($user_id != $user->user_id),
		'B_FOLLOWING' => $user->is_following($user_id),
		'BADGES' => $badges,
		'JOINED' => $user_data['user_joined'],

		'TOTAL_VOLUME' => $total_volume,
		'TOTAL_REPS' => $total_reps,
		'TOTAL_SETS' => $total_sets,
		'TOTAL_INT' => ($exercise_count > 0) ? round($total_intensity/$exercise_count, 1) : 0,

		'AVG_INTENSITY_TYPE' => $user->user_data['user_viewintensityabs'],
		'B_LOG' => (!(empty($log_data) && empty($log_ic['log_comment']))),
		'COMMENT' => $log_ic['log_comment'],
		'DATE' => $log_date,
		'TOMORROW' => date("Y-m-d", $timestamp + 86400),
		'YESTERDAY' => date("Y-m-d", $timestamp - 86400),
		'LOG_ID' => $log_ic['log_id'],
		'LOG_COMMENTS' => $log_comments->comments,
		'COMMENTING' => $commenting
		));
	$template->set_filenames(array(
			'body' => 'log_view_beta.tpl'
			));
	$template->display('header');
	$template->display('body');
	$template->display('footer');
}
// to add a log or edit a log
elseif ($_GET['do'] == 'edit')
{
	$error = false;
	$log_text = '';
	// has anything been submitted?
	if (isset($_POST['log']))
	{
		// get user weight
		if (!isset($_POST['weight']) || strlen($_POST['weight']) == 0 || intval($_POST['weight']) == 0)
		{
			$query = "SELECT log_weight FROM logs WHERE log_date < :log_date AND user_id = :user_id ORDER BY log_date DESC LIMIT 1";
			$params = array(
				array(':user_id', $user->user_id, 'int'),
				array(':log_date', $log_date, 'str')
			);
			$db->query($query, $params);
			if ($db->numrows() == 1)
			{
				$weight = $db->result('log_weight');
			}
			else
			{
				$weight = $user->user_data['user_weight'];
			}
		}
		else
		{
			$weight = floatval($_POST['weight']);
		}
		// set log text
		$log_text = trim($_POST['log']);
		// load the parser
		require INCDIR . 'class_parser.php';
		$parser = new parser();
		// parse the log
		$parser->parse_text ($log_text);
		$new_prs = $parser->store_log_data ($log_date, $weight);
		// check if there are prs
		if (is_array($new_prs) && count($new_prs) > 0)
		{
			$pr_string = '';
			foreach ($new_prs as $exercise => $reps)
			{
				// for weight
				foreach ($reps['W'] as $rep => $weights)
				{
					foreach ($weights as $weight)
					{
						// TODO: move this into user class
						$unit = ($user->user_data['user_unit'] == 2) ? 'lb' : 'kg';
						$pr_string .= "<p>You have set a new <strong>$exercise {$rep}RM</strong> of <strong>$weight</strong> $unit</p>";
					}
				}
				// for time
				foreach ($reps['T'] as $rep => $times)
				{
					foreach ($times as $time)
					{
						$time = format_time($time);
						$pr_string .= "<p>You have set a new <strong>$exercise {$rep}RM</strong> of <strong>$time</strong></p>";
					}
				}
			}
			print_message($pr_string);
		}
		print_message('Log processed', '?page=log&do=view&date=' . $log_date);
	}
	// editing a log? try to load the old data
	// check log is real
	$valid_log = $log->is_valid_log ($user->user_id, $log_date);
	if($valid_log)
	{
		// load log data
		$log_data = $log->load_log ($user->user_id, $log_date);
		$log_text = $log_data['log_text'];
		$weight = $log_data['log_weight'];
		// check if log_text needs to be rewitten
		if ($log_data['log_update_text'] == 1)
		{
			$log_text = $log->rebuild_log_text ($user->user_id, $log_date);
		}
	}
	else
	{
		$log_text = '';
		$weight = $log->get_user_weight ($user->user_id, $log_date);
	}

	// build exercise list for editor hints
	$exercises = $log->list_exercises($user->user_id);
	$elist = '';
	foreach ($exercises as $exercise)
	{
		$elist .= "[\"{$exercise['exercise_name']}\", {$exercise['COUNT']}],";
	}
	$template->assign_vars(array(
		'WEEK_START' => $user->user_data['user_weekstart'],
		'LOG' => (isset($_POST['log'])) ? $_POST['log'] : $log_text,
		'WEIGHT' => correct_weight($weight, 'kg', $user->user_data['user_unit']),
		'DATE' => $log_date,
		'USER_ID' => $user->user_id,
		'ERROR' => $error,
		'VALID_LOG' => $valid_log,
		'EXERCISE_LIST' => substr($elist, 0, -1)
		));
	$template->set_filenames(array(
			'body' => 'log_edit_beta.tpl'
			));
	$template->display('header');
	$template->display('body');
	$template->display('footer');
}
?>
