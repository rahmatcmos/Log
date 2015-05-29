<?php namespace ThunderID\Log\Models\Observers;

use DB, Validator;
use ThunderID\Log\Models\ProcessLog;
use ThunderID\Person\Models\Person;

/* ----------------------------------------------------------------------
 * Event:
 * 	Saving						
 * 	Deleting						
 * ---------------------------------------------------------------------- */

class LogObserver 
{
	public function saving($model)
	{
		$validator 				= Validator::make($model['attributes'], $model['rules']);

		if ($validator->passes())
		{
			return true;
		}
		else
		{
			$model['errors'] 	= $validator->errors();

			return false;
		}
	}

	public function deleting($model)
	{
		if(date('Y-m-d',strtotime($model['attributes']['on'])) <= date('Y-m-d'))
		{
			$model['errors'] 	= ['Tidak dapat menghapus log yang sudah lewat dari tanggal hari ini.'];

			return false;
		}

		$processes 							= ProcessLog::personid($model['attributes']['person_id'])->ondate([date('Y-m-d',strtotime($model['attributes']['on'])), date('Y-m-d',strtotime($model['attributes']['on'].' + 1 Day'))])->get();

		foreach ($processes as $key => $value) 
		{
			$process 						= ProcessLog::find($value->id);

			if(!$process->delete())
			{
				$model['errors'] 			= $process->getError();
				
				return false;
			}
		}
	}
}
