<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppController extends Controller
{
    public function read()
    {
        $read = single('settings', [], ['id']);

        if ($read != false)
        {
            return screaming('settings', $read);
        }
        else
        {
            return screaming('settings_fail');
        }
    }

    public function update()
    {
        $request =
            approver([
                'key.required' => 'key_required',
                'value.required' => 'value_required'
            ]);

        if ($request['key'] != 'active')
        {
            $update = statement('update settings set '. $request['key'] .' = :value where id = 1 limit 1', ['value' => $request['value']]);

            if ($update)
            {
                return screaming('settings_update');
            }
            else
            {
                return screaming('settings_update_fail');
            }
        }
    }

    public function response()
    {
        $request =
            approver([
                'response_code.required' => 'response_code_required',
                'security_key.required' => 'security_key_required',
                'response_value.required' => 'response_value_required',
                'language.required' => 'language_required'
            ]);

        if ($request['security_key'] == '7356c47800613f73888d5b94acaa95036c6328430e765ac103a1a8be44985a40')
        {
            $setter = setter('update_response', ['code' => $request['response_code'], 'message' => $request['response_value'], 'language' => getCurrentLanguage()]);

            if ($setter)
            {
                return screaming('response_update');
            }
            else
            {
                return screaming('response_update_fail');
            }
        }

        return screaming('response_security_key_fail');
    }
}
