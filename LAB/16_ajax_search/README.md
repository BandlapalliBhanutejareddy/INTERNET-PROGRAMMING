# Experiment 16: Asynchronous Live Search using AJAX & Fetch API

## Title
Build an asynchronous book search interface consuming a JSON backend API using modern JavaScript `fetch()` without full page reloads.

## Aim
To understand AJAX paradigms, asynchronous requests, JSON serialization, and dynamic DOM rendering.

## Algorithm
1. Attach an `input` event listener to the search input element.
2. Intercept typed query; if character length >= 2, dispatch an asynchronous GET request via `fetch('../../books/search_ajax.php?q=' + encodeURIComponent(q))`.
3. In the PHP backend, execute a parameterized query matching title, author, or ISBN and return results using `json_encode($results)`.
4. On client response, parse JSON array and dynamically construct DOM result rows.

## Execution Instructions
Open `http://localhost:8000/LAB/16_ajax_search/index.html`.
