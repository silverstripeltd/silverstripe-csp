<?php

namespace Silverstripe\CSP;

class RandomString extends NonceGenerator
{
    public function generate(): string
    {
        return self::random(32);
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     * Thanks Laravel
     */
    public static function random($length = 16): string
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}
