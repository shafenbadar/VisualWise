document.addEventListener('DOMContentLoaded', function() {
    var currentScreen = document.querySelector('body').classList.contains('toplevel_page_visualwise-jules');
    if (!currentScreen) { // Only run on the Visualwise-Jules admin page
        return;
    }

    var tabs = document.querySelectorAll('.nav-tab-wrapper .nav-tab');
    var tabPanes = document.querySelectorAll('.tab-pane'); // Assuming admin page uses .tab-pane for content

    if (!tabs.length || !tabPanes.length) {
        return;
    }

    // Determine active tab from URL or default to 'general'
    var activeTabName = 'general';
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('tab')) {
        activeTabName = urlParams.get('tab');
    }

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function(event) {
            event.preventDefault();

            var currentTab = this.getAttribute('data-tab');

            // Update URL without full reload (optional for admin, but consistent with frontend)
            var url = new URL(window.location);
            url.searchParams.set('tab', currentTab);
            window.history.pushState({}, '', url);

            // Active tab class
            tabs.forEach(function(t) {
                t.classList.remove('nav-tab-active');
            });
            this.classList.add('nav-tab-active');

            // Show/hide tab panes
            tabPanes.forEach(function(pane) {
                // Assumes tab content divs have id like 'tab-content-general'
                if (pane.id === 'tab-content-' + currentTab) {
                    pane.style.display = 'block';
                    pane.classList.add('active');
                } else {
                    pane.style.display = 'none';
                    pane.classList.remove('active');
                }
            });
        });
    });

    // Ensure correct tab is shown on page load
    var initialTabFound = false;
    tabPanes.forEach(function(pane) {
        if (pane.id === 'tab-content-' + activeTabName) {
            pane.style.display = 'block';
            pane.classList.add('active');
            initialTabFound = true;
        } else {
            pane.style.display = 'none';
            pane.classList.remove('active');
        }
    });

    // Activate the corresponding tab link
     tabs.forEach(function(t) {
        if (t.getAttribute('data-tab') === activeTabName) {
            t.classList.add('nav-tab-active');
        } else {
            t.classList.remove('nav-tab-active');
        }
    });


    // If the activeTabName from URL didn't match any existing tab content, default to showing the first one
    if (!initialTabFound && tabPanes.length > 0) {
        tabPanes[0].style.display = 'block';
        tabPanes[0].classList.add('active');
        if(tabs.length > 0) {
            tabs[0].classList.add('nav-tab-active'); // Also activate the first tab link
            // Update URL to reflect this default state if it was an invalid tab
            var url = new URL(window.location);
            url.searchParams.set('tab', tabs[0].getAttribute('data-tab'));
            window.history.replaceState({}, '', url); // Use replaceState to not clutter history
        }
    }
});
