const searchInput = document.getElementById("eventSearch");
const categoryFilter = document.getElementById("categoryFilter");
const eventCards = document.querySelectorAll(".event-card");
const noResults = document.getElementById("noResults");

function filterEvents() {
    if (!searchInput || !categoryFilter) return;

    const searchText = searchInput.value.trim().toLowerCase();
    const selectedCategory = categoryFilter.value;
    let visibleCount = 0;

    eventCards.forEach((card) => {
        const title = card.dataset.title || "";
        const venue = card.dataset.venue || "";
        const category = card.dataset.category || "";

        const matchesSearch = title.includes(searchText) || venue.includes(searchText);
        const matchesCategory = selectedCategory === "all" || category === selectedCategory;
        const shouldShow = matchesSearch && matchesCategory;

        card.hidden = !shouldShow;
        if (shouldShow) visibleCount++;
    });

    if (noResults) noResults.hidden = visibleCount !== 0;
}

if (searchInput) searchInput.addEventListener("input", filterEvents);
if (categoryFilter) categoryFilter.addEventListener("change", filterEvents);
