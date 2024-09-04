<?php

namespace App\Repositories;

use Illuminate\Http\Request;

interface ClientRepositoryInterface
{
    public function getAllClients(Request $request);

    public function getClientById($id);

    public function createClient(array $data);

    public function updateClient($id, array $data);

    public function deleteClient($id);
}
