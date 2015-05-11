<?php namespace ThunderID\Log\Models\Relations\BelongsTo;

trait HasPersonTrait {

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasPersonTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP IN PERSON PACKAGE -------------------------------------------------------------------*/
	public function Person()
	{
		return $this->belongsTo('ThunderID\Person\Models\Person');
	}

	public function scopeChartTag($query, $variable)
	{
		return $query->WhereHas('person.works', function($q)use($variable){$q->where('tag', $variable);});
	}

	public function scopeBranchName($query, $variable)
	{
		return $query->WhereHas('person.works.branch', function($q)use($variable){$q->where('name', $variable);});
	}

	public function ScopeHasNoSchedule($query, $variable)
	{
		return $query->whereDoesntHave('person.schedules' ,function($q)use($variable){$q->ondate($variable['on']);});
	}

	public function ScopeCalendar($query, $variable)
	{
		return $query->whereHas('person.calendars' ,function($q)use($variable){$q->start($variable['start'])->id($variable['id']);});
	}

	public function ScopeWorkCalendar($query, $variable)
	{
		return $query->whereHas('person' ,function($q)use($variable){$q->CheckWork(true)->WorkCalendar(['start' => $variable['start'], 'id' => $variable['id']]);});
	}
}