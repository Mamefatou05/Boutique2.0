<?php
namespace App\Services;

interface ClientServiceInterface
{
    public function getAllClients($request);
    public function getClientById($id);
    public function createClient($data);
    public function updateClient($id, $data);
    public function deleteClient($id);
}
