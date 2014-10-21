<?php

namespace Models\Friend;

use Models\AbstractValidator;

/**
 * Class FriendValidator
 * @package Models\Friend
 */
class FriendValidator extends AbstractValidator
{
    /**
     * @var array list of inputs to keep after validation
     */
    protected $inputs = array(
        'register' => 'email,pseudo',
        'login' => 'email'
    );

    /**
     * @var array Register validation rules
     */
    protected $register = array(
        'email' => 'required|email|unique:users',
        'pseudo' => 'required|alpha_dash|min:2|max:55',
        'password' => 'required|min:4|max:55|confirmed'
    );

    /**
     * @var array Login validation rules
     */
    protected $login = array(
        'email' => 'required|exists:users',
        'password' => 'required'
    );
}
