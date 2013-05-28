<?php
/**
 * Simple UUID generation functionality, using Zend Framework 2's Zend\Math\Rand component.
 *
 * @link      http://github.com/MichaelGooden/MdgUuid for the canonical source repository
 * @copyright Copyright (c) 2013 Michael Gooden (http://michaelgooden.github.com)
 */
namespace MdgUuid;

use Zend\Math\Rand;

/**
 * UUID Generator
 */
abstract class Generator
{
    /**
     * Generate a version 4 UUID using random bytes from OpenSSL or Mcrypt and mt_rand() as fallback
     *
     * @param  bool $strong  true if you need a strong random generator
     * @return string
     * @throws Exception\RuntimeException
     */
    public static function getV4($strong = false)
    {
        /**
         * Original function courtesy of Victor Smirnov
         * @see http://stackoverflow.com/a/15874518
         */
        $randomString = Rand::getBytes(16, $strong);
        $time_low = bin2hex(substr($randomString, 0, 4));
        $time_mid = bin2hex(substr($randomString, 4, 2));
        $time_hi_and_version = bin2hex(substr($randomString, 6, 2));
        $clock_seq_hi_and_reserved = bin2hex(substr($randomString, 8, 2));
        $node = bin2hex(substr($randomString, 10, 6));

        /**
         * Set the four most significant bits (bits 12 through 15) of the
         * time_hi_and_version field to the 4-bit version number from
         * Section 4.1.3.
         * @see http://tools.ietf.org/html/rfc4122#section-4.1.3
         */
        $time_hi_and_version = hexdec($time_hi_and_version);
        $time_hi_and_version = $time_hi_and_version >> 4;
        $time_hi_and_version = $time_hi_and_version | 0x4000;

        /**
         * Set the two most significant bits (bits 6 and 7) of the
         * clock_seq_hi_and_reserved to zero and one, respectively.
         */
        $clock_seq_hi_and_reserved = hexdec($clock_seq_hi_and_reserved);
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;
        return sprintf('%08s%04s%04x%04x%012s', $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node);
    }
}
