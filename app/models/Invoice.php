<?php

class Invoice extends Model {
	public $timestamps = false;

	public function user()
    {
        return $this->belongsTo('User');
    }
}