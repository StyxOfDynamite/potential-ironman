<?php

class User extends Model {

	public function invoices()
    {
        return $this->hasMany('Invoice');
    }
}