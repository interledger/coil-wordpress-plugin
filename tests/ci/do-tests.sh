# Runs the Cypress tests locally using docker containers.
# Must be run from within the test/ci folder
set -e

# Stops all containers and removes any that might still be hanging around from previous attempts
docker-compose stop
docker-compose rm -f

# Deletes the contents of the wordpress folder to make sure we start from a clean slate
rm -rf wordpress
mkdir wordpress
chmod 777 wordpress

# Start the database
docker-compose up -d db

# Wait some seconds because the database can take a moment
sleep 5

# Start the wordpress site
docker-compose up -d wordpress

# Wait a moment for wordpress to be initialised
sleep 10

# An important thing to note here is that the website is installed to http://wordpress and not http://localhost, so if
# you are troubleshooting and want to hit the site with your browser you will need to create a host entry on your machine 127.0.0.1 -> wordpress
# Use the wordpress-cli container to initialise a new site
docker-compose run wordpress-cli wp core install --url="http://wordpress" --title=wordpress --admin_user=admin --admin_password=password --admin_email=admin@example.com --skip-email

# Install the wordpress-importer plugin
docker-compose run wordpress-cli wp plugin install wordpress-importer --activate

# Copy the coil plugin into the wordpress content folder
sudo rsync -av --exclude='coil-wordpress-plugin/tests/ci' ../../../coil-wordpress-plugin wordpress/wp-content/plugins/

# Installing the coil-wordpress-plugin
docker-compose run wordpress-cli wp plugin activate coil-wordpress-plugin

docker-compose build tester
# Fire up Cypress tests
docker-compose run tester --project /var/www/html/wp-content/plugins/coil-wordpress-plugin/tests --config baseUrl="http://wordpress:80/"
