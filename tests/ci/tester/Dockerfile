FROM cypress/included:10.3.1

# The tests make exec calls to wp-cli which needs to be installed in this container.
RUN apt update
RUN apt install -y php-cli less php-mysql default-mysql-client
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
RUN chmod +x wp-cli.phar
RUN mv wp-cli.phar /usr/local/bin/wp
