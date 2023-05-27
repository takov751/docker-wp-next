#!/bin/bash

set -e

# root directory
cd "$(dirname "$0")/.."

# load environment variables
source .env

# create directory for certificates
mkdir -p certs

mkcert -install "${DOMAIN}"

# Move the generated certificate and key to the certs directory
mv "${DOMAIN}.pem" certs/
mv "${DOMAIN}-key.pem" certs/
