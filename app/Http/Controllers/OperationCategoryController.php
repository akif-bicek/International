<?php

namespace App\Http\Controllers;

class OperationCategoryController extends Controller
{
    public function read()
    {
        $read = selecter('operation_categories', ['language' => getCurrentLanguage()]);

        if (count($read) > 0)
        {
            return screaming('operation_categories', $read);
        }
        else
        {
            return screaming('operation_categories_fail');
        }
    }
}
