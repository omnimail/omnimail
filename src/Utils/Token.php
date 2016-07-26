<?php

namespace Omnimail\Utils;

class Token
{
    /**
     * @var array
     */
    private static $characters = [
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'o', 'j', 'k', 'l',
        'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
        'E', 'F', 'G', 'H', 'O', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U',
        'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
    ];

    /**
     * Generate a random string
     *
     * @param int $length
     * @param mixed $type
     * @param bool $caseSensitive
     * @return string
     */
    public static function generate($length = 128, $type = 'alnum', $caseSensitive = true)
    {
        $characters = self::$characters;

        // Provided characters
        if (is_array($type)) {
            $characters = $type;
        } // Hexa decimal
        elseif ($type === 'hexa') {
            $characters = array_merge(array_slice(self::$characters, 52), array_slice(self::$characters, 26, 6));
        } // Case insensitive alpha numeric
        elseif ($type === 'alnum' && $caseSensitive === false) {
            $characters = array_slice(self::$characters, 26);
        } // Alphabet only
        elseif ($type === 'alpha') {
            $characters = array_slice(self::$characters, 0, 26);
        } // Numbers only
        elseif ($type === 'digit' || $type === 'numeric') {
            $characters = array_slice(self::$characters, 52);
        }

        return self::make($characters, $length);
    }

    /**
     * Make the random string
     *
     * @param string $characters
     * @param int $length
     * @return string
     */
    private static function make($characters, $length)
    {
        $token = '';

        do {
            $token .= $characters[random_int(0, count($characters) - 1)];
        } while (strlen($token) < $length);

        return $token;
    }
}
