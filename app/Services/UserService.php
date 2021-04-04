<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class UserService
{
    public function create(object $data)
    {
        $user = new User();

        $document = $this->clean_document($data->document);

        $user->email = $data->email;
        $user->name = $data->name;
        $user->user_type = $data->user_type;
        $user->code = Str::uuid();
        $user->password = bcrypt($data->password);
        $user->document = $document;

        $user->saveOrFail();

        return $user;
    }


    protected function clean_document($value)
    {
        $value = trim($value);
        $value = str_replace(".", "", $value);
        $value = str_replace(",", "", $value);
        $value = str_replace("-", "", $value);
        $value = str_replace("/", "", $value);
        return $value;
    }
}
