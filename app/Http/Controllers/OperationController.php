<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;

class OperationController extends Controller
{
    public function read($id = 0)
    {
        if ($id != 0)
        {
            $read = single('operation', ['id' => $id], ['type', 'id_category', 'language', 'gender']);

            if ($read != false)
            {
                $imgs = array();
                foreach (selecter('operation_images', ['operation_id' => $id]) as $image)
                {
                    $imgs[] = public_path('uploads/images/') . $image->image;
                }
                $read->images = $imgs;
                $read->doctor = single('doctor', ['id' => $read->doctor])->name;
                if ($read->video != null)
                {
                    $read->video = public_path('uploads/videos/') . $read->video;
                }

                return screaming('operation', $read);
            }
            else
            {
                return screaming('operation_fail');
            }
        }

        $search = Request::post('search');
        $query = 'operations';
        $bindings = ['language' => getCurrentLanguage(), 'gender' => getCurrentGender()];
        if ($search != null)
        {
            $query .= '_search';
            $search = '%' . $search . '%';
            $bindings['title'] = $search;
            $bindings['content'] = $search;
            $bindings['desciription'] = $search;
        }

        $category = Request::post('category');
        if ($category != null)
        {
            $query .= '_category';
            $bindings['category_id'] = $category;
        }

        $read = selecter($query, $bindings, ['doctor', 'content', 'type', 'id_category', 'language', 'gender']);

        if (count($read) > 0)
        {
            foreach ($read as $key => $value)
            {
                $image = single('operation_first_image', ['operation_id' => $value->id]);
                $read[$key]->image = public_path('uploads/images/') . $image->image;
            }
            return screaming('operations', $read);
        }
        else
        {
            return screaming('operations_fail');
        }
    }
}
