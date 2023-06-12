<?php

namespace LaraFiler\Response;

use LaraFiler\Exception\LaraFilerException;
use Symfony\Component\HttpFoundation\Response;

class LaraFilerResponse extends Response
{

    protected $message = null;
    protected $status = null;
    public function __construct($message, $status)
    {
        parent::__construct($message, $status);
        $this->message = $message;
        $this->status = $status;
        if (config('larafm.exeption_handling', false) && $status > 399) {
            throw new LaraFilerException($message, $status);
        }
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getStatus()
    {
        return $this->status;
    }
}