<?php

namespace Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

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

         return Response::json($response);
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

         return Response::json($response);
    }

    /**
     * Log a user to the app with is id and token.
     *
     * Requires:
     * $_POST['mobile_id']
     * $_POST['token']
     *
     * @return mixed
     */
    public function postLoginWithToken()
    {
        $response = $this->getUser()->loginWithToken(Input::get('mobile_id'), Input::get('token'));

        return Response::json($response);
    }

    public function getLogout ()
    {
        $this->logged();

        $response = $this->getUser()->logout();

         return Response::json($response);
    }

    public function getInfo()
    {
        $this->logged();

        $response = $this->getUser()->info(Auth::user()->id);

         return Response::json($response);
    }
}
