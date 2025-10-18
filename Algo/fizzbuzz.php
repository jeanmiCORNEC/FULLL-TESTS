<?php
declare(strict_types=1);

/**
 * Prints the FizzBuzz sequence from 1 to the given limit.
 *
 * @param int $limit Upper limit of the sequence (must be positive)
 * @return void
 */
function fizzBuzz(int $limit): void
{
    if ($limit <= 0) {
        fwrite(STDERR, "Error: limit must be a positive integer." . PHP_EOL);
        exit(1);
    }

    for ($i = 1; $i <= $limit; $i++) {
        $output = '';

        if ($i % 3 === 0) {
            $output .= 'Fizz';
        }

        if ($i % 5 === 0) {
            $output .= 'Buzz';
        }

        echo $output !== '' ? $output : $i, PHP_EOL;
    }
}

/**
 * Entry point when running from CLI.
 * Reads the first argument or defaults to 20.
 */
$limit = (int)($argv[1] ?? 20);
fizzBuzz($limit);
