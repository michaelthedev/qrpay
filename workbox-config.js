module.exports = {
	globDirectory: '.',
	globPatterns: [
		'**/*.{json,lock,php,md,hhi,xml,css,png,jpg,ico,gif,js,eot,svg,ttf,woff,woff2,html,TXT,sql}'
	],
	swDest: 'sw.js',
	ignoreURLParametersMatching: [
		/^utm_/,
		/^fbclid$/
	]
};