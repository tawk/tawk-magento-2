module.exports = {
	'*.js': 'eslint -c ./vendor/magento/magento-coding-standard/eslint/.eslintrc --rulesdir ./vendor/magento/magento-coding-standard/eslint/rules --cache --fix',
	'*.php': [
		'composer run lint:fix',
		'composer run lint'
	]
}
