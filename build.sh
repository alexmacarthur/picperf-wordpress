#!/bin/bash

echo "Building production version of plugin..."

rm -rf ../picperf-build
mkdir -p ../picperf-build/picperf
cp -a ./ ../picperf-build/picperf

FILES_TO_DELETE=(
    "tests"
    ".git"
    ".gitignore"
    "build.sh"
    "README.md"
    "composer.json"
    "composer.lock"
)

rm -rf ../picperf-build/picperf/vendor

for item in "${FILES_TO_DELETE[@]}"; do
    path_to_delete="../picperf-build/picperf/$item"
    echo "Deleting $path_to_delete"
    rm -rf $path_to_delete
done

cd ../picperf-build
echo "Zipping..."
zip -r "picperf.zip" ./picperf
open .
cd ../picperf-wordpress

echo "Done!"
