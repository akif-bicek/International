<?php

namespace App\Http\Controllers;

class GenderController extends Controller
{
    public function read()
    {
        $read = selecter('genders', ['language' => getCurrentLanguage()]);
        if (count($read) > 0)
        {
            return screaming('genders', $read);
        }
        else
        {
            return screaming('genders_fail');
        }
    }
}
