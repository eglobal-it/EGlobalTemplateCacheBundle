#!/bin/bash
set -e

TARGET_UID=$(stat -c "%u" /var/www)
TARGET_GID=$(stat -c "%g" /var/www)

if [ $TARGET_UID != 0 ] || [ $TARGET_GID != 0 ]; then
    echo '* Working around permission errors locally by making sure that "docker" uses the same uid and gid as the host volume'
fi

if [ $TARGET_UID != 0 ]; then
    echo '-- Setting docker user to use uid '$TARGET_UID
    usermod -o -u $TARGET_UID docker || true
fi

if [ $TARGET_GID != 0 ]; then
    echo '-- Setting docker group to use gid '$TARGET_GID
    groupmod -o -g $TARGET_GID docker || true
fi

# Update composer
composer self-update

exec "$@"
