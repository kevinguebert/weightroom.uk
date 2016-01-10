<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Exercise_record extends Model
{
    protected $primaryKey = 'pr_id';
    protected $dates = ['log_date'];
    protected $dateFormat = 'Y-m-d';
    protected $casts = [
        'is_time' => 'boolean',
    ];
    protected $guarded = ['pr_id'];

    public function scopeGetexerciseprs($query, $user_id, $log_date, $exercise_name, $is_time = false, $return_date = false)
    {
        $query = $query->join('exercises', 'exercise_records.exercise_id', '=', 'exercises.exercise_id')
                ->where('exercise_records.user_id', $user_id)
                ->where('exercises.exercise_name', $exercise_name)
                ->where('exercises.is_time', $is_time)
                ->where('log_date', '<=', $log_date)
                ->groupBy('pr_reps');
        if ($return_date)
        {
            $query = $query->select('pr_reps', DB::raw('MAX(pr_value) as pr_value'), DB::raw('MAX(log_date) as log_date'));
        }
        else
        {
            $query = $query->lists(DB::raw('MAX(pr_value) as pr_value'), 'pr_reps');
        }
        return $query;
    }

    public function scopeGetexerciseprsall($query, $user_id, $log_date, $exercise_name, $is_time = false)
    {
        $query = $query->join('exercises', 'exercise_records.exercise_id', '=', 'exercises.exercise_id')
                ->select('pr_reps', 'pr_value', 'log_date')
                ->where('exercise_records.user_id', $user_id)
                ->where('exercises.exercise_name', $exercise_name)
                ->where('exercises.is_time', $is_time)
                ->where('log_date', '<=', $log_date)
                ->orderBy('log_date', 'desc');
        return $query;
    }

    public function scopeGetexercisemaxpr($query, $user_id, $exercise_id, $exercise_is_time)
    {
        $query = $query->where('user_id', $user_id)
                ->where('exercise_id', $exercise_id)
                ->where('is_time', $exercise_is_time)
                ->orderBy('pr_value', 'desc')
                ->value('pr_value');
        return $query;
    }

    public static function filterPrs($collection)
    {
        $last_pr = 0;
        return $collection->reverse()->map(function ($item, $key) use (&$last_pr) {
            if ($item->pr_value < $last_pr)
            {
                $item->pr_value = $last_pr . '*';
            }
            $last_pr = $item->pr_value;
            return $item;
        })->reverse()->groupBy('pr_reps')->toArray()
    }
}
