<?php

namespace App\Http\Controllers;

class DoctorController extends Controller
{
    public function read($id = 0)
    {
        if ($id != 0)
        {
            $read = single('doctor', ['id' => $id]);

            if ($read != false)
            {
                $lang = single('doctor_lang', ['language' => getCurrentLanguage(), 'doctor_id' => $id]);
                $read->title = $lang->title;
                $read->desciription = $lang->desciription;
                $images = array();
                foreach (selecter('doctor_images', ['doctor_id' => $id]) as $image)
                {
                    $images[] = public_path('uploads/images/') . $image->image;
                }
                $read->images = $images;

                return screaming('doctor', $read);
            }
            else
            {
                return screaming('doctor.fail');
            }
        }

        $read = selecter('doctors');

        if (count($read) > 0)
        {
            foreach ($read as $key => $item)
            {
                $lang = single('doctor_lang', ['language' => getCurrentLanguage(), 'doctor_id' => $item->id]);
                $image = single('doctor_first_image', ['doctor_id' => $item->id]);
                $read[$key]->title = $lang->title;
                $read[$key]->desciription = $lang->desciription;
                $read[$key]->image = public_path('uploads/images/') . $image->image;
            }

            return screaming('doctors', $read);
        }
        else
        {
            return screaming('doctors_fail');
        }
    }
}
