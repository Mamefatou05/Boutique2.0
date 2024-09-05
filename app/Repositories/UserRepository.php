<?php

namespace App\Repositories;

use App\Enums\Role as EnumsRole;
use App\Enums\StatutEnum;
use App\Models\Client;
use App\Models\User;
use App\Models\Role;
use App\Services\UploadService;
use App\Services\UserQrCodeEmail;
use Illuminate\Support\Facades\Mail;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class UserRepository implements UserRepositoryInterface
{

    public function createUser(array $data)
    {
        return Client::create($data);

    }
}