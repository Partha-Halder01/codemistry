rm -rf ~/codemistry_backend
mkdir -p ~/codemistry_backend
unzip -q ~/backend.zip -d ~/codemistry_backend

# Fix directory structure if it extracted into a 'backend' subfolder
if [ -d "~/codemistry_backend/backend" ]; then
    mv ~/codemistry_backend/backend/* ~/codemistry_backend/
    mv ~/codemistry_backend/backend/.* ~/codemistry_backend/ 2>/dev/null
    rmdir ~/codemistry_backend/backend
fi

# Run composer install to get the vendor directory
cd ~/codemistry_backend
COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-dev

# Setup .env file
cp .env.example .env
php artisan key:generate

# Copy frontend to public_html
rm -rf ~/public_html/*
unzip -q ~/frontend-dist.zip -d ~/public_html
