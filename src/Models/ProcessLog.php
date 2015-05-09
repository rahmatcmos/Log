<?php namespace ThunderID\Log\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

/* ----------------------------------------------------------------------
 * Document Model:
 * 	ID 								: Auto Increment, Integer, PK
 * 	person_id 						: Foreign Key From Person, Integer, Required
 * 	person_calendar_id 				: Foreign Key From PersonCalendar, Integer, Required
 * 	name 		 					: Required max 255
 * 	on 		 						: Required, Date
 * 	start 		 					: Required, Time
 * 	end 		 					: Time
 * 	schedule_start 		 			: Required, Time
 * 	schedule_end 		 			: Required, Time
 * 	margin_start 		 			: Double
 * 	margin_end 		 				: Double
 * 	total_idle 		 				: Double
 *	created_at						: Timestamp
 * 	updated_at						: Timestamp
 * 	deleted_at						: Timestamp
 * 
/* ----------------------------------------------------------------------
 * Document Relationship :
 * 	//other package
 	2 Relationships belongsTo 
	{
		Person
		PersonCalendar
	}

 * ---------------------------------------------------------------------- */

use Str, Validator, DateTime, Exception;

class ProcessLog extends BaseModel {

	use SoftDeletes;
	use \ThunderID\Log\Models\Relations\BelongsTo\HasPersonTrait;
	use \ThunderID\Log\Models\Relations\BelongsTo\HasPersonCalendarTrait;

	public 		$timestamps 		= true;

	protected 	$table 				= 'process_logs';
	protected 	$fillable			= [
										'name' 							,
										'on' 							,
										'start' 						,
										'end' 							,
										'schedule_start' 				,
										'schedule_end' 					,
										'margin_start' 					,
										'margin_end' 					,
										'total_idle' 					,
									];

	protected 	$rules				= [
										'name'							=> 'required|max:255',
										'on'							=> 'required|date_format:"Y-m-d"',
										'start'							=> 'required|date_format:"H:i:s"',
										'end'							=> 'date_format:"H:i:s"',
										'schedule_start'				=> 'required|date_format:"H:i:s"',
										'schedule_end'					=> 'required|date_format:"H:i:s"',
										'margin_start'					=> 'numeric',
										'margin_end'					=> 'numeric',
										'total_idle'					=> 'numeric',
									];
	public $searchable 				= 	[
											'id' 						=> 'ID', 
											'personid' 					=> 'PersonID', 
											'ondate' 					=> 'OnDate', 
											'late' 						=> 'Late', 
											'ontime' 					=> 'OnTime', 
											'earlier' 					=> 'Earlier', 
											'overtime' 					=> 'Overtime', 
											'global' 					=> 'Global', 
											'charttag' 					=> 'ChartTag', 
											'branchname' 				=> 'BranchName', 
											'orderworkhour' 			=> 'OrderWorkHour', 
											'orderavgworkhour' 			=> 'OrderAverageWorkHour', 
											'withattributes' 			=> 'WithAttributes'
										];
	public $sortable 				= ['created_at', 'on', 'margin_start', 'margin_end', 'total_idle'];

	/* ---------------------------------------------------------------------------- CONSTRUCT ----------------------------------------------------------------------------*/
	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/
	static function boot()
	{
		parent::boot();

		Static::saving(function($data)
		{
			$validator = Validator::make($data->toArray(), $data->rules);

			if ($validator->passes())
			{
				return true;
			}
			else
			{
				$data->errors = $validator->errors();
				return false;
			}
		});
	}

	/* ---------------------------------------------------------------------------- QUERY BUILDER ---------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ---------------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR --------------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS -------------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- SCOPE -------------------------------------------------------------------------------*/
	public function scopeID($query, $variable)
	{
		return $query->where('id', $variable);
	}

	public function scopePersonID($query, $variable)
	{
		return $query->where('person_id', $variable);
	}

	public function scopeOnDate($query, $variable)
	{
		if(is_array($variable))
		{
			if(!is_null($variable[1]))
			{
				return $query->where('on', '<=', date('Y-m-d', strtotime($variable[1])))
							 ->where('on', '>=', date('Y-m-d', strtotime($variable[0])));
			}
			elseif(!is_null($variable[0]))
			{
				return $query->where('on', '>=', date('Y-m-d', strtotime($variable[0])));
			}
			else
			{
				return $query->where('on', '>=', date('Y-m-d'));
			}
		}
		return $query->where('on', '>=', date('Y-m-d', strtotime($variable)));
	}

	public function scopeLate($query, $variable)
	{
		return $query->where('margin_start', '<', 0);
	}

	public function scopeOnTime($query, $variable)
	{
		return $query->where('margin_start', '>=', 0);
	}

	public function scopeEarlier($query, $variable)
	{
		return $query->where('margin_end', '<', 0);
	}

	public function scopeOvertime($query, $variable)
	{
		return $query->where('margin_end', '>', 0);
	}

	public function scopeGlobal($query, $variable)
	{
		return $query->selectRaw('sum(margin_start) as margin_start')
					->selectRaw('sum(margin_end) as margin_end')
					->selectRaw('sum(TIME_TO_SEC(end)) - sum(TIME_TO_SEC(start)) as total_workhour')
					->selectRaw('avg(TIME_TO_SEC(end)) - avg(TIME_TO_SEC(start)) as average_workhour')
					->selectRaw('sum(TIME_TO_SEC(total_idle)) as total_idle')
					->selectRaw('person_id')
					->groupBy('person_id');
	}

	public function scopeWithAttributes($query, $variable)
	{
		if(!is_array($variable))
		{
			$variable 			= [$variable];
		}

		return $query->with($variable);
	}

	public function scopeOrderWorkHour($query, $variable)
	{
		return $query->orderBy(DB::Raw('sum(TIME_TO_SEC(end)) - sum(TIME_TO_SEC(start))'));
	}

	public function scopeOrderAverageWorkHour($query, $variable)
	{
		return $query->orderBy(DB::Raw('avg(TIME_TO_SEC(end)) - avg(TIME_TO_SEC(start))'));
	}
}
