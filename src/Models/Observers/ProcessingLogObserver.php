<?php namespace ThunderID\Log\Models\Observers;

use DB, Validator;
use ThunderID\Log\Models\ProcessLog;
use ThunderID\Log\Models\Log;
use ThunderID\Person\Models\Person;
use Illuminate\Support\MessageBag;

/* ----------------------------------------------------------------------
 * Event:
 * 	Creating						
 * 	Saving						
 * 	Updating						
 * 	Deleting						
 * ---------------------------------------------------------------------- */

class ProcessingLogObserver 
{
	public function saved($model)
	{
		if(isset($model['attributes']['person_id']))
		{
			$this->errors 			= new MessageBag;

			$on 					= date("Y-m-d", strtotime($model['attributes']['on']));
			$time 					= date("H:i:s", strtotime($model['attributes']['on']));
			$data 					= new ProcessLog;
			$data 					= $data->ondate($on)->personid($model['attributes']['person_id'])->first();
			if(isset($data->id))
			{
				if($data->start <= $time)
				{
					$start 			= $data->start;
					list($hours, $minutes, $seconds) = explode(":", $start);

					$start 			= $hours*3600+$minutes*60+$seconds;

					$schedule_start = $data->schedule_start;
					list($hours, $minutes, $seconds) = explode(":", $schedule_start);

					$schedule_start	= $hours*3600+$minutes*60+$seconds;

					$margin_start 	= $schedule_start - $start;

					$end 			= $data->end;
					list($hours, $minutes, $seconds) = explode(":", $end);

					$end 			= $hours*3600+$minutes*60+$seconds;

					$schedule_end = $data->schedule_end;
					list($hours, $minutes, $seconds) = explode(":", $schedule_end);

					$schedule_end	= $hours*3600+$minutes*60+$seconds;

					$margin_end 	= $end - $schedule_end;
					
					$idle 			= Log::ondate($on)->personid($model['attributes']['person_id'])->orderBy('on', 'asc')->get();
					$total_idle 	= 0;

					foreach ($idle as $key => $value) 
					{
						if(strtolower($value['name']) == 'idle')
						{
							$start_idle = date('H:i:s', strtotime($value['on']));
							list($hours, $minutes, $seconds) = explode(":", $start_idle);

							$start_idle = $hours*3600+$minutes*60+$seconds;
						}
						elseif(strtolower($value['name'] != 'idle') && isset($start_idle))
						{
							$new_idle 	= date('H:i:s', strtotime($value['on']));
							list($hours, $minutes, $seconds) = explode(":", $new_idle);

							$new_idle 	= $hours*3600+$minutes*60+$seconds;

							$total_idle	= $total_idle + $new_idle - $start_idle;
							unset($start_idle);
						}
					}

					$data->fill([
										'end'			=> $time,
										'margin_start'	=> $margin_start,
										'margin_end'	=> $margin_end,
										'total_idle'	=> $total_idle,
								]
					);
				}
				elseif($data->start > $time)
				{
					$start 			= $data->start;
					list($hours, $minutes, $seconds) = explode(":", $start);

					$start 			= $hours*3600+$minutes*60+$seconds;

					$schedule_start = $data->schedule_start;
					list($hours, $minutes, $seconds) = explode(":", $schedule_start);

					$schedule_start	= $hours*3600+$minutes*60+$seconds;

					$margin_start 	= $schedule_start - $start;

					$end 			= $data->end;
					list($hours, $minutes, $seconds) = explode(":", $end);

					$end 			= $hours*3600+$minutes*60+$seconds;

					$schedule_end = $data->schedule_end;
					list($hours, $minutes, $seconds) = explode(":", $schedule_end);

					$schedule_end	= $hours*3600+$minutes*60+$seconds;

					$margin_end 	= $end - $schedule_end;
					
					$idle 			= Log::ondate($on)->personid($model['attributes']['person_id'])->orderBy('on', 'asc')->get();
					$total_idle 	= 0;

					foreach ($idle as $key => $value) 
					{
						if(strtolower($value['name']) == 'idle')
						{
							$start_idle = date('H:i:s', strtotime($value['on']));
							list($hours, $minutes, $seconds) = explode(":", $start_idle);
							
							$start_idle = $hours*3600+$minutes*60+$seconds;
						}
						if(strtolower($value['name'] != 'idle') && isset($start_idle))
						{
							$new_idle 	= date('H:i:s', strtotime($value['on']));
							list($hours, $minutes, $seconds) = explode(":", $new_idle);

							$new_idle 	= $hours*3600+$minutes*60+$seconds;

							$total_idle	= $total_idle + $new_idle - $start_idle;
							unset($start_idle);
						}
					}

					$data->fill([
										'start'			=> $time,
										'margin_start'	=> $margin_start,
										'margin_end'	=> $margin_end,
										'total_idle'	=> $total_idle,
								]
					);
				}

				if (!$data->save())
				{
					$model['errors'] = $data->getError();
					return false;
				}

				return true;

			}
			else
			{
				$data 				= new ProcessLog;
				$person 			= Person::find($model['attributes']['person_id']);
				$pschedule 			= Person::ID($model['attributes']['person_id'])->schedule(['on' => $on])->withAttributes(['schedules'])->first();
				if($pschedule)
				{
					$schedule_start	= $pschedule->schedules[0]->start;
					$schedule_end	= $pschedule->schedules[0]->end;
				}
				else
				{
					$ccalendar 	= Person::ID($model['attributes']['person_id'])->CheckWork(true)->WorkCalendar(true)->WorkCalendarschedule(['on' => [$on, $on]])->withAttributes(['workscalendars','workscalendars.calendar', 'workscalendars.calendar.schedules'])->first();
				
					if($ccalendar)
					{
						$schedule_start	= $ccalendar->workscalendars[0]->calendar->schedules[0]->start;
						$schedule_end	= $ccalendar->workscalendars[0]->calendar->schedules[0]->end;
					}
					else
					{
						//wait for company policies
						$schedule_start = '00:00:00';
						$schedule_end 	= '00:00:00';
					}
				}

				$data->fill([
									'name'			=> 'Attendance',
									'on'			=> $on,
									'start'			=> $time,
									'schedule_start'=> date('H:i:s',strtotime($schedule_start)),
									'schedule_end'	=> date('H:i:s',strtotime($schedule_end)),
							]
				);

				$data->Person()->associate($person);
				if (!$data->save())
				{
					$model['errors'] = $data->getError();
					return false;
				}

				return true;
			}
		}
		return true;
	}
}
