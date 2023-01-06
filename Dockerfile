FROM debian
WORKDIR /var/www/html
COPY . /var/www/html

#Installing php
RUN sudo apt update && sudo apt -y upgrade
RUN sudo apt install -y lsb-release ca-certificates apt-transport-https software-properties-common gnupg2
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/sury-php.list
RUN sudo apt install -y curl
RUN curl -fsSL  https://packages.sury.org/php/apt.gpg| sudo gpg --dearmor -o /etc/apt/trusted.gpg.d/sury-keyring.gpg
RUN sudo apt update
RUN sudo apt install -y --no-install-recommends php8.1 && sudo apt install -y php8.1-{cli,common,xml}
RUN sudo apt update && sudo apt -y upgrade

# To start data_collection_daemon run "nohup php /var/www/html/data_collection_daemon.php &""

#Installing apache2
RUN sudo apt install -y apache2
RUN sudo ufw allow 'Apache Full'

