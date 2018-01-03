## Documentation

- Installation guide: https://docs.mageplaza.com/kb/installation.html
- User Guide: https://docs.mageplaza.com/layered-navigation-m2/
- Product page: https://www.mageplaza.com/magento-2-layered-navigation-extension/
- Get Support: https://mageplaza.freshdesk.com/ or support@mageplaza.com
- Changelog: https://www.mageplaza.com/changelog/m2-layered-navigation.txt
- License agreement: https://www.mageplaza.com/LICENSE.txt



## How to install

### Method 1: Install ready-to-paste package (Recommended)

- Download the latest version at https://store.mageplaza.com/my-downloadable-products.html
- Installation guide: https://docs.mageplaza.com/kb/installation.html



### Method 2: Manually install via composer

1. Access to your server via SSH
2. Create a folder (Not Magento root directory) in called: `mageplaza`, 
3. Download the zip package at https://store.mageplaza.com/my-downloadable-products.html
4. Upload the zip package to `mageplaza` folder.


3. Add the following snippet to `composer.json`

```
	{
		"repositories": [
		 {
		 "type": "artifact",
		 "url": "mageplaza/"
		 }
		]
	}
```

4. Run composer command line

```
composer require mageplaza/layered-navigation-m2
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## FAQs


#### Q: I got error: `Mageplaza_Core has been already defined`
A: Read solution: https://github.com/mageplaza/module-core/issues/3


#### Q: My site is down
A: Please follow this guide: https://www.mageplaza.com/blog/magento-site-down.html
