 // This script checks if the user navigated to this page via the back button.
        (function () {
            window.onpageshow = function(event) {
                if (event.persisted) {
                    // If the page is loaded from the fast back/forward cache,
                    // it means the user clicked "Back". We clear the form and reload.
                    document.querySelector('form').reset();
                    window.location.reload();
                }
            };
        })();