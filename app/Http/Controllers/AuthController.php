<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function read()
    {
        $request =

            approver([
                'email.required' => 'email_required',
                'password.required' => 'password_required',
                'password.min:6' => 'password_min',
                'password.max:16' => 'password_max'
            ]);

        $user = single('login', ['email' => $request['email'], 'password' => md5($request['password'])], ['password']);
        if ($user != false)
        {
            $user->is_developer = 0;
            if ($user->is_admin == 2)
            {
                $user->is_developer = 1;
            }
            $user->photo = public_path('uploads/images/') . $user->photo;
            $user->gender = single('gender', ['id' => $user->gender])->name;
            $user->token = User::find($user->id)->createToken('login')->plainTextToken;
            return screaming('login', $user);
        }
        else
        {
            return screaming('login_fail');
        }
    }

    public function create()
    {
        $request =

            approver([
                'name.required' => 'name_required',
                'surname.required' => 'surname_required',
                'email.required' => 'email_required',
                'email.unique:users' => 'email_unique',
                'password.required' => 'password_required',
                'password.min:6' => 'password_min',
                'password.max:16' => 'password_max',
                're_password.required' => 're_password_required',
                're_password.same:password' => 're_password_same',
                'gender.required' => 'gender_required',
                'language.required' => 'language_required'
            ]);

        $user = new User();
        $user->name = $request['name'];
        $user->surname = $request['surname'];
        $user->email = $request['email'];
        $user->password = md5($request['password']);
        $user->gender = $request['gender'];
        $user->language = getLanguage($request['language']);
        $user->save();
        $token = $user->createToken('auth_token')->plainTextToken;

        if ($user != null)
        {
            return screaming('register', ['token' => $token]);
        }
        else
        {
            return screaming('register_fail');
        }
    }

    public function update()
    {
        $request =

            approver([
                'name.required' => 'name_required',
                'surname.required' => 'surname_required',
                'email.required' => 'email_required',
                'email.unique:users' => 'email_unique',
                'phone.required' => 'phone_required',
                'gender.required' => 'gender_required',
                'born_date.required' => 'born_date_required'
            ]);

        $user = User::find(Auth::id());
        $user->name = $request['name'];
        $user->surname = $request['surname'];
        $user->email = $request['email'];
        $user->phone = $request['phone'];
        $user->gender = $request['gender'];
        $user->save();

        if ($user != null)
        {
            return screaming('profile');
        }
        else
        {
            return screaming('profile_fail');
        }
    }

    public function password()
    {
        $request =
            approver([
                'old_password.required' => 'old_password_required',
                'password.required' => 'password_required',
                'password.min:6' => 'password_min',
                'password.max:16' => 'password_max',
                're_password.required' => 're_password_required',
                're_password.same:password' => 're_password_same'
            ]);

        $user = User::find(Auth::id());
        $user->password = md5($request['password']);
        $user->save();

        if ($user != null)
        {
           return screaming('password_update');
        }
        else
        {
            return screaming('password_update_fail');
        }
    }

    public function resetPhone()
    {
        $request =
            approver([
                'phone.required' => 'phone_required'
            ]);

        $user = User::query()->where('phone', $request['phone'])->first();

        if ($user != null)
        {
            $smsCode = mt_rand(100000, 999999);
            setter('reset_delete', ['user_id' => $user->id]);
            $code = setter('reset', ['user_id' => $user->id, 'code' => $smsCode, 'date' => time()]);
            if ($code != false)
            {
                return screaming('reset_phone', ['code' => $smsCode]);
            }
            else
            {
                return screaming('reset_phone_fail');
            }
        }
        else
        {
            return screaming('reset_phone_user_fail');
        }
    }

    public function resetCode()
    {
        $request =
            approver([
                'code.required' => 'code_required'
            ]);

        $code = single('reset_code', ['code' => $request['code'], 'date' => (time() + 180)]);


        if ($code != false)
        {
            $user = User::find($code->user_id);
            if ($user != null)
            {
                return screaming('reset_code', ['user_id' => $user->id, 'key' => md5($user->name . $user->password)]);
            }
        }

        return screaming('reset_code_fail');
    }

    public function resetPassword()
    {
        $request =
            approver([
                'reset_key.required' => 'reset_key_required',
                'user_id.required' => 'user_id_required',
                'password.required' => 'password_required',
                'password.min:6' => 'password_min',
                'password.max:16' => 'password_max',
                're_password.required' => 're_password_required',
                're_password.same:password' => 're_password_same'
            ]);

        $user = User::find($request['user_id']);
        if (md5($user->name . $user->password) == $request['key'])
        {
            $user->password = md5($request['password']);
            $user->save();
            if ($user != null)
            {
                return screaming('password_update');
            }
        }

        return screaming('password_update_fail');
    }

    public function photo()
    {
        $user = User::find(Auth::id());
        $user->photo = uploader('photo');
        $user->save();

        if ($user != null)
        {
            return screaming('photo_update');
        }
        else
        {
            return screaming('photo_update_fail');
        }
    }

    public function photoReset()
    {
        $user = User::find(Auth::id());
        if ($user != null)
        {
            $user->photo = 'profile.jpg';
            $user->save();
            return screaming('photo_update');
        }
        else
        {
            return screaming('photo_update_fail');
        }
    }
}
