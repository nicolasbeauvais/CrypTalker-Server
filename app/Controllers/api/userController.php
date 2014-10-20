<?php

namespace Controllers\api;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Controllers\AbstractController;

class userController extends AbstractController
{

    /**
     * Register a user to CrypTalker.
     *
     * Requires:
     * $_POST['email']
     * $_POST['pseudo']
     * $_POST['password']
     * $_POST['password_confirmation']
     *
     * @return mixed
     */
    public function postRegister()
    {
        $response = $this->getUser()->register(
            Input::get('email'),
            Input::get('pseudo'),
            Input::get('password'),
            Input::get('password_confirmation')
        );

        return View::make('json', array('response' => $response));
    }

    /**
     * Log a user to the app with is pseudo or email.
     *
     * Requires:
     * $_POST['pseudoOrEmail']
     * $_POST['password']
     *
     * @return mixed
     */
    public function postLogin()
    {
        $response = $this->getUser()->login(Input::get('pseudoOrEmail'), Input::get('password'));

        return View::make('json', array('response' => $response));
    }
}
