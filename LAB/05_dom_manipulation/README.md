# Experiment 05: Dynamic DOM Manipulation & Real-Time Filtering

## Title
Manipulate the Document Object Model (DOM) to dynamically add, filter, and remove library book items using vanilla JavaScript.

## Aim
To understand DOM traversal, element creation (`createElement`), event delegation, node removal (`remove`), and real-time list filtering using `addEventListener('keyup')`.

## Algorithm
1. Cache references to the input fields, button, and unordered list using `document.getElementById()`.
2. Attach a click event handler to the "Add Book" button.
3. Validate user input; if non-empty, create a new `<li>` element using `document.createElement('li')`.
4. Inject the book title and a delete button into the `<li>` and append it using `bookList.appendChild(li)`.
5. Attach a `keyup` event listener to the filter search field.
6. On keyup, retrieve the search string, iterate over all `<li>` child nodes, and set `style.display` to `flex` if matched or `none` if not.
7. Provide a function to delete a book item and update the total counter.

## Source Code
See `index.html` and `app.js` in this directory.

## Sample Input
- Type "Database System Concepts" and click "Add Book".
- Type "algorithms" into the filter box.

## Expected Output
- New book is appended to the DOM list immediately and counter increments to 4.
- Typing "algorithms" filters the list to show only the CLRS book.

## Execution Instructions
1. Open `LAB/05_dom_manipulation/index.html` in any browser.
2. Add, filter, and delete books to observe dynamic DOM updates without page reloads.
