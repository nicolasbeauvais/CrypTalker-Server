<?php

namespace Models\User;

use Models\AbstractModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;

class User extends AbstractModels
{
    /**
     * Register a user.
     *
     * @param $email
     * @param $pseudo
     * @param $password
     * @param $password_confirmation
     * @param $mobile_id
     *
     * @return array
     */
    public function register($email, $pseudo, $password, $password_confirmation, $mobile_id)
    {
        // test parameters
        $this->required('register', $email, $pseudo, $password, $password_confirmation, $mobile_id);

        // Email full lowercase
        $email = strtolower($email);

        // check
        $validation = $this->validate('register', array(
            'email' => $email,
            'pseudo' => $pseudo,
            'password' => $password,
            'password_confirmation' => $password_confirmation,
            'mobile_id' => $mobile_id
        ));

        $this->parseValidation($validation);

        if ($this->isPseudoExist($pseudo)) {
            $this->error('pseudo', 'Pseudo already exist');

        } elseif ($validation->validated) {

            // insert to DB
            $user_id = DB::table('users')->insertGetId(array(
                'email' => $email,
                'pseudo' => $pseudo,
                'password' => Hash::make($password),
                'created_at' => date('Y-m-d H:i:s')
            ));

            Auth::loginUsingId($user_id);

            $token = $this->makeToken($email);
            DB::table('mobiles')->insert(array(
                'user_id' => $user_id,
                'mobile_id' => $mobile_id,
                'token' => $token,
                'created_at' => date('Y-m-d H:i:s')
            ));

            $this->data('token', $token);
        }

        return $this->response();
    }

    /**
     * Log a user to the app with his pseudo or his email.
     *
     * @param $pseudoOrEmail
     * @param $password
     * @param $mobile_id
     *
     * @return array
     */
    public function login($pseudoOrEmail, $password, $mobile_id)
    {
        // test parameters
        $this->required('login', $pseudoOrEmail, $password, $mobile_id);

        $isPseudo = false;

        // If it's not an email, we check directly the pseudo
        if (strpos($pseudoOrEmail, '@') == -1 && !$this->isPseudoExist($pseudoOrEmail)) {
            $this->error('pseudo', 'Pseudo doesn\'t exist');
            return $this->response();
        } elseif (strpos($pseudoOrEmail, '@') > -1) {// It's an email

            $validation = $this->validate('login', array(
                'email' => $pseudoOrEmail,
                'password' => $password
            ));

            $this->parseValidation($validation);

            if (!$validation->validated) {
                return $this->response();
            }
        } else {
            $isPseudo = true;
        }

        if ($isPseudo) {
            $user = DB::table('users')
                ->where(DB::raw('LOWER(pseudo)'), '=', strtolower($pseudoOrEmail))
                ->first();
        } else {
            $user = DB::table('users')
                ->where('email', '=', $pseudoOrEmail)
                ->first();
        }

        if (!$user || !Hash::check($password, $user->password)) {
            $this->error('password', 'Bad pseudo/password combination');
        } else {
            Auth::loginUsingId($user->id);

            // Create token
            $token = $this->makeToken($pseudoOrEmail);

            // Already a token for this phone ?
            $tokenExist = DB::table('mobiles')
                ->where('user_id', '=', $user->id)
                ->where('mobile_id', '=', $mobile_id, 'AND')
                ->first();

            // Update if exist, else create
            if ($tokenExist) {
                DB::table('mobiles')
                    ->where('user_id', '=', $user->id)
                    ->where('mobile_id', '=', $mobile_id, 'AND')
                    ->update(array('token' => $token));
            } else {
                DB::table('mobiles')->insert(array(
                    'user_id' => $user->id,
                    'mobile_id' => $mobile_id,
                    'token' => $token,
                    'created_at' => date('Y-m-d H:i:s')
                ));
            }

            $this->data('token', $token);
        }

        return $this->response();
    }

    public function loginWithToken($mobile_id, $token)
    {
        $this->required('loginWithToken', $token);

        $mobile = DB::table('mobiles')
            ->where('token', '=', $token)
            ->where('mobile_id', '=', $mobile_id, 'AND')
            ->first();

        if ($mobile) {
            Auth::loginUsingId($mobile->user_id);
            $this->data('token', $token);
        } else {
            $this->error('token', 'Bad token');
        }

        return $this->response();
    }

    public function logout()
    {
        Auth::logout();

        return $this->response();
    }

    /**
     * Verify if a pseudo already exist (not case sensitive)
     *
     * @param $pseudo
     * @return bool
     */
    private function isPseudoExist($pseudo)
    {
        if (!$pseudo) {
            return false;
        }

        $pseudo = strtolower($pseudo);

        return DB::table('users')->where(DB::raw('LOWER(pseudo)'), '=', $pseudo)->first() === null ? false : true;
    }

    /**
     * Create a user token from any string.
     *
     * @param $string
     * @return string
     */
    private function makeToken($string)
    {
        return substr(sha1($string . microtime()), 0, 69);
    }
}
