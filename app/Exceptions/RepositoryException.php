<?php
namespace App\Exceptions;

use Exception;

class RepositoryException extends Exception implements RepositoryException
{
    protected $context;

    public function __construct(string $message, int $code = 500, array $context = [])
    {
        parent::__construct($message, $code);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function render($request)
    {
        return response([
            'status' => $this->code,
            'success' => 'failure',
            'message' => $this->getMessage(),
            'data' => $this->getContext(),
        ], $this->code);
    }
}
