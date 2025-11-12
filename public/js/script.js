// Sidebar toggle for mobile view
  
  document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');

            if (sidebar && sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('open');
                });
            }
        });

  // JAVASCRIPT FOR AUTOMATIC SEARCH AND FILTERS 

  document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const sectionSelect = document.getElementById('section-select');
        const filterForm = document.getElementById('filter-form');
        let searchTimeout;

        function submitForm() {
            filterForm.submit();
        }

        searchInput.addEventListener('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(submitForm, 500); // Wait 500ms after user stops typing
        });

        sectionSelect.addEventListener('change', submitForm);
    });