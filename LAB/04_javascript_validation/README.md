# Experiment 04: Client-Side Form Validation using JavaScript

## Title
Implement comprehensive client-side form validation using regular expressions and DOM error reporting.

## Aim
To prevent invalid user submissions by writing JavaScript validation functions that check registration numbers, alphabetic names, valid email formats, and password complexity criteria with immediate user feedback.

## Algorithm
1. Attach an `addEventListener('submit')` listener to the form element and execute `event.preventDefault()` to intercept submission.
2. Clear any preexisting validation error messages.
3. Validate Student ID against the regular expression `/^\d{9}$/` (exactly 9 digits).
4. Validate Student Name using `/^[A-Za-z\s]{3,}$/` ensuring minimum length and alphabetic constraints.
5. Validate Email using standard RFC 5322 regex pattern.
6. Validate Password using a lookahead regex `/^(?=.*[A-Z])(?=.*\d).{8,}$/` verifying at least 8 characters, one capital letter, and one numeric digit.
7. Display corresponding error text under invalid fields.
8. If all checks pass, display the green success banner.

## Source Code
See `index.html` and `validation.js` in this directory.

## Sample Input
- ID: `192325016`
- Name: `Bhanuteja Reddy`
- Email: `bhanu@college.edu`
- Password: `Password@123`

## Expected Output
Form displays: "Validation Successful! Form submitted." with no red error messages.

## Execution Instructions
1. Open `LAB/04_javascript_validation/index.html` in a web browser.
2. Enter invalid values (e.g., short password or symbols in name) to see instant feedback.
