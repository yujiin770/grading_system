</main> <!-- end .main-content -->
    </div> <!-- end .page-wrapper -->

    <!-- JAVASCRIPT FOR ALL SITE FEATURES -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- 1. SCRIPT FOR MOBILE SIDEBAR TOGGLE ---
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const pageWrapper = document.querySelector('.page-wrapper');

            if (sidebar && sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('open');
                    pageWrapper.classList.toggle('sidebar-open');
                });
            }

            // --- 2. SCRIPT FOR DYNAMIC YEAR SELECTOR (view_subject.php) ---
            const startYearSelect = document.getElementById('start_year');
            const endYearDisplay = document.getElementById('end_year');

            if (startYearSelect && endYearDisplay) {
                startYearSelect.addEventListener('change', function() {
                    const startYear = parseInt(this.value);
                    const endYear = startYear + 1;
                    endYearDisplay.textContent = endYear;
                });
            }

            // --- 3. SCRIPT FOR CASCADING/DEPENDENT DROPDOWNS (students.php) ---
            const sySelect = document.getElementById('sy-select');
            const semSelect = document.getElementById('sem-select');
            const sectionSelect = document.getElementById('section-select');

            if (sySelect && semSelect && sectionSelect) {
                const allSectionOptions = Array.from(sectionSelect.options);

                function filterSections() {
                    const selectedSY = sySelect.value;
                    const selectedSem = semSelect.value;
                    const currentSectionValue = sectionSelect.value;

                    sectionSelect.innerHTML = '';
                    sectionSelect.appendChild(allSectionOptions[0]); // Add back "All Sections"

                    allSectionOptions.forEach(option => {
                        if (option.value === '') return;
                        const text = option.textContent;
                        
                        const matchesSY = !selectedSY || text.startsWith(selectedSY);
                        const matchesSem = !selectedSem || text.includes(selectedSem);

                        if (matchesSY && matchesSem) {
                            sectionSelect.appendChild(option.cloneNode(true));
                        }
                    });

                    sectionSelect.value = currentSectionValue;
                }

                sySelect.addEventListener('change', filterSections);
                semSelect.addEventListener('change', filterSections);
                filterSections(); // Run on page load
            }

            // --- 4. SCRIPT FOR 'SELECT ALL' BUTTON (view_section.php) ---
            const selectAllBtn = document.getElementById('select-all-btn');
            const studentListSelect = document.getElementById('student-list-select');

            if (selectAllBtn && studentListSelect) {
                selectAllBtn.addEventListener('click', function() {
                    for (let i = 0; i < studentListSelect.options.length; i++) {
                        const option = studentListSelect.options[i];
                        if (!option.disabled) {
                            option.selected = true;
                        }
                    }
                });
            }

        });
    </script>
</body>
</html>