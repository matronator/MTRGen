<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Store;

use RuntimeException;

/**
 * Path
 *
 * Everything except the last method `safe` is taken from webmozart/path-util (https://github.com/webmozart/path-util/blob/master/src/Path.php)
 */
class Path
{
    /**
     * The number of buffer entries that triggers a cleanup operation.
     */
    private const CLEANUP_THRESHOLD = 1250;

    /**
     * The buffer size after the cleanup operation.
     */
    private const CLEANUP_SIZE = 1000;

    /**
     * Buffers input/output of {@link canonicalize()}.
     *
     * @var array<string, string>
     */
    private static $buffer = [];

    /**
     * @var int
     */
    private static $bufferSize = 0;

    public static function canonicalize(string $path): string
    {
        if ('' === $path) {
            return '';
        }

        // This method is called by many other methods in this class. Buffer
        // the canonicalized paths to make up for the severe performance
        // decrease.
        if (isset(self::$buffer[$path])) {
            return self::$buffer[$path];
        }

        // Replace "~" with user's home directory.
        if ('~' === $path[0]) {
            $path = self::getHomeDirectory().substr($path, 1);
        }

        $path = self::normalize($path);

        [$root, $pathWithoutRoot] = self::split($path);

        $canonicalParts = self::findCanonicalParts($root, $pathWithoutRoot);

        // Add the root directory again
        self::$buffer[$path] = $canonicalPath = $root.implode('/', $canonicalParts);
        ++self::$bufferSize;

        // Clean up regularly to prevent memory leaks
        if (self::$bufferSize > self::CLEANUP_THRESHOLD) {
            self::$buffer = \array_slice(self::$buffer, -self::CLEANUP_SIZE, null, true);
            self::$bufferSize = self::CLEANUP_SIZE;
        }

        return $canonicalPath;
    }

    public static function normalize(string $path): string
    {
        return str_replace('\\', '/', $path);
    }

    public static function getHomeDirectory(): string
    {
        // For UNIX support
        if (getenv('HOME')) {
            return self::canonicalize(getenv('HOME'));
        }

        // For >= Windows8 support
        if (getenv('HOMEDRIVE') && getenv('HOMEPATH')) {
            return self::canonicalize(getenv('HOMEDRIVE').getenv('HOMEPATH'));
        }

        throw new RuntimeException("Cannot find the home directory path: Your environment or operating system isn't supported.");
    }

    private static function findCanonicalParts(string $root, string $pathWithoutRoot): array
    {
        $parts = explode('/', $pathWithoutRoot);

        $canonicalParts = [];

        // Collapse "." and "..", if possible
        foreach ($parts as $part) {
            if ('.' === $part || '' === $part) {
                continue;
            }

            // Collapse ".." with the previous part, if one exists
            // Don't collapse ".." if the previous part is also ".."
            if ('..' === $part && \count($canonicalParts) > 0 && '..' !== $canonicalParts[\count($canonicalParts) - 1]) {
                array_pop($canonicalParts);

                continue;
            }

            // Only add ".." prefixes for relative paths
            if ('..' !== $part || '' === $root) {
                $canonicalParts[] = $part;
            }
        }

        return $canonicalParts;
    }

    private static function split(string $path): array
    {
        if ('' === $path) {
            return ['', ''];
        }

        // Remember scheme as part of the root, if any
        if (false !== $schemeSeparatorPosition = strpos($path, '://')) {
            $root = substr($path, 0, $schemeSeparatorPosition + 3);
            $path = substr($path, $schemeSeparatorPosition + 3);
        } else {
            $root = '';
        }

        $length = \strlen($path);

        // Remove and remember root directory
        if (0 === strpos($path, '/')) {
            $root .= '/';
            $path = $length > 1 ? substr($path, 1) : '';
        } elseif ($length > 1 && ctype_alpha($path[0]) && ':' === $path[1]) {
            if (2 === $length) {
                // Windows special case: "C:"
                $root .= $path.'/';
                $path = '';
            } elseif ('/' === $path[2]) {
                // Windows normal case: "C:/"..
                $root .= substr($path, 0, 3);
                $path = $length > 3 ? substr($path, 3) : '';
            }
        }

        return [$root, $path];
    }

    /**
     * Return a filename in Nette safe-stream for reading and writing.
     * @return string
     * @param string $filename
     */
    public static function safe(string $filename): string
    {
        return 'nette.safe://' . $filename;
    }

    public static function getRoot(): string
    {
        return __DIR__ . '/../../../';
    }

    /**
     * Check if a path is absolute (Unix, Windows, or URL scheme).
     */
    private static function isAbsolute(string $path): bool
    {
        if ($path === '') {
            return false;
        }

        $path = self::normalize($path);

        // URL like "phar://", "file://", etc.
        if (strpos($path, '://') !== false) {
            return true;
        }

        // Unix absolute path: starts with "/"
        if ($path[0] === '/') {
            return true;
        }

        // Windows absolute path: "C:/" or "C:\"
        return \strlen($path) > 1 && ctype_alpha($path[0]) && $path[1] === ':';
    }

    public static function makeAbsolute(string $path): string
    {
        // Expand and canonicalize first (handles "~" and normalization)
        $path = self::canonicalize($path);

        // If it's already absolute, just return it
        if (self::isAbsolute($path)) {
            return $path;
        }

        // Otherwise, make it absolute relative to the project root
        return self::canonicalize(self::getRoot() . $path);
    }

    public static function getExtension(string $path): string
    {
        $parts = explode('.', $path);
        // return $parts[count($parts) - 1];
        return end($parts);
    }
}
