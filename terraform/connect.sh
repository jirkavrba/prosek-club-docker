#!/bin/bash

set -euo pipefail

if [ ! -f ./hetzner-ssh-key ]; then
    echo "Missing key, eat tofu."
    exit 1
fi

ssh -4 -i ./hetzner-ssh-key -o "StrictHostKeyChecking no" root@$(tofu output -raw public_ip)
