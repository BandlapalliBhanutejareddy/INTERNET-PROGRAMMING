// Experiment 05 - DOM Manipulation
const newBookInput = document.getElementById('newBookInput');
const addBookBtn = document.getElementById('addBookBtn');
const filterInput = document.getElementById('filterInput');
const bookList = document.getElementById('bookList');
const countBadge = document.getElementById('countBadge');

function updateCount() {
    const total = bookList.querySelectorAll('li').length;
    countBadge.textContent = `Total Books: ${total}`;
}

// 1. Add Book Dynamically
addBookBtn.addEventListener('click', () => {
    const title = newBookInput.value.trim();
    if (!title) {
        alert('Please enter a book title!');
        return;
    }

    const li = document.createElement('li');
    li.innerHTML = `
        <span>${title}</span>
        <button class="del-btn" onclick="removeBook(this)">&times; Delete</button>
    `;
    bookList.appendChild(li);
    newBookInput.value = '';
    updateCount();
});

// 2. Remove Book
window.removeBook = function(button) {
    const li = button.closest('li');
    li.remove();
    updateCount();
};

// 3. Live DOM Search Filter
filterInput.addEventListener('keyup', () => {
    const query = filterInput.value.toLowerCase();
    const items = bookList.querySelectorAll('li');
    items.forEach(item => {
        const text = item.querySelector('span').textContent.toLowerCase();
        item.style.display = text.includes(query) ? 'flex' : 'none';
    });
});
