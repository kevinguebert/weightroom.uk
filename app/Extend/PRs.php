<?php

namespace App\Extend;

use DB;

class PRs {
    //TODO rewirte all of this
    public function fix_prs()
	{
		global $db;
		//prepare everything
		$query = "TRUNCATE exercise_records";
		$db->direct_query($query);
		$query = "UPDATE log_items SET is_pr = 0";
		$db->direct_query($query);
		// load the exercises
		$query = "SELECT exercise_id, user_id FROM exercises ORDER BY exercise_id ASC";
		$db->direct_query($query);
		$data = $db->fetchall();
		foreach($data as $row)
		{
			$query = "SELECT logitem_id, logitem_weight, logitem_reps, logitem_date FROM log_items WHERE exercise_id = :exercise_id ORDER BY logitem_date ASC";
			$params = array(
				array(':exercise_id', $row['exercise_id'], 'int')
			);
			$db->query($query, $params);
			$pr = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0);
			while($ex_data = $db->fetch())
			{
				if ($ex_data['logitem_reps'] <= 10 && $ex_data['logitem_reps'] > 0 && $pr[$ex_data['logitem_reps']] < $ex_data['logitem_weight'])
				{
					$pr[$ex_data['logitem_reps']] = $ex_data['logitem_weight'];
					$query = "UPDATE log_items SET is_pr = 1 WHERE logitem_id = :logitem_id";
					$params = array(
						array(':logitem_id', $ex_data['logitem_id'], 'int')
					);
					$db->query($query, $params);
					$query = "INSERT INTO exercise_records (exercise_id, user_id, pr_date, pr_value, pr_reps)
							VALUES (:exercise_id, :user_id, :pr_date, :pr_value, :pr_reps)";
					$params = array(
						array(':exercise_id', $row['exercise_id'], 'int'),
						array(':user_id', $row['user_id'], 'int'),
						array(':pr_date', $ex_data['logitem_date'], 'str'),
						array(':pr_value', $ex_data['logitem_weight'], 'float'),
						array(':pr_reps', $ex_data['logitem_reps'], 'int')
					);
					$db->query($query, $params);
				}
			}
		}
	}

	public function rebuildExercisePRs($exercise_id)
	{
		// delete existing records
        DB::table('exercise_records')
            ->where('exercise_id', $exercise_id)
            ->delete();
        DB::table('log_items')
            ->where('exercise_id', $exercise_id)
            ->update(['is_pr' => 0]);

        $log_items = DB::table('log_items')
            ->select('logitem_id', 'logitem_abs_weight', 'logitem_reps', 'logitem_date', 'user_id', 'is_time')
            ->where('exercise_id', $exercise_id)
            ->orderBy('logitem_date', 'asc')
            ->get();
		$pr_time = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0);
        $pr_value = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0);
		foreach ($log_items as $log_item)
		{
			if ($log_item['logitem_reps'] <= 10 && $log_item['logitem_reps'] > 0
             && (($pr_time[$log_item['logitem_reps']] < $log_item['logitem_abs_weight'] && $log_item['is_time'] == 1)
             || ($pr_value[$log_item['logitem_reps']] < $log_item['logitem_abs_weight'] && $log_item['is_time'] == 0)))
			{
                if ($log_item['is_time'])
                {
                    $pr_time[$log_item['logitem_reps']] = $log_item['logitem_abs_weight'];
                } else {
                    $pr_value[$log_item['logitem_reps']] = $log_item['logitem_abs_weight'];
                }
                DB::table('log_items')
                    ->where('logitem_id', $log_item['logitem_id'])
                    ->update(['is_pr' => 1]);
                DB::table('exercise_records')->insert(
                    ['exercise_id' => $exercise_id,
                    'user_id' => $log_item['user_id'],
                    'pr_date' => $log_item['logitem_date'],
                    'pr_value' => $log_item['logitem_abs_weight'],
                    'pr_reps' => $log_item['logitem_reps'],
                    'is_time' => $log_item['is_time']]);
			}
		}
	}

    public function get_prs ($user_id, $log_date, $exercise_name, $return_date = false)
	{
		// load all preceeding prs
        $records = DB::table('exercise_records')
                    ->join('exercises', 'exercise_records.exercise_id', '=', 'exercises.exercise_id')
                    ->select('pr_reps', 'exercises.is_time', DB::raw('MAX(pr_value) as pr_value'))
                    ->where('exercise_records.user_id', $user_id)
                    ->where('exercises.exercise_name', $exercise_name)
                    ->where('pr_date', '<=', $log_date)
                    ->groupBy(function ($item, $key) {
                        return ($item['is_time']) ? 'T' : 'W';
                    })
                    ->groupBy('pr_reps');
        if ($return_date)
        {
            $records = $records->addSelect(DB::raw('MAX(pr_date) as pr_date'));
        }
        $records = $records->get();
		$prs = array('W' => array(), 'T' => array());
		$date = array('W' => array(), 'T' => array());
		while ($row = $db->fetch())
		{
			$type = ($row['is_time'] == 1) ? 'T' : 'W';
			if ($return_date)
			{
				$date[$type][$row['pr_reps']] = $row['pr_date'];
			}
			$prs[$type][$row['pr_reps']] = $row['pr_value'];
		}
		if ($return_date)
		{
			return array($prs, $date);
		}
		else
		{
			return $prs;
		}
	}
}