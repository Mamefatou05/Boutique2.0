<?php
namespace App\Exceptions;

use Exception;

interface RepositoryExceptionInterface extends Exception 
{

    public function getContext(): array;

    public function render($request);
}
