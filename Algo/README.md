# FizzBuzz – Algorithmic Exercise

This folder contains my implementation of the classic **FizzBuzz** problem in PHP.

---

### Approach

Two different implementations are provided:

1. **`fizzbuzz.php`** – a simple, functional version.  
   - Straightforward loop from 1 to N.  
   - Prints "Fizz" for multiples of 3, "Buzz" for multiples of 5, "FizzBuzz" for both.  
   - Includes input validation and error handling via `STDERR`.

2. **`fizzbuzzOOP.php`** – an object-oriented version.  
   - Encapsulates the logic inside a `FizzBuzz` class.  
   - Rules are configurable through the constructor:  
     ```php
     new FizzBuzz([3 => 'Fizz', 5 => 'Buzz', 7 => 'Pop']);
     ```
   - Extensibility and clean separation of responsibilities (`compute()` vs `run()`).

---

#### How to Run

```bash
php fizzbuzz.php 15
# or
php fizzbuzzOOP.php 15
```
If no argument is provided, the default value is 20.

Invalid inputs (negative, zero, or non-numeric) will produce an error message on STDERR.

#### Example Output
1
2
Fizz
4
Buzz
Fizz
7
8
Fizz
Buzz
11
Fizz
13
14
FizzBuzz