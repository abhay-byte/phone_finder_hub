#!/bin/bash
set -e

echo "Prepare for Render Deployment..."

# 1. Ensure scripts are executable
chmod +x docker-entrypoint.sh

# 2. Add files to git
git add Dockerfile docker-entrypoint.sh render.yaml

# 3. Commit changes
echo "Committing Render configuration..."
git commit -m "Configure Docker and Render Blueprint with Persistent Disk" || echo "Nothing to commit"

# 4. Instructions
echo "--------------------------------------------------------"
echo "✅ Setup Complete!"
echo "--------------------------------------------------------"
echo "Next Steps to Deploy on Render:"
echo "1. Push this repository to GitHub/GitLab."
echo "2. Go to https://dashboard.render.com"
echo "3. Click 'New' -> 'Blueprint'."
echo "4. Connect your repository."
echo "5. Render will detect 'render.yaml' and prompt to create the service."
echo "6. NOTE: Persistent Disks are a paid feature on Render (Starter Plan)."
echo "--------------------------------------------------------"
