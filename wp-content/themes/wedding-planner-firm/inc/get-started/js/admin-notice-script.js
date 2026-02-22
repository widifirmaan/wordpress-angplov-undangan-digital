
// Creta Testimonial Showcase plugin activation
document.addEventListener('DOMContentLoaded', function () {
    const wedding_planner_firm_button = document.getElementById('install-activate-button');

    if (!wedding_planner_firm_button) return;

    wedding_planner_firm_button.addEventListener('click', function (e) {
        e.preventDefault();

        const wedding_planner_firm_redirectUrl = wedding_planner_firm_button.getAttribute('data-redirect');

        // Step 1: Check if plugin is already active
        const wedding_planner_firm_checkData = new FormData();
        wedding_planner_firm_checkData.append('action', 'check_creta_testimonial_activation');

        fetch(installcretatestimonialData.ajaxurl, {
            method: 'POST',
            body: wedding_planner_firm_checkData,
        })
        .then(res => res.json())
        .then(res => {
            if (res.success && res.data.active) {
                // Plugin is already active → just redirect
                window.location.href = wedding_planner_firm_redirectUrl;
            } else {
                // Not active → proceed with install + activate
                wedding_planner_firm_button.textContent = 'Nevigate Getstart';

                const wedding_planner_firm_installData = new FormData();
                wedding_planner_firm_installData.append('action', 'install_and_activate_creta_testimonial_plugin');
                wedding_planner_firm_installData.append('_ajax_nonce', installcretatestimonialData.nonce);

                fetch(installcretatestimonialData.ajaxurl, {
                    method: 'POST',
                    body: wedding_planner_firm_installData,
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        window.location.href = wedding_planner_firm_redirectUrl;
                    } else {
                        alert('Activation error: ' + (res.data?.message || 'Unknown error'));
                        wedding_planner_firm_button.textContent = 'Try Again';
                    }
                })
                .catch(error => {
                    alert('Request failed: ' + error.message);
                    wedding_planner_firm_button.textContent = 'Try Again';
                });
            }
        })
        .catch(error => {
            alert('Check request failed: ' + error.message);
        });
    });
});
