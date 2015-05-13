<?php namespace ThunderID\Log\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/* ----------------------------------------------------------------------
 * Document Model:
 * 	ID 								: Auto Increment, Integer, PK
 * 	organisation_id 				: Foreign Key From Organisation, Integer, Required
 * 	email 		 					: max 255
 * 	name 		 					: max 255
 * 	on 		 						: Datetime
 * 	pc 			 					: max 255
 * 	message 						: text
 *	created_at						: Timestamp
 * 	updated_at						: Timestamp
 * 	deleted_at						: Timestamp
 * 
/* ----------------------------------------------------------------------
 * Document Relationship :
 * 	//other package
 	1 Relationship belongsTo 
	{
		Organisation
	}

 * ---------------------------------------------------------------------- */

use Str, Validator, DateTime, Exception;

class ErrorLog extends BaseModel {

	use SoftDeletes;
	use \ThunderID\Log\Models\Relations\BelongsTo\HasOrganisationTrait;

	public 		$timestamps 		= true;

	protected 	$table 				= 'error_logs';

	protected 	$fillable			= 	[
											'email' 					,
											'name' 						,
											'on' 						,
											'pc' 						,
											'message' 					,
										];

	protected 	$rules				= 	[
											'email'						=> 'max:255',
											'name'						=> 'max:255',
											'on'						=> 'date_format:"Y-m-d H:i:s"',
											'pc'						=> 'max:255',
										];

	public $searchable 				= 	[
											'id' 						=> 'ID', 
											'organisationid' 			=> 'OrganisationID', 
											'name' 						=> 'Name', 
											'withattributes' 			=> 'WithAttributes'
										];
										
	public $sortable 				= 	['created_at'];

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

	public function scopeOrganisationID($query, $variable)
	{
		return $query->where('organisation_id', $variable);
	}

	public function scopeName($query, $variable)
	{
		if(is_array($variable))
		{
			return $query->whereIn('name', $variable);
		}

		return $query->where('name', $variable);
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

	public function scopeWithAttributes($query, $variable)
	{
		if(!is_array($variable))
		{
			$variable 			= [$variable];
		}

		return $query->with($variable);
	}
}
