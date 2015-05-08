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
}