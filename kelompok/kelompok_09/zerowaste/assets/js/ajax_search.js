document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById('filterForm');
    const container = document.getElementById('foodContainer');

    const loadData = () => {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();

        container.style.opacity = '0.5'; 

        fetch(`actions/api_search.php?${params}`)
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
                container.style.opacity = '1'; 
            })
            .catch(err => console.error('Error:', err));
    };


    form.addEventListener('submit', (e) => {
        e.preventDefault(); 
        loadData(); 
    });

    const searchInput = form.querySelector('input[name="q"]');
    if (searchInput) {
        searchInput.addEventListener('keyup', loadData);
    }

    const filters = form.querySelectorAll('input[type="checkbox"], select');
    filters.forEach(input => {
        input.addEventListener('change', () => {
            loadData(); 
        });
    });
    
    loadData();
});