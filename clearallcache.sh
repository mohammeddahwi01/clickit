sudo php bin/magento cache:clean;
sudo php bin/magento cache:flush;
sudo rm -drf pub/static/*;
sudo rm -drf generated/*;
sudo rm -drf var/page_cache/*;
sudo rm -drf var/view_preprocessed/*;
sudo rm -drf var/cache/*;
sudo chmod -R 777 var/ pub/media/ pub/static/;
