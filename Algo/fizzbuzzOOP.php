<?php
declare(strict_types=1);

/**
 * Class FizzBuzz
 *
 * Encapsulates the FizzBuzz logic.
 * Extensible by passing custom rules to the constructor.
 */
final class FizzBuzz
{
    /** @var array<int, string> */
    private array $rules;

    /**
     * @param array<int, string> $rules
     *  Key: divisor → Value: word to display
     *  Example: [3 => "Fizz", 5 => "Buzz"]
     */
    public function __construct(array $rules = [3 => 'Fizz', 5 => 'Buzz'])
    {
        $this->rules = $rules;
    }

    /**
     * Compute the FizzBuzz output for a single number.
     *
     * @param int $number
     * @return string
     */
    public function compute(int $number): string
    {
        $output = '';

        foreach ($this->rules as $divisor => $word) {
            if ($number % $divisor === 0) {
                $output .= $word;
            }
        }

        return $output !== '' ? $output : (string)$number;
    }

    /**
     * Print the FizzBuzz sequence from 1 to the given limit.
     *
     * @param int $limit
     * @return void
     */
    public function run(int $limit): void
    {
        if ($limit <= 0) {
            fwrite(STDERR, "Error: limit must be a positive integer." . PHP_EOL);
            exit(1);
        }

        for ($i = 1; $i <= $limit; $i++) {
            echo $this->compute($i), PHP_EOL;
        }
    }
}

/**
 * Entry point when running from CLI.
 * Reads the first argument or defaults to 20.
 */
$limit = (int)($argv[1] ?? 20);

$fizzBuzz = new FizzBuzz();
// Example of extending rules:
// $fizzBuzz = new FizzBuzz([3 => 'Fizz', 5 => 'Buzz', 7 => 'Pop']);
$fizzBuzz->run($limit);
