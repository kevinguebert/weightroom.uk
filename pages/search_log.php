<?php
if (!$user->is_logged_in())
{
	print_message('You are not loged in', '?page=login');
	exit;
}

require INCDIR . 'class_log.php';
$log = new log();

$show = (isset($_GET['show'])) ? $_GET['show'] : 1;
$exercise_name = (isset($_GET['exercise'])) ? $_GET['exercise'] : '';
$weight = (isset($_GET['weight'])) ? $_GET['weight'] : 0;
$reps = (isset($_GET['reps'])) ? $_GET['reps'] : '';

$exercises = $log->list_exercises($user->user_id);
foreach ($exercises as $exercise)
{
	$template->assign_block_vars('exercise', array(
			'EXERCISE' => ucwords($exercise['exercise_name']),
			'SELECTED' => (strtolower($exercise_name) == $exercise['exercise_name'])
			));
}

if ($exercise_name != '' && $log->is_valid_exercise($user->user_id, $exercise_name))
{
	// get current pr
	$pr_data = $log->get_prs($user->user_id, date("Y-m-d"), $exercise_name);
	// build a reference for current 1rm
	$pr_weight = max($pr_data);
	$reps = array_search($pr_weight, $pr_data);
	$current_1rm = $log->generate_rm($pr_weight, $reps);

	$params = array();
	$rep_sql = '';
	if ($reps != 'any' && $reps != '')
	{
		$rep_sql = ' AND i2.logitem_reps = :reps';
		$params[] = array(':reps', $reps, 'int');
	}

	// get results subquery limits dont work :(
	$query = "SELECT i.*, lx.logex_volume, lx.logex_reps, lx.logex_sets, lx.logex_comment, lx.logex_1rm, e.exercise_name
			FROM log_items AS i
			LEFT JOIN exercises e ON ( e.exercise_id = i.exercise_id ) 
			LEFT JOIN log_exercises As lx ON (lx.log_id = i.log_id AND e.exercise_id = lx.exercise_id)
			WHERE i.log_id
			IN (
				SELECT i2.log_id
				FROM log_items i2
				LEFT JOIN exercises ex ON ( ex.exercise_id = i2.exercise_id ) 
				WHERE i2.user_id = :user_id
				AND i2.logitem_weight = :weight
				$rep_sql
				AND ex.exercise_name =  :exercise_name_one
			)
			AND e.exercise_name =  :exercise_name_two
			ORDER BY logitem_date DESC , logitem_id ASC";
	$params[] = array(':user_id', $user->user_id, 'int');
	$params[] = array(':exercise_name_one', $exercise_name, 'str');
	$params[] = array(':exercise_name_two', $exercise_name, 'str');
	$params[] = array(':weight', $weight, 'int');
	$params[] = array(':show', $show, 'str');
	$db->query($query, $params);
	$data = array();
	while ($log = $db->fetch())
	{
		if (!isset($data[$log['log_id']]))
		{
			// check to see if limit is reached
			if (count($data) >= $show && $show != 0)
			{
				break;
			}
			$data[$log['log_id']] = 1;
			$template->assign_block_vars('items', array(
					'LOG_DATE' => $log['logitem_date'],
					'VOLUME' => correct_weight($log['logex_volume'], 'kg', $user->user_data['user_unit']),
					'REPS' => $log['logex_reps'],
					'SETS' => $log['logex_sets'],
					'COMMENT' => $log['logex_comment'],
					));
		}
		$showunit = true;
		$log['logitem_weight'] = correct_weight($log['logitem_weight'], 'kg', $user->user_data['user_unit']);
		if ($log['is_bw'] == 0)
		{
			$rep_weight = $log['logitem_weight'];
		}
		else
		{
			if ($log['logitem_weight'] != 0)
			{
				if ($log['logitem_weight'] < 0)
				{
					$rep_weight = 'BW - ' . abs($log['logitem_weight']);
				}
				else
				{
					$rep_weight = 'BW + ' . $log['logitem_weight'];
				}
			}
			else
			{
				$rep_weight = 'BW';
				$showunit = false;
			}
		}
		$template->assign_block_vars('items.sets', array(
				'WEIGHT' => $rep_weight,
				'REPS' => $log['logitem_reps'],
				'SETS' => $log['logitem_sets'],
				'RPES' => $log['logitem_rpes'],
				'COMMENT' => $log['logitem_comment'],
				'IS_PR' => $log['is_pr'],
				'SHOW_UNIT' => $showunit,
				'EST1RM' => correct_weight($log['logitem_1rm'], 'kg', $user->user_data['user_unit']),
				));
		$average_intensity = (($log['logex_volume']/$log['logex_reps'])/$current_1rm) * 100;
		$template->alter_block_array('items', array('AVG_INT' => round($average_intensity, 1)), true, 'change');
	}
}

$template->assign_vars(array(
		'SHOW' => $show,
		'WEIGHT' => $weight,
		'REPS' => $reps,
		));
$template->set_filenames(array(
		'body' => 'search_log.tpl'
		));
$template->display('header');
$template->display('body');
$template->display('footer');
?>