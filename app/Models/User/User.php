<?php

namespace Models\User;

use Models\AbstractModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

                $rowExist =  DB::table('mobiles')
                    ->where('user_id', '=', $user->id)
                    ->first();

                if ($rowExist) {

                    DB::table('mobiles')->update(array(
                        'mobile_id' => $mobile_id,
                        'token' => $token,
                        'created_at' => date('Y-m-d H:i:s')
                    ))->where('mobiles.user_id', '=', $user->id);
                } else {

                    DB::table('mobiles')->insert(array(
                        'user_id' => $user->id,
                        'mobile_id' => $mobile_id,
                        'token' => $token,
                        'created_at' => date('Y-m-d H:i:s')
                    ));
                }
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

    public function info($user_id)
    {
        $response = array();

        $response['user'] = DB::table('users')
            ->select(
                'users.id',
                'users.email',
                'users.pseudo'
            )
            ->where('users.id', '=', $user_id)
            ->first();

        $response['friend_request_received'] = DB::table('friends')
            ->select(
                'users.id',
                'users.pseudo'
            )
            ->join('users', 'users.id', '=', 'friends.user_id')
            ->where('friends.user_friend_id', '=', $user_id)
            ->where('friends.status', '=', 0)
            ->get();

        $response['friend_request_sended'] = DB::table('friends')
            ->select(
                'users.id',
                'users.pseudo'
            )
            ->join('users', 'users.id', '=', 'friends.user_friend_id')
            ->where('friends.user_id', '=', $user_id)
            ->where('friends.status', '=', 0)
            ->get();

        $rooms_id = DB::table('user_room')
            ->select('rooms.id')
            ->join('rooms', 'rooms.id', '=', 'user_room.room_id')
            ->where('user_room.user_id', '=', $user_id)
            ->lists('rooms.id');

        $rooms_id = empty($rooms_id) ? array(0) : $rooms_id;

        $response['rooms'] = DB::table('rooms')
            ->select(
                DB::raw('GROUP_CONCAT(users.pseudo) as pseudos'),
                'rooms.id as id',
                'rooms.name'
            )
            ->join('user_room', 'rooms.id', '=', 'user_room.room_id')
            ->join('users', 'users.id', '=', 'user_room.user_id')
            ->where('users.id', '!=', $user_id)
            ->whereIn('rooms.id', (array)$rooms_id)
            ->groupBy('rooms.id')
            ->get();

        foreach ($response['rooms'] as $k => $v) {

            $response['rooms'][$k]->name = empty($v->name) ?
                $response['rooms'][$k]->pseudos : $response['rooms'][$k]->name;

            $response['rooms'][$k]->messages = array();
        }

        $this->data($response);

        return $this->response();
    }

    public function getMobileIdByRoom($roomId, $blacklistedId)
    {
        return DB::table('rooms')
            ->join('user_room', 'user_room.room_id', '=', 'rooms.id')
            ->join('mobiles', 'mobiles.user_id', '=', 'user_room.user_id')
            ->where('rooms.id', '=', (int)$roomId)
            ->where('user_room.user_id', '!=', (int)$blacklistedId, 'AND')
            ->lists('mobiles.mobile_id');
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
