<?php

function queries()
{
    return [
        'messages' => 'select * from responses where code = :code and language = :language limit 1',
        'set_message' => 'insert into responses (code, message, status, language) values (:code, :message, :status, :language) limit 1',
        'language' => 'select id from languages where short_tag = :short_tag limit 1',
        'code' => 'select * from responses where message = :message and language = :language limit 1',
        'login' => 'select * from users where email = :email and password = :password limit 1',
        'genders'=> 'select * from genders where language = :language order by id asc',
        'operations' => 'select * from operations where language = :language and gender = :gender order by id desc',
        'gender' => 'select * from genders where id = :id limit 1',
        'operation_first_image' => 'select * from operation_images where operation_id = :operation_id limit 1',
        'operation' => 'select * from operations where id = :id limit 1',
        'operation_images' => 'select * from operation_images where operation_id = :operation_id order by id desc',
        'doctor' => 'select name from doctors where id = :id limit 1',
        'doctor_lang' => 'select * from doctor_lang where doctor_id = :doctor_id and language = :language limit 1',
        'operations_search' => "select * from operations where language = :language and gender = :gender and title like :title or desciription like :desciription or content like :content order by id desc",
        'operations_category' => 'select * from operations where language = :language and gender = :gender and category_id = :category_id order by id desc',
        'operations_search_category' => 'select * from operations where language = :language and gender = :gender and category_id = :category_id and title like :title or desciription like :desciription or content like :content order by id desc',
        'operation_categories' => 'select * from operation_categories where language = :language order by id desc',
        'doctors' => 'select * from doctors order by id desc',
        'doctor_first_image' => 'select * from doctor_images where doctor_id = :doctor_id order by id asc limit 1',
        'doctor_images' => 'select * from doctor_images where doctor_id = :doctor_id order by id asc',
        'settings' => 'select * from settings where id = 1',
        'app_update' => 'update settings set :key = :value where id = 1 limit 1',
        'active' => 'select active from settings where id = 1',
        'reset' => 'insert into reset_password (code, user_id, date) values (:code, :user_id, :date)',
        'reset_delete' => 'delete from reset_password where user_id = :user_id',
        'reset_code' => 'select * from reset_password where code = :code and date < :date order by id desc limit 1',
        'update_response' => 'update responses set message = :message where code = :code and language = :language limit 1'

    ];
}
