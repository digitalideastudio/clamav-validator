<?php namespace Sunspikes\ClamavValidator;

use Illuminate\Validation\ValidationException;

class ClamavValidatorException extends ValidationException
{
    protected $exception;

    public $attribute;

    public function __construct(\Exception $exception, string $attribute, \Illuminate\Contracts\Validation\Validator $validator, $response = null)
    {
        $this->exception = $exception;
        $this->attribute = $attribute;

        parent::__construct($validator, $this->makeResponse());
    }

    public function makeResponse()
    {
        $message = $this->exception->getMessage();

        if (substr_count($message, 'SOCKET_ECONNREFUSED')) {
            $message = 'Unable to initiate antivirus check. Please try later.';
        }

        if (request()->expectsJson()) {
            return response()->json([
                $this->attribute => $message
            ], 422);
        }

        return redirect()->back()->withInput(
            request()->input()
        );
    }
}
