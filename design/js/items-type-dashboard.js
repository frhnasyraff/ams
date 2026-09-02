$(document).ready(function () {
    
    const $recordsPerPage = $("#recordsPerPage");
    const $searchInput = $("#searchInput");
    const $itemCardContainer = $("#item-card-container");
    const $cards = $(".item-card");
    const $pagination = $("#pagination");
    const $previousPage = $("#previousPage");
    const $nextPage = $("#nextPage");
    let itemsPerPage = parseInt($recordsPerPage.val());
    let currentPage = 1;
    let totalPages = 0;
    let filteredCards = $cards; // Initialize with all cards

    function updatePaginationButtons() {
        $pagination.find("#previousPage").toggleClass("disabled", currentPage === 1);
        $pagination.find("#nextPage").toggleClass("disabled", currentPage === totalPages || totalPages === 0);
    }

    function getVisiblePages() {
        if (totalPages <= 7) {
            return Array.from({ length: totalPages }, (_, index) => index + 1);
        }

        let startPage = Math.max(2, currentPage - 2);
        let endPage = Math.min(totalPages - 1, currentPage + 2);

        if (currentPage <= 4) endPage = 5;
        if (currentPage >= totalPages - 3) startPage = totalPages - 4;

        const pages = [1];
        if (startPage > 2) pages.push('ellipsis');
        for (let page = startPage; page <= endPage; page++) pages.push(page);
        if (endPage < totalPages - 1) pages.push('ellipsis');
        pages.push(totalPages);

        return pages;
    }

    function updatePagination(totalItems, itemsPerPage) {
        totalPages = Math.ceil(totalItems / itemsPerPage);
        const pageLinks = [];

        if (totalPages > 1) {
            // Add previous button
            pageLinks.push($previousPage[0].outerHTML);

            // Add a compact page window with boundary pages and ellipses.
            getVisiblePages().forEach(function (page) {
                if (page === 'ellipsis') {
                    pageLinks.push('<li class="page-item disabled pagination-ellipsis"><span class="page-link">&hellip;</span></li>');
                    return;
                }
                pageLinks.push(
                    `<li class="page-item ${page === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${page}">${page}</a>
                    </li>`
                );
            });

            // Add next button
            pageLinks.push($nextPage[0].outerHTML);
        }

        $pagination.html(pageLinks.join(''));
        updatePaginationButtons();
    }

    function showPage(page, currentItemsPerPage, cardsToShow) {
        $itemCardContainer.empty();
        const start = (page - 1) * currentItemsPerPage;
        const end = start + currentItemsPerPage;
        cardsToShow.slice(start, end).appendTo($itemCardContainer);
    }

    function filterAndPaginate() {
        const searchTerm = $searchInput.val().toLowerCase();
        filteredCards = $cards.filter(function () {
            return $(this).data("item-type").includes(searchTerm);
        });

        const currentItemsPerPage = itemsPerPage === 'all' ? filteredCards.length : parseInt(itemsPerPage);
        currentPage = 1;
        showPage(currentPage, currentItemsPerPage, filteredCards);
        updatePagination(filteredCards.length, currentItemsPerPage);
    }

    // Initial load
    setTimeout(function () {
        filterAndPaginate();
    }, 100);

    // Event listener for records per page change
    $recordsPerPage.on("change", function () {
        itemsPerPage = $(this).val();
        filterAndPaginate();
    });

    // Event listener for search input
    $searchInput.on("input", function () {
        filterAndPaginate();
    });

    // Event listener for pagination clicks (including numbered pages, next, and previous)
    $pagination.on("click", ".page-link", function (e) {
        e.preventDefault();
        const $this = $(this);
        const page = $this.data("page");

        if (page) {
            currentPage = page;
        } else if ($this.parent().is("#previousPage") && currentPage > 1) {
            currentPage--;
        } else if ($this.parent().is("#nextPage") && currentPage < totalPages) {
            currentPage++;
        }

        const currentItemsPerPage = itemsPerPage === 'all' ? filteredCards.length : parseInt(itemsPerPage);
        showPage(currentPage, currentItemsPerPage, filteredCards);
        updatePagination($cards.filter(function () {
            return $(this).data("item-type").includes($searchInput.val().toLowerCase());
        }).length, currentItemsPerPage);
    });


});
