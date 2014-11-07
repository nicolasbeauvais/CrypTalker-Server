<?php

namespace Controllers\Api;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;

/**
 * Class UserController
 * @package Controllers\Api
 */
class UserController extends AbstractApiController
{

    /**
     * Register a user to CrypTalker.
     *
     * Requires:
     * $_POST['email']
     * $_POST['pseudo']
     * $_POST['password']
     * $_POST['password_confirmation']
     * $_POST['mobile_id'] (Google Cloud Messaging id)
     *
     * @return mixed
     */
    public function postRegister()
    {
        $response = $this->getUser()->register(
            Input::get('email'),
            Input::get('pseudo'),
            Input::get('password'),
            Input::get('password_confirmation'),
            Input::get('mobile_id')
        );

        return View::make('json', array('response' => $response));
    }

    /**
     * Log a user to the app with is pseudo or email.
     *
     * Requires:
     * $_POST['pseudoOrEmail']
     * $_POST['password']
     * $_POST['mobile_id'] (Google Cloud Messaging id)
     *
     * @return mixed
     */
    public function postLogin()
    {
        $response = $this->getUser()->login(
            Input::get('pseudoOrEmail'),
            Input::get('password'),
            Input::get('mobile_id')
        );

        return View::make('json', array('response' => $response));
    }

    /**
     * Log a user to the app with is id and token.
     *
     * Requires:
     * $_POST['user_id']
     * $_POST['token']
     *
     * @return mixed
     */
    public function postLoginWithToken()
    {
        $response = $this->getUser()->loginWithToken(Input::get('token'));

        return View::make('json', array('response' => $response));
    }

    public function getLogout ()
    {
        $this->logged();

        $response = $this->getUser()->logout();

        return View::make('json', array('response' => $response));
    }
}
