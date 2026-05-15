<?php
require_once 'admin-auth.php';
require_admin_login();

$edit_service = null;
$error = null;

try {
    // Check if we are editing a service
    if (isset($_GET['edit_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$_GET['edit_id']]);
        $edit_service = $stmt->fetch();
        if (!$edit_service) {
            $error = "Service to edit not found.";
        }
    }

    // Fetch all services to display in the list
    $services_list = $pdo->query("SELECT id, name, full_price, deposit_price, rating FROM services ORDER BY id ASC")->fetchAll(); // Added rating to query

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

require 'header.php';
?>

<h1 class="text-3xl font-bold text-white mb-6">Manage Services</h1>

<div id="status-message" class="hidden fixed top-20 right-8 z-50 p-4 rounded-lg shadow-lg text-white"></div>

<?php if ($error): ?>
    <div class="bg-red-800 border border-red-600 text-red-200 p-4 rounded-lg mb-6">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <div class="lg:col-span-1">
        <form id="service-form" class="bg-gray-800/50 border border-gray-700 p-6 rounded-2xl space-y-4" enctype="multipart/form-data">
            <h2 class="text-2xl font-bold text-white mb-4"><?php echo $edit_service ? 'Edit Service' : 'Add New Service'; ?></h2>

            <input type="hidden" name="service_id" value="<?php echo $edit_service['id'] ?? ''; ?>">
            <input type="hidden" name="action" value="<?php echo $edit_service ? 'edit_service' : 'add_service'; ?>">

            <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Service Name</label>
                <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($edit_service['name'] ?? ''); ?>" class="w-full bg-gray-700 text-white p-3 rounded-lg border border-gray-600">
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                <textarea name="description" id="description" rows="3" required class="w-full bg-gray-700 text-white p-3 rounded-lg border border-gray-600"><?php echo htmlspecialchars($edit_service['description'] ?? ''); ?></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="full_price" class="block text-sm font-medium text-gray-300 mb-2">Full Price (₹)</label>
                    <input type="number" name="full_price" id="full_price" required value="<?php echo htmlspecialchars($edit_service['full_price'] ?? ''); ?>" class="w-full bg-gray-700 text-white p-3 rounded-lg border border-gray-600">
                </div>
                <div>
                    <label for="deposit_price" class="block text-sm font-medium text-gray-300 mb-2">Deposit Price (₹)</label>
                    <input type="number" name="deposit_price" id="deposit_price" required value="<?php echo htmlspecialchars($edit_service['deposit_price'] ?? ''); ?>" class="w-full bg-gray-700 text-white p-3 rounded-lg border border-gray-600">
                </div>
            </div>

            <div>
                <label for="rating" class="block text-sm font-medium text-gray-300 mb-2">Rating (0.0 - 5.0)</label>
                <input type="number" name="rating" id="rating" value="<?php echo htmlspecialchars($edit_service['rating'] ?? '5.0'); ?>" min="0.0" max="5.0" step="0.1" required class="w-full bg-gray-700 text-white p-3 rounded-lg border border-gray-600">
            </div>

             <div>
                <label for="cover_image" class="block text-sm font-medium text-gray-300 mb-2">Cover Image (16:9 Recommended)</label>
                <input type="file" name="cover_image" id="cover_image" accept="image/jpeg, image/png, image/webp" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                <input type="hidden" name="existing_cover_image_path" value="<?php echo htmlspecialchars($edit_service['cover_image_path'] ?? ''); ?>">
                <?php if (!empty($edit_service['cover_image_path'])): ?>
                    <img src="../<?php echo htmlspecialchars($edit_service['cover_image_path']); ?>" alt="Current Cover Image" class="mt-3 rounded-lg max-h-32 border border-gray-600">
                    <p class="text-xs text-gray-400 mt-1">Current Image. Upload a new one to replace it.</p>
                <?php endif; ?>
            </div>

            <div>
                <label for="features" class="block text-sm font-medium text-gray-300 mb-2">Features (comma-separated)</label>
                <textarea name="features" id="features" rows="3" required class="w-full bg-gray-700 text-white p-3 rounded-lg border border-gray-600"><?php echo htmlspecialchars($edit_service['features'] ?? ''); ?></textarea>
            </div>
            
            <div>
                <h3 class="text-xl font-semibold text-white mb-3">FAQ Builder</h3>
                <div id="faq-container" class="space-y-4">
                    <?php
                    $faqs = [];
                    if (!empty($edit_service['faq'])) {
                        $decoded_faqs = json_decode($edit_service['faq'], true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded_faqs)) {
                            $faqs = $decoded_faqs;
                        }
                    }
                    
                    if (empty($faqs)) :
                        // Show one empty block by default if no FAQs
                    ?>
                        <div class="faq-item bg-gray-700/50 p-4 rounded-lg border border-gray-600 space-y-2">
                            <label class="block text-sm font-medium text-gray-300">Question 1</label>
                            <textarea name="faq_q[]" rows="2" class="w-full bg-gray-600 text-white p-2 rounded-lg border border-gray-500" placeholder="Enter the question"></textarea>
                            <label class="block text-sm font-medium text-gray-300">Answer 1</label>
                            <textarea name="faq_a[]" rows="3" class="w-full bg-gray-600 text-white p-2 rounded-lg border border-gray-500" placeholder="Enter the answer"></textarea>
                            <button type="button" class="faq-remove-btn bg-red-600 hover:bg-red-700 text-white text-xs font-medium py-1 px-3 rounded-lg">Remove</button>
                        </div>
                    <?php else :
                        foreach ($faqs as $index => $faq) :
                            $display_index = $index + 1;
                    ?>
                        <div class="faq-item bg-gray-700/50 p-4 rounded-lg border border-gray-600 space-y-2">
                            <label class="block text-sm font-medium text-gray-300">Question <?php echo $display_index; ?></label>
                            <textarea name="faq_q[]" rows="2" class="w-full bg-gray-600 text-white p-2 rounded-lg border border-gray-500" placeholder="Enter the question"><?php echo htmlspecialchars($faq['q'] ?? ''); ?></textarea>
                            <label class="block text-sm font-medium text-gray-300">Answer <?php echo $display_index; ?></label>
                            <textarea name="faq_a[]" rows="3" class="w-full bg-gray-600 text-white p-2 rounded-lg border border-gray-500" placeholder="Enter the answer"><?php echo htmlspecialchars($faq['a'] ?? ''); ?></textarea>
                            <button type="button" class="faq-remove-btn bg-red-600 hover:bg-red-700 text-white text-xs font-medium py-1 px-3 rounded-lg">Remove</button>
                        </div>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </div>
                <button type="button" id="add-faq-btn" class="mt-4 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg">Add Another FAQ</button>
            </div>


            <div class="flex gap-4 pt-2">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg"><?php echo $edit_service ? 'Save Changes' : 'Add Service'; ?></button>
                <?php if ($edit_service): ?>
                    <a href="services.php" class="w-full text-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 rounded-lg">Cancel Edit</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-gray-800/50 border border-gray-700 rounded-2xl overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-800 border-b border-gray-700">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-gray-300">ID</th>
                        <th class="p-4 text-sm font-semibold text-gray-300">Name</th>
                        <th class="p-4 text-sm font-semibold text-gray-300">Full Price</th>
                        <th class="p-4 text-sm font-semibold text-gray-300">Deposit</th>
                        <th class="p-4 text-sm font-semibold text-gray-300">Rating</th>
                        <th class="p-4 text-sm font-semibold text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" id="services-table-body">
                    <?php if (empty($services_list)): ?>
                        <tr><td colspan="6" class="p-4 text-center text-gray-400">No services found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($services_list as $service): ?>
                        <tr id="service-row-<?php echo $service['id']; ?>">
                            <td class="p-4 text-sm"><?php echo $service['id']; ?></td>
                            <td class="p-4 font-medium text-white"><?php echo htmlspecialchars($service['name']); ?></td>
                            <td class="p-4">₹<?php echo number_format($service['full_price']); ?></td>
                            <td class="p-4">₹<?php echo number_format($service['deposit_price']); ?></td>
                            <td class="p-4 text-yellow-400"><?php echo number_format($service['rating'], 1); ?> ★</td>
                            <td class="p-4 space-x-2">
                                <a href="services.php?edit_id=<?php echo $service['id']; ?>" class="bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium py-1 px-3 rounded-lg">Edit</a>
                                <button onclick="deleteService(<?php echo $service['id']; ?>)" class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-1 px-3 rounded-lg">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function showStatusMessage(message, isSuccess = true) {
        const statusMessage = document.getElementById('status-message');
        statusMessage.textContent = message;
        statusMessage.className = `fixed top-20 right-8 z-50 p-4 rounded-lg shadow-lg text-white ${isSuccess ? 'bg-green-600' : 'bg-red-600'}`;
        statusMessage.classList.remove('hidden');
        setTimeout(() => {
            statusMessage.classList.add('hidden');
        }, 3000);
    }

    document.getElementById('service-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form); // FormData handles multipart/form-data automatically
        const action = formData.get('action');

        // Get the submit button and disable it
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Saving...';


        try {
            const response = await apiRequest(action, formData); // Use the existing apiRequest function

            if (response.success) {
                showStatusMessage(response.message || 'Action successful!', true);
                // Reload the page to show changes
                setTimeout(() => {
                    window.location.href = 'services.php'; // Redirect back to clean services page
                }, 1000);
            } else {
                showStatusMessage(response.message || 'An error occurred.', false);
                // Re-enable button on failure
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
            }
        } catch (error) {
             showStatusMessage('A network or unexpected error occurred.', false);
             console.error("Form Submit Error:", error);
             // Re-enable button on unexpected error
             submitButton.disabled = false;
             submitButton.textContent = originalButtonText;
        }
    });

    async function deleteService(id) {
        if (!confirm('Are you sure you want to delete this service? This may affect existing orders.')) {
            return;
        }

        const formData = new FormData();
        formData.append('service_id', id);

        const response = await apiRequest('delete_service', formData);

        if (response.success) {
            showStatusMessage('Service deleted successfully!', true);
            const row = document.getElementById(`service-row-${id}`);
            if (row) {
                row.remove();
            }
        } else {
            showStatusMessage(response.message || 'Failed to delete service.', false);
        }
    }

    // --- FAQ Builder Logic ---
    const faqContainer = document.getElementById('faq-container');
    const addFaqBtn = document.getElementById('add-faq-btn');

    function updateFaqIndexes() {
        const items = faqContainer.querySelectorAll('.faq-item');
        items.forEach((item, index) => {
            const num = index + 1;
            item.querySelector('label:first-child').textContent = `Question ${num}`;
            item.querySelector('label:last-of-type').textContent = `Answer ${num}`;
            
            // Hide "Remove" button if it's the only one
            const removeBtn = item.querySelector('.faq-remove-btn');
            if (removeBtn) {
                 removeBtn.style.display = (items.length > 1) ? 'inline-block' : 'none';
            }
        });
    }

    function createFaqItem() {
        const newItem = document.createElement('div');
        newItem.className = 'faq-item bg-gray-700/50 p-4 rounded-lg border border-gray-600 space-y-2';
        
        const num = faqContainer.children.length + 1;
        
        newItem.innerHTML = `
            <label class="block text-sm font-medium text-gray-300">Question ${num}</label>
            <textarea name="faq_q[]" rows="2" class="w-full bg-gray-600 text-white p-2 rounded-lg border border-gray-500" placeholder="Enter the question"></textarea>
            <label class="block text-sm font-medium text-gray-300">Answer ${num}</label>
            <textarea name="faq_a[]" rows="3" class="w-full bg-gray-600 text-white p-2 rounded-lg border border-gray-500" placeholder="Enter the answer"></textarea>
            <button type="button" class="faq-remove-btn bg-red-600 hover:bg-red-700 text-white text-xs font-medium py-1 px-3 rounded-lg">Remove</button>
        `;
        
        newItem.querySelector('.faq-remove-btn').addEventListener('click', () => {
            newItem.remove();
            updateFaqIndexes();
        });
        
        return newItem;
    }

    addFaqBtn.addEventListener('click', () => {
        faqContainer.appendChild(createFaqItem());
        updateFaqIndexes();
    });

    // Add remove functionality to existing items
    faqContainer.querySelectorAll('.faq-remove-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.target.closest('.faq-item').remove();
            updateFaqIndexes();
        });
    });
    
    // Initial call to set button visibility
    updateFaqIndexes();
</script>

<?php require 'footer.php'; ?>