<?php namespace ThunderID\Log\seeds;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use ThunderID\Log\Models\Log;
use ThunderID\Person\Models\Person;
use \Faker\Factory, Illuminate\Support\Facades\DB;
use \DateTime, \DateInterval, \DatePeriod;

class LogTableSeeder extends Seeder
{
	function run()
	{

		DB::table('logs')->truncate();
		DB::table('process_logs')->truncate();
		$faker 										= Factory::create();
		$total_persons  							= Person::count();
		$logs 										= ['login', 'logout','sleep', 'idle', 'lock', 'working', 'presence', 'presence', 'presence'];
		$pcs 										= ['redhat', 'ubuntu', 'debian', 'mint', 'centos', '7', 'xp', 'fp', 'fp', 'fp'];
		try
		{
			foreach(range(1, $total_persons) as $index)
			{
				$person 							= Person::find($index);
				$rand 								= rand(0,2);
				$begin 								= new DateTime( 'first day of january 2015' );
				$ended 								= new DateTime( 'last day of june 2015'  );

				$interval 							= DateInterval::createFromDateString('1 day');
				$periods 							= new DatePeriod($begin, $interval, $ended);

				foreach ( $periods as $period )
				{
					foreach(range(1, 8) as $index2)
					{
						if($index2==1)
						{
							$state 					= 0;
							$time 					= 'hour';
						}
						elseif($index2==8)
						{
							$state					= 1;
							$time 					= 'hours';
						}
						else
						{
							$state 					= rand(2,8);
							$time 					= 'hours';
						}

						$rand 						= rand(0,8);
						$data 						= new Log;
						$data->fill([
							'name'					=> $logs[$state],
							'on'					=> date("Y-m-d H:i:s", strtotime($period->format('Y-m-d').' + '.$index2.' '.$time)),
							'pc'					=> $pcs[$rand],
						]);

						$data->Person()->associate($person);

						if (!$data->save())
						{
							print_r($data->getError());
							exit;
						}
					}
				} 
			} 
		}
		catch (Exception $e) 
		{
    		echo 'Caught exception: ',  $e->getMessage(), "\n";
		}	
	}
}