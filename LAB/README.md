# Internet Programming Lab &mdash; Course CSA4315

## Project Title
Internet Programming Lab: Basic HTML5 & CSS3 Website

## Aim
To create a clean, responsive, beginner-friendly academic laboratory website using pure HTML5 and embedded CSS3 that demonstrates semantic document structure, section navigation, experiment cards, curriculum overview, and an accessible contact form.

## Technologies Used
- **HTML5**: Semantic tags (`<header>`, `<nav>`, `<section>`, `<article>`, `<footer>`, `<form>`).
- **CSS3**: Embedded `<style>` with CSS custom properties (variables), CSS Grid, Flexbox, media queries, and smooth scrolling.
- **Tools**: Standard web browser (Google Chrome, Microsoft Edge, Mozilla Firefox) with zero build steps or dependencies.

## Key Features
1. **Semantic Structure**: Meaningful markup utilizing standard HTML5 elements for optimal accessibility.
2. **Navigation Bar**: Working anchor links jumping to `#home`, `#experiments`, `#about`, and `#contact`.
3. **Introduction Banner**: Clear course description with university affiliation.
4. **Experiment Cards**: Grid-based layout showcasing the five fundamental experiments:
   - Experiment 1: Basic HTML Structure
   - Experiment 2: HTML Forms
   - Experiment 3: CSS Styling
   - Experiment 4: Tables and Lists
   - Experiment 5: Links and Images
5. **Curriculum Section**: Informational card detailing student learning outcomes.
6. **Accessible Contact Form**: Clean form with appropriate labels, inputs, text area, and explicit demonstration disclaimer.
7. **Responsive Design**: Adapts cleanly across desktop monitors, tablets, and smartphones without external frameworks.

## Steps to Run the Website
1. Clone or download this repository:
   ```bash
   git clone https://github.com/BandlapalliBhanutejareddy/INTERNET-PROGRAMMING.git
   ```
2. Navigate to the `LAB` folder:
   ```bash
   cd INTERNET-PROGRAMMING/LAB
   ```
3. Open `index.html` in any web browser:
   - On Windows: Double-click `index.html` or run `start index.html` in PowerShell.
   - Alternatively, drag and drop `index.html` into your browser window.

## Instructions for Editing the HTML
1. Open `LAB/index.html` in any text editor or IDE (Antigravity, VS Code, Notepad).
2. To add or modify experiments, locate the `<div class="experiments-grid">` block and edit or duplicate an `<article class="exp-card">` element.
3. To alter styling, edit the CSS rules contained within the `<style>` element inside the `<head>` tag.
4. Save the file and refresh your browser (`F5` or `Ctrl + R`) to preview changes immediately.

## Browser Testing Instructions
- **Chrome / Edge / Firefox**: Verify that headings, cards, and buttons render cleanly.
- **Anchor Navigation**: Click each item in the top navigation menu (Home, Experiments, About, Contact) to confirm smooth page scrolling to each section ID.
- **Mobile Emulation**: Press `F12` to open Developer Tools, click the Device Toggle icon (`Ctrl + Shift + M`), and test screen widths (375px, 768px, 1200px) to verify responsive grid adjustments.\n