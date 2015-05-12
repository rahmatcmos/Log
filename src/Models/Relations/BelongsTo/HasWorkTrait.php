<?php namespace ThunderID\Log\Models\Relations\BelongsTo;

trait HasWorkTrait {

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasWorkTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP IN PERSON PACKAGE -------------------------------------------------------------------*/
	public function Work()
	{
		return $this->belongsTo('ThunderID\Work\Models\Work');
	}

}