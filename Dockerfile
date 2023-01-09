FROM debian
WORKDIR /var/www/html
COPY ./app/ /var/www/html
RUN apt-get update && apt-get -y install sudo curl

#Installing php
RUN sudo apt-get update && sudo apt-get -y upgrade
RUN sudo apt-get -y install lsb-release ca-certificates apt-transport-https software-properties-common gnupg2
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/sury-php.list
RUN curl -fsSL  https://packages.sury.org/php/apt.gpg| sudo gpg --dearmor -o /etc/apt/trusted.gpg.d/sury-keyring.gpg
RUN sudo apt-get update
RUN sudo apt-get -y install --no-install-recommends php8.1 
RUN sudo apt-get update
RUN sudo apt-get -y install php8.1-cli php8.1-common php8.1-xml
RUN sudo apt-get update && sudo apt-get -y upgrade

# To start data_collection_daemon run "nohup php /var/www/html/data_collection_daemon.php &""

#Installing apache2
RUN sudo apt install -y apache2

