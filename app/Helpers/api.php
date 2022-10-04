<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Request;

function getUserParam($param, $default)
{
    if (\Illuminate\Support\Facades\Auth::check())
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user[$param];
    }
    else
    {
        return $default;
    }
}

function approver($messages = array())
{
    $request = Request::post();
    $rules = array();
    foreach ($messages as $key => $message)
    {
        [$param, $type] = explode('.', $key);
        if (isset($rules[$param]))
        {
            $rules[$param] .= '|' . $type;
        }
        else
        {
            $rules[$param] = $type;
        }

        if (strpos($key, ':'))
        {
            [$rule, $value] = explode(':', $key);
            unset($messages[$key]);
            $messages[$rule] = $message;
        }
    }

    $validator = Validator::make($request, $rules, $messages);

    if ($validator->fails()) {
        $fails = array_values($validator->getMessageBag()->toArray())[0];
        $error = $fails[0];
        echo json_encode(screaming($error)->getData());
        die();
    }
    else
    {
        return $request;
    }
}

function uploader($name, $folder = 'images/', $approver = '_required')
{
    $request = Request();
    if ($request->hasFile($name)) {
        $imageName = rand(1000000, 9999999) . '.' . $request->image->extension();
        $request->{$name}->move(public_path('uploads/' . $folder), $imageName);
        return $imageName;
    }
    return screaming($name . $approver);
}

function getCode($message)
{
    $message = single('code', ['message' => $message, 'language' => getCurrentLanguage()]);
    return $message->code;
}

function screaming($code, $data = null)
{
    setMessages();
    $message = getMessage($code);
    $response = ['code' => $code, 'status' => $message->status, 'message' => $message->message];

    if ($data != null)
    {
        $response['data'] = $data;
    }

    return response()->json($response);
}

function getCurrentLanguage()
{
    $request = Request();

    $datas = $request->all();
    $language = getLanguage($datas['language'] ?? getUserParam('language', 'en'));

    return $language;
}

function getMessage($code)
{
    $message = single('messages', ['code' => $code, 'language' => getCurrentLanguage()]);
    return $message;
}

function getQuery($key)
{
    $queries = queries();

    return $queries[$key];
}

function setter($queryKey, $bindings, $replacer = false)
{
    return DB::statement(getQuery($queryKey), $bindings);
}

function statement($query, $bindings)
{
    return DB::statement($query, $bindings);
}

function selecter($queryKey, $bindings = [], $unsetter = [])
{
    $select = DB::select(getQuery($queryKey), $bindings);
    if (count($unsetter) > 0)
    {
        foreach ($select as $index => $data)
        {
            foreach ($data as $key => $value)
            {
                if (in_array($key, $unsetter))
                {
                    unset($select[$index]->{$key});
                }
            }
        }
    }
    return $select;
}

function single($queryKey, $bindings = [], $unsetter = [])
{
    $select = selecter($queryKey, $bindings, $unsetter);

    return $select[0] ?? false;
}

function checker($queryKey, $bindings = [])
{
    $select = selecter($queryKey, $bindings);

    return (count($select) > 0);
}

function status($status)
{
    $statuses = ['red' => 0, 'green' => 1];

    return $statuses[$status];
}

function getLanguage($short)
{
    $language = single('language', ['short_tag' => $short]);

    return $language->id ?? 1;
}

function setMessages()
{
    $messages = messages();

    foreach ($messages as $language => $langMessages)
    {
        foreach ($langMessages as $key => $message)
        {
            [$status, $code] = explode('.', $key);
            if (!checker('messages', ['code' => $code, 'language' => getLanguage($language)]))
            {
                setter(
                    'set_message',
                    [
                        'code' => $code,
                        'message' => $message,
                        'status' => status($status),
                        'language' => getLanguage($language)
                    ]
                );
            }
        }
    }
}

function getActive()
{
    return !boolval(single('active')->active);
}
