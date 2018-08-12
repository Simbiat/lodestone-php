<?php

namespace Lodestone\Modules;

use Exception,
    Throwable;
use Lodestone\Modules\Validator;

/**
 * Class Exceptions
 *
 * @package Lodestone\Validator
 */
class Exceptions extends Exception
{
    /**
     * Exceptions constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param $validator Validator
     * @return Exceptions
     */
    public static function emptyValidation($validator)
    {
        if ($validator->id != null) {
            $message = sprintf("%s cannot be empty for id: %d.",
                $validator->name,
                $validator->id
            );
        } else {
            $message = sprintf("%s cannot be empty.",
                $validator->name
            );
        }

        return new Exceptions($message);
    }
}