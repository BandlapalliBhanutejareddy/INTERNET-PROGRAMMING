# Experiment 02: HTML5 Form Design & Controls

## Title
Construct a comprehensive student registration form utilizing modern HTML5 form inputs, input types, and native validation attributes.

## Aim
To design an interactive form collecting student details for library registration using various input types (`text`, `email`, `password`, `tel`, `date`, `radio`, `checkbox`, `select`, `textarea`) and native attributes (`required`, `pattern`, `minlength`, `placeholder`).

## Algorithm
1. Initialize the HTML5 document with appropriate viewport and styling.
2. Create a container card and embed a `<form>` element specifying `method="POST"`.
3. Add text input controls for Student Registration Number and Full Name with `placeholder` and `required` attributes.
4. Use `type="email"` for email validation and `type="tel"` with a regex `pattern="[0-9]{10}"` for 10-digit phone verification.
5. Create a `<select>` dropdown menu populated with academic departments.
6. Implement radio buttons sharing the same `name` attribute to allow mutual selection of the study year.
7. Add a date picker using `type="date"` and a password field with `minlength="6"`.
8. Insert a mandatory checkbox for library rules agreement.
9. Provide a submit `<button>` to trigger browser form validation.

## Source Code
See `index.html` in this directory.

## Sample Input
- Registration Number: `192325016`
- Name: `Bhanuteja Reddy`
- Email: `bhanu@college.edu`
- Department: `Computer Science & Engineering`
- Year: `3rd Year`
- Password: `Student@123`

## Expected Output
Browser validates inputs upon submission:
- Empty required fields are highlighted with built-in validation tooltips.
- Invalid email formats and non-matching phone patterns trigger validation prompts.
- Valid submissions proceed without error.

## Execution Instructions
1. Open `LAB/02_html_forms/index.html` in any browser.
2. Test submitting the form with empty and invalid values to observe browser validation.
