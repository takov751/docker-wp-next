#!/bin/bash

set -e

# root directory
cd "$(dirname "$0")/.."

# load environment variables
source .env

# create directory for certificates
mkdir -p nginx/certs

mkcert -install "${CMS_DOMAIN}"
mkcert -install "${APP_DOMAIN}"

# Move the generated certificate and key to the certs directory
mv "${CMS_DOMAIN}.pem" nginx/certs/
mv "${CMS_DOMAIN}-key.pem" nginx/certs/
mv "${APP_DOMAIN}.pem" nginx/certs/
mv "${APP_DOMAIN}-key.pem" nginx/certs/
