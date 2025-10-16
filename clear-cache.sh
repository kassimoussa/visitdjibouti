#!/bin/bash

echo "Clearing Laravel caches..."

# Clear compiled views
rm -rf storage/framework/views/*
echo "✓ Views cache cleared"

# Clear application cache
rm -rf storage/framework/cache/data/*
echo "✓ Application cache cleared"

# Clear bootstrap cache
rm -rf bootstrap/cache/*.php
echo "✓ Bootstrap cache cleared"

# Recreate necessary directories
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
echo "✓ Directories recreated"

echo ""
echo "All caches cleared successfully!"
echo "Please restart your Laragon server now."
