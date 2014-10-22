<?php

namespace Models\Message;

use Models\AbstractValidator;

/**
 * Class MessageValidator
 * @package Models\Room
 */
class MessageValidator extends AbstractValidator
{
    /**
     * @var array list of inputs to keep after validation
     */
    protected $inputs = array();
}
