# Fulll – Backend Developer Tests

**Candidate:** Jean-Michel Cornec  
**Position:** PHP / Symfony Backend Developer  
**Date:** October 2025

---

## Overview

This repository contains the two technical exercises requested by **Fulll**:

1. **Algorithmic exercise** — classic *FizzBuzz* implemented in both procedural and object-oriented styles.  
2. **Backend exercise (PHP)** — business logic implementation validated by Behat tests 

Each exercise is self-contained in its own folder, with a dedicated README for context and usage.

---

## 1) Algorithmic Exercise – FizzBuzz

**Location:** `./Algo/`

**Description:** Display numbers from 1 to N using these rules:
- Divisible by **3** → `Fizz`
- Divisible by **5** → `Buzz`
- Divisible by **3 and 5** → `FizzBuzz`
- Otherwise → print the number itself

Two implementations are provided:
- `fizzbuzz.php` — simple procedural version (direct and readable)
- `fizzbuzzOOP.php` — object-oriented version with a `FizzBuzz` class (extensible)

**How to run**
```bash
cd Algo
php fizzbuzz.php 15
# or
php fizzbuzzOOP.php 15 
```
**Example Output**

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

---
## 2) Backend Exercise – PHP (Intermediate / Senior)

**Location:** `./Backend/`

**Description:**  
This exercise is based on Fulll’s official hiring boilerplate.  
The goal is to implement the backend logic described in the Behat feature files and make all automated tests pass.

**Objective:**
- Implement the required domain logic using modern PHP.  
- Use **Behat** (Behavior Driven Development) for automated functional tests.  
- Ensure all test scenarios are **green** (successfully passing).

**How to run**
```bash
cd Backend
composer install
vendor/bin/behat
```
**Example Output**
4 scenarios (4 passed)
12 steps (12 passed)
