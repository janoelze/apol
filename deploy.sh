remote_addr="janoelze@aquila.uberspace.de"
remote_dir="/var/www/virtual/janoelze/endtime-instruments.org/apol"
timestamp=$(date +"%Y-%m-%d_%H-%M-%S")
build_id=$(date +%s | sha256sum | base64 | head -c 16)

if [ ! -f ./deploy.sh ]; then
    echo "This script must be called from the root directory of the project."
    exit 1
fi

echo "Deploying to $remote_addr in $remote_dir"

echo "Creating build directory"

if [ -d build ]; then
    rm -rf build
fi

mkdir build

echo "Build ID is $build_id"
echo $build_id > build/build

cp -r ./html/ build

echo "Creating remote directories"

ssh $remote_addr "mkdir -p $remote_dir"

rsync -vhra build/ $remote_addr:$remote_dir/ --include='html/**.gitignore' --exclude='html/.git' --filter=':- html/.gitignore'

ssh $remote_addr "mv $remote_dir/.htaccess-prod $remote_dir/.htaccess"

echo "Done! :) https://endtime-instruments.org/apol/"