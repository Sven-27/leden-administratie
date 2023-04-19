// Wait for the document to load before executing this
document.addEventListener("DOMContentLoaded", function() {

    // Select all elements that have data attribute show-row
    document.querySelectorAll('[show-row]').forEach(el => {
        // For each element found we add a click listener
        el.addEventListener('click', function() {
            // Retrieve from the element the data attribute show-row to see which family ID should be shown
            const family = this.getAttribute('show-row');

            // Retrieve all elements that have data attribute family with the value of the family ID we retrieved
            document.querySelectorAll(`tr[family="${family}"]`).forEach(el => {
                // For each element look what the data attribute hidden is and based on that we set that value to opposite value
                (el.getAttribute('hidden') === 'true') ? el.setAttribute('hidden', 'false') : el.setAttribute('hidden', 'true');
            });
        });
    });

     // Select all elements that have data attribute show-years
    document.querySelectorAll('[show-years]').forEach(el => {
        // For each element found we add a click listener
        el.addEventListener('click', function() {
            // Retrieve from the element the data attribute show-years to see which member ID should be shown
            const member = this.getAttribute('show-years');

            // Retrieve all elements that have data attribute family-member with the value of the member ID we retrieved
            document.querySelectorAll(`tr[family-member="${member}"]`).forEach(el => {
                // For each element look what the data attribute hidden is and based on that we set that value to opposite value
                (el.getAttribute('hidden') === 'true') ? el.setAttribute('hidden', 'false') : el.setAttribute('hidden', 'true');
            });
        });
    });
});