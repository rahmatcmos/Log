<?php namespace ThunderID\Log\Models\Relations\BelongsTo;

trait HasPersonCalendarTrait {

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasPersonCalendarTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP IN PERSON PACKAGE -------------------------------------------------------------------*/
	public function PersonCalendar()
	{
		return $this->belongsTo('ThunderID\Schedule\Models\PersonCalendar');
	}

}