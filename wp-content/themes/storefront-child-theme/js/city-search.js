/**
 * This script adds functionality for searching and displaying cities with pagination.
 * It includes input debounce for search queries, a loading indicator, and dynamic pagination rendering.
 * The script expects specific DOM elements and an AJAX endpoint for fetching city data.
 */
document.addEventListener('DOMContentLoaded', function () {
    let debounceTimeout;
    let currentPage = 1;

    const cityResults = document.getElementById('city-results');
    const searchInput = document.getElementById('city-search-input');

    if (!cityResults || !searchInput) {
        console.error('Required DOM elements not found!');
        return;
    }

    /**
     * Displays or hides the loading indicator.
     * @param {boolean} isLoading - Show or hide the loading indicator.
     */
    function showLoading(isLoading) {
        const loadingDiv = document.getElementById('loading-indicator');
        if (isLoading) {
            if (!loadingDiv) {
                const newLoadingDiv = document.createElement('div');
                newLoadingDiv.id = 'loading-indicator';
                newLoadingDiv.textContent = 'Loading...';
                newLoadingDiv.style.color = 'blue';
                cityResults.before(newLoadingDiv);
            }
        } else if (loadingDiv) {
            loadingDiv.remove();
        }
    }

    /**
     * Fetches city data from the db based on the search query and page number.
     * If the search query is empty, fetches all available city data from the db.
     * @param {string} [query=''] - The search query to filter cities by name.
     * @param {number} [page=1] - The page number for paginated results.
     */
    async function fetchCities(query = '', page = 1) {
        showLoading(true);

        const buttons = document.querySelectorAll('.pagination-btn');
        buttons.forEach(btn => (btn.disabled = true)); // Disable pagination buttons during fetch

        try {
            const response = await fetch(citySearchData.ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'search_cities',
                    query: query,
                    paged: page,
                    security: citySearchData.security,
                }),
            });

            if (!response.ok) throw new Error(`Network response was not ok ${response.status}`);

            const { success, data } = await response.json();
            if (!success) throw new Error('Error fetching data');

            const { cities, pages: totalPages } = data;

            cityResults.innerHTML = cities.length ? renderTable(cities) : 'No cities found.';

            cityResults.innerHTML = renderTable(cities);

            if (totalPages > 1) renderPagination(totalPages, page);

        } catch (error) {
            cityResults.innerHTML = 'An error occurred while fetching cities.';
            console.error(error);
        } finally {
            showLoading(false);
            buttons.forEach(btn => (btn.disabled = false));
        }
    }

    /**
     * Generates HTML for the city table.
     * @param {Array} cities - Array of city objects.
     * @returns {string} - HTML.
     */
    function renderTable(cities) {
        let tableHTML = `
            <table>
                <thead>
                    <tr>
                        <th>City</th>
                        <th>Country</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Temperature</th>
                    </tr>
                </thead>
                <tbody>
        `;

        cities.forEach(city => {
            tableHTML += `
                <tr>
                    <td>${city.name}</td>
                    <td>${city.country}</td>
                    <td>${city.latitude || 'N/A'}</td>
                    <td>${city.longitude || 'N/A'}</td>
                    <td>${city.temperature || 'N/A'}</td>
                </tr>
            `;
        });

        tableHTML += `
                </tbody>
            </table>
        `;

        return tableHTML;
    }

    /**
     * Renders pagination buttons based on the total number of pages and the current page.
     * @param {number} totalPages - The total number of pages.
     * @param {number} currentPage - The current page number.
     */
    function renderPagination(totalPages, currentPage) {
        const existingPagination = document.querySelector('.pagination');
        existingPagination?.remove();

        let paginationHTML = '<div class="pagination">';

        for (let i = 1; i <= totalPages; i++) {
            paginationHTML += `
                <button class="pagination-btn ${i === currentPage ? 'active' : ''}" data-page="${i}">
                    ${i}
                </button>
            `;
        }

        paginationHTML += '</div>';
        cityResults.insertAdjacentHTML('beforeend', paginationHTML);
    }

    /**
     * Handles pagination button clicks and fetches the corresponding page.
     */
    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('pagination-btn')) {
            const page = parseInt(event.target.getAttribute('data-page'), 10);
            const query = searchInput.value;

            currentPage = page;
            fetchCities(query, page);
        }
    });

    /**
     * Handles input events on the search field with debounce to reduce unnecessary fetch requests.
     */
    searchInput.addEventListener('input', function () {
        const query = this.value.trim();

        clearTimeout(debounceTimeout);

        debounceTimeout = setTimeout(function () {
            currentPage = 1; // Сброс на первую страницу
            fetchCities(query, currentPage);
        }, 500);
    });

    // Initial city data fetch
    fetchCities();
});
