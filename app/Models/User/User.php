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
     *
     * @return array
     */
    public function register($email, $pseudo, $password, $password_confirmation)
    {
        // test parameters
        $this->required('register', $email, $pseudo, $password, $password_confirmation);

        // Email full lowercase
        $email = strtolower($email);

        // check pseudo
        if ($this->isPseudoExist($pseudo)) {
            $this->error('pseudo', 'Pseudo already exist');
        } else {

            // check password
            $validation = $this->validate('register', array(
                'email' => $email,
                'pseudo' => $pseudo,
                'password' => $password,
                'password_confirmation' => $password_confirmation
            ));

            $this->parseValidation($validation);

            if ($validation->validated) {

                // insert to DB
                DB::table('users')->insert(array(
                    'email' => $email,
                    'pseudo' => $pseudo,
                    'password' => Hash::make($password),
                    'created_at' => date('Y-m-d H:i:s')
                ));

                $this->success();
            }
        }

        return $this->response();
    }

    /**
     * Log a user to the app with his pseudo or his email.
     *
     * @param $pseudoOrEmail
     * @param $password
     *
     * @return array
     */
    public function login($pseudoOrEmail, $password)
    {
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

        if (!Hash::check($password, $user->password)) {
            $this->error('password', 'Bad pseudo/password combination');
        } else {
            Auth::loginUsingId($user->id);
            $this->success();
        }

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
}
