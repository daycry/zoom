<?php namespace Daycry\Zoom\Exceptions;

class ZoomException extends \RuntimeException implements ExceptionInterface
{
    public static function forIncorrectState()
    {
        return new self( lang( 'Zoom.invalidState' ) );
    }

    public static function forTokenExpired()
    {
        return new self( lang( 'Zoom.tokenExpired' ) );
    }
}