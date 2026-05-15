


     </main> </div> <script>
        /**
         * Sends an API request to the admin backend (admin-api.php).
         * @param {string} action - The API action to perform (e.g., 'add_service', 'delete_coupon').
         * @param {FormData} formData - The data payload for the request.
         * @returns {Promise<object>} - A promise that resolves to the JSON response from the server.
         */
        async function apiRequest(action, formData) {
            // Automatically add the 'action' parameter to the FormData
            formData.append('action', action);

            try {
                const response = await fetch('admin-api.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    let errorText = `HTTP error! Status: ${response.status}`;
                    try { errorText = await response.text(); } catch (e) {}
                    throw new Error(errorText);
                }

                const data = await response.json();
                return data;

            } catch (error) {
                console.error('API Request Error:', error);
                return {
                    success: false,
                    message: `Network error or invalid server response: ${error.message}`
                };
            }
        }
    </script>