# Experiment 03: Responsive CSS3 Layouts & Card Design

## Title
Implement a responsive book catalog grid using CSS3 Flexbox, CSS Grid, custom CSS variables, and media queries.

## Aim
To master responsive front-end design techniques by creating interactive card layouts that automatically adapt across desktop, tablet, and mobile device screens.

## Algorithm
1. Establish a root styling block (`:root`) with CSS custom variables for themes, colors, and typography.
2. Structure the HTML container with a `.book-grid` class and multiple `<article class="card">` items.
3. Configure `.book-grid` with `display: grid` and `grid-template-columns: repeat(3, 1fr)` for a 3-column desktop layout.
4. Style each card using `display: flex` and `flex-direction: column` with `justify-content: space-between` to ensure uniform card height.
5. Add CSS transitions (`transform`, `box-shadow`) on card hover states.
6. Style status badges dynamically based on book availability.
7. Create media queries at `max-width: 900px` (2 columns) and `max-width: 600px` (single column) to achieve responsiveness.

## Source Code
See `index.html` and `style.css` in this directory.

## Sample Input
Render `index.html` and resize the browser window from 1200px down to 400px.

## Expected Output
- Desktop (> 900px): 3 equal columns with hover animation.
- Tablet (601px - 900px): 2 balanced columns.
- Mobile (<= 600px): Stacked single-column vertical layout.

## Execution Instructions
1. Open `LAB/03_css_styling/index.html` in Chrome or Firefox.
2. Press `F12` to open Developer Tools and toggle device emulation to inspect responsive transitions.
