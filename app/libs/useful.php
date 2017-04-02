<?php

if (!function_exists('array_pluck')) {
    /**
     * Pluck an array of values from an array. (Only for PHP 5.3+).
     *
     * @param  $array - data
     * @param  $key - value you want to pluck from array
     *
     * @return plucked array only with key data
     */
    function array_pluck($array, $key)
    {
        return array_map(function ($v) use ($key) {
            return is_object($v) ? $v->$key : $v[$key];
        }, $array);
    }
}

/**
 * Check if a string starts with the given string.
 *
 * @param string $string
 * @param string $starts_with
 *
 * @return bool
 */
function starts_with($string, $starts_with)
{
    return strpos($string, $starts_with) === 0;
}

/**
 * Check if a string ends with the given string.
 *
 * @param string $string
 * @param string $ends_with
 *
 * @return bool
 */
function ends_with($string, $ends_with)
{
    return substr($string, -strlen($ends_with)) === $ends_with;
}

/**
 * Generates a string of random characters.
 *
 * @throws LengthException If $length is bigger than the available
 *                         character pool and $no_duplicate_chars is
 *                         enabled
 *
 * @param int  $length             The length of the string to
 *                                 generate
 * @param bool $human_friendly     Whether or not to make the
 *                                 string human friendly by
 *                                 removing characters that can be
 *                                 confused with other characters (
 *                                 O and 0, l and 1, etc)
 * @param bool $include_symbols    Whether or not to include
 *                                 symbols in the string. Can not
 *                                 be enabled if $human_friendly is
 *                                 true
 * @param bool $no_duplicate_chars whether or not to only use
 *                                 characters once in the string
 *
 * @return string
 */
function random_string($length = 16, $human_friendly = true, $include_symbols = false, $no_duplicate_chars = false)
{
    $nice_chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefhjkmnprstuvwxyz23456789';
    $all_an = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
    $symbols = '!@#$%^&*()~_-=+{}[]|:;<>,.?/"\'\\`';
    $string = '';

    // Determine the pool of available characters based on the given parameters
    if ($human_friendly) {
        $pool = $nice_chars;
    } else {
        $pool = $all_an;

        if ($include_symbols) {
            $pool .= $symbols;
        }
    }

    if (!$no_duplicate_chars) {
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    // Don't allow duplicate letters to be disabled if the length is
    // longer than the available characters
    if ($no_duplicate_chars && strlen($pool) < $length) {
        throw new \LengthException('$length exceeds the size of the pool and $no_duplicate_chars is enabled');
    }

    // Convert the pool of characters into an array of characters and
    // shuffle the array
    $pool = str_split($pool);
    $poolLength = count($pool);
    $rand = mt_rand(0, $poolLength - 1);

    // Generate our string
    for ($i = 0; $i < $length; ++$i) {
        $string .= $pool[$rand];

        // Remove the character from the array to avoid duplicates
        array_splice($pool, $rand, 1);

        // Generate a new number
        if (($poolLength - 2 - $i) > 0) {
            $rand = mt_rand(0, $poolLength - 2 - $i);
        } else {
            $rand = 0;
        }
    }

    return $string;
}

/**
 * Generate secure random string of given length
 * If 'openssl_random_pseudo_bytes' is not available
 * then generate random string using default function.
 *
 * Part of the Laravel Project <https://github.com/laravel/laravel>
 *
 * @param int $length length of string
 *
 * @return bool
 */
function secure_random_string($length = 16)
{
    if (function_exists('openssl_random_pseudo_bytes')) {
        $bytes = openssl_random_pseudo_bytes($length * 2);

        if ($bytes === false) {
            throw new \LengthException('$length is not accurate, unable to generate random string');
        }

        return substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $length);
    }

    return random_string($length);
}

/**
 * Removes a directory (and its contents) recursively.
 *
 * Contributed by Askar (ARACOOL) <https://github.com/ARACOOOL>
 *
 * @param string $path             The path to be deleted recursively
 * @param bool   $traverseSymlinks Delete contents of symlinks recursively
 *
 * @return bool
 *
 * @throws \RuntimeException
 */
function delete($path, $traverseSymlinks = false)
{
    $path = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
    if (!file_exists($path)) {
        return true;
    } elseif (!is_dir($path)) {
        $files = [$path];
    } else {
        $files = scandir($path);
    }

    if (!is_link($path) || $traverseSymlinks) {
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $currentPath = $path.DIRECTORY_SEPARATOR.$file;

            if (is_dir($currentPath)) {
                delete($currentPath, $traverseSymlinks);
            } elseif (!unlink($currentPath)) {
                throw new \RuntimeException('Unable to delete '.$currentPath);
            }
        }
    }

    // Windows treats removing directory symlinks identically to removing directories.
    if (is_link($path) && !defined('PHP_WINDOWS_VERSION_MAJOR')) {
        if (!unlink($path)) {
            throw new \RuntimeException('Unable to delete '.$path);
        }
    } else {
        if (!rmdir($path)) {
            throw new \RuntimeException('Unable to delete '.$path);
        }
    }

    return true;
}

/**
 * Checks to see if the page is being served over SSL or not.
 *
 * @return bool
 */
function isHTTPS($trust_proxy_headers = false)
{
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        // Check the standard HTTPS headers
        return true;
    } elseif ($trust_proxy_headers && isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        // Check proxy headers if allowed
        return true;
    } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        return true;
    }

    return false;
}

/**
 * Truncate a string to a specified length without cutting a word off.
 *
 * @param string $string The string to truncate
 * @param int    $length The length to truncate the string to
 * @param string $append Text to append to the string IF it gets
 *                       truncated, defaults to '...'
 *
 * @return string
 */
function safe_truncate($string, $length, $append = '...')
{
    $ret = substr($string, 0, $length);
    $last_space = strrpos($ret, ' ');

    if ($last_space !== false && $string != $ret) {
        $ret = substr($ret, 0, $last_space);
    }

    if ($ret != $string) {
        $ret .= $append;
    }

    return $ret;
}
/**
 * Transmit headers that force a browser to display the download file
 * dialog. Cross browser compatible. Only fires if headers have not
 * already been sent.
 *
 * @param string $filename The name of the filename to display to
 *                         browsers
 * @param string $content  The content to output for the download.
 *                         If you don't specify this, just the
 *                         headers will be sent
 *
 * @return bool
 */
function force_download($filename, $content = false)
{
    if (!headers_sent()) {
        // Required for some browsers
        if (ini_get('zlib.output_compression')) {
            @ini_set('zlib.output_compression', 'Off');
        }

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

        // Required for certain browsers
        header('Cache-Control: private', false);

        header('Content-Disposition: attachment; filename="'.basename(str_replace('"', '', $filename)).'";');
        header('Content-Type: application/force-download');
        header('Content-Transfer-Encoding: binary');

        if ($content) {
            header('Content-Length: '.strlen($content));
        }

        ob_clean();
        flush();

        if ($content) {
            echo $content;
        }

        return true;
    }

    return false;
}
