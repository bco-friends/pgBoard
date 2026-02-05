#!/bin/sh

# Install gettext for envsubst if not present
if ! command -v envsubst >/dev/null 2>&1; then
    echo "Installing envsubst..."
    apk add --no-cache gettext
fi

# Generate sphinx.conf from template using environment variables
echo "Generating Sphinx configuration from template..."
envsubst < /opt/sphinx/conf/sphinx.conf.template > /opt/sphinx/conf/sphinx.conf

# Debug: show generated config
echo "Generated configuration:"
cat /opt/sphinx/conf/sphinx.conf | head -20

# Wait for database to be ready
echo "Waiting for database to be ready..."
sleep 10

# Create index directory if it doesn't exist
mkdir -p /opt/sphinx/index

# Build indexes
echo "Building Sphinx indexes..."
indexer --config /opt/sphinx/conf/sphinx.conf --all

# Start searchd
echo "Starting Sphinx daemon..."
searchd --config /opt/sphinx/conf/sphinx.conf --nodetach
