<?php

namespace App\Extend;

class Format {
    public static function format_time($time)
    {
    	if ($time <= 60)
    	{
    		return $time . ' secs';
    	}
    	elseif ($time % 3600 == 0)
    	{
    		return ($time / 3600) . ' hours';
    	}
    	elseif ($time % 60 == 0)
    	{
    		return ($time / 60) . ' mins';
    	}
    	else
    	{
    		$output = '';
    		$hours = floor($time / 3600);
    		$time = $time % 3600;
    		$mins = floor($time / 60);
    		$secs = $time % 60;
    		if ($hours > 0)
    		{
    			return $hours . ':' . sprintf("%02d", $mins) . ':' . sprintf("%02d", $secs);
    		}
    		else
    		{
    			return $mins . ':' . sprintf("%02d", $secs);
    		}
    	}
    }

    public static function correct_time($time, $unit_used, $unit_want) // $unit_used = s/m/h $unit_want = s/m/h
    {
    	$unit_used = ($unit_used == 's') ? 1 : (($unit_used == 'm') ? 2 : 3);
    	$unit_want = ($unit_want == 's') ? 1 : (($unit_want == 'm') ? 2 : 3);
    	if ($unit_used > $unit_want)
    	{
    		return round(($time * pow (60,($unit_used - $unit_want))), 2);
    	}
    	elseif ($unit_used < $unit_want)
    	{
    		return round(($time / pow(60,($unit_want - $unit_used))), 2);
    	}
    	else
    	{
    		return round($time, 2);
    	}
    }

    public static function correct_weight($weight, $unit_used, $unit_want) // $unit_used = kg/lb $unit_want = kg/lb
    {
    	if (($unit_used == 'kg' && $unit_want == 'kg') || ($unit_used == 'lb' && $unit_want == 'lb'))
    	{
    		return round($weight, 2);
    	}
    	elseif ($unit_used == 'kg' && $unit_want == 'lb')
    	{
    		return round(($weight * 2.20462), 2); // convert to lb
    	}
    	elseif ($unit_used == 'lb' && $unit_want == 'kg')
    	{
    		return round(($weight * 0.453592), 2); // convert to kg
    	}
    	else
    	{
    		return round($weight, 2);
    	}
    }
}