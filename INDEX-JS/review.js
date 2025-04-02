            // Form submission time recording
            function recordFormSubmitTime() {
                const currentDate = new Date();
                const isoString = currentDate.toISOString();
                const mysqlFormattedDateTime = isoString.replace('T', ' ').substring(0, 19);
                document.getElementById('form_submit_time').value = mysqlFormattedDateTime;
            }
        
            // Star rating functionality
            let selectedRating = 0;
        
            function setRating(rating) {
                selectedRating = rating;
                const stars = document.querySelectorAll('.stars i');
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.add('selected');
                    } else {
                        star.classList.remove('selected');
                    }
                });
            }
        
            // Ensure the rating value is set before form submission
            document.getElementById("ReviewSubmit").addEventListener("submit", function (event) { const ratingError = document.getElementById("ratingerror");
                if (!selectedRating) { // Check if no star rating is selected
                    event.preventDefault(); // Prevent form submission
                    ratingerror.style.display = "block"; // Show the error message
                } else {
                    ratingError.style.display = "none"; // Hide the error message if rating is selected
                    document.getElementById("rating").value = selectedRating;
                }
            });
        
            // Toggle contact fields (if used later)
            function toggleContactFields(value) {
                const contactFields = document.getElementById('contact-fields');
                contactFields.className = value === 'yes' ? '' : 'hidden';
            }