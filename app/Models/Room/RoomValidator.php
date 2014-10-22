<?php

namespace Models\Room;

use Models\AbstractValidator;

/**
 * Class RoomValidator
 * @package Models\User
 */
class RoomValidator extends AbstractValidator
{
    /**
     * @var array list of inputs to keep after validation
     */
    protected $inputs = array(
        'name' => 'name'
    );

    protected $name = array(
        'name' => 'required|min:2|max:55'
    );
}
