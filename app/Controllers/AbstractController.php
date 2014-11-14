<?php

namespace Controllers;

use Cryptalker\ModelAccessor;
use Illuminate\Routing\Controller;


/**
 * Class AbstractController
 * @package Controllers
 */
class AbstractController extends Controller
{
    use ModelAccessor;
}
