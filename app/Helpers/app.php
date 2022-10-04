<?php
function getCurrentGender()
{
    $request = Request();

    $datas = $request->all();

    return $datas['gender'] ?? getUserParam('gender', 1);
}
