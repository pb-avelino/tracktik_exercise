# TrackTik Excercise

## Installation

```bash
$ composer install
```

## Run Exercise
The exercise uses the Symfony console command.

```bash
$ php tracktik tt:exercise <action>
```
Then follow the instruction on the screen.

### Actions:
   * create : Create an electronic Item
   * q1a: Question 1 on developer evaluation (PO created by the program).
   * q1m: Question 1 on developer evaluation (PO is created by the user).
   * q2: Question 2 on customer evaluation.

#### Sample call:
```bash
$ php tracktik tt:exercise q2
```

### Display Help
```bash
$ php tracktik tt:exercise -h
```

## Run Tests

```bash
$ ./bin/behat
```
