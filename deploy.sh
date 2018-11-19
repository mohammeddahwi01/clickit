sudo php bin/magento cache:clean;
sudo php bin/magento setup:upgrade;
sudo php bin/magento setup:di:compile;
sudo php bin/magento setup:static-content:deploy -f en_US en_CA fr_FR;
sudo php bin/magento cache:clean;
sudo php bin/magento indexer:reindex;
sudo php bin/magento cache:clean;
sudo chmod -R 777 var/ pub/media/ pub/static/
