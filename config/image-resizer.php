<?php

	return [
		/**
		 * The php image driver to use
		 * this can be 'imagick' or 'gd'
		 */
		'driver' => 'gd',

		/**
		 * The cache driver to use as defined
		 * in config/cache of your app
		 */
		'cache' => 'file',

		/**
		 * The path where to store image in cache
		 */
		'cachePath' => 'cache/images',

		/**
		 * Image cache life time
		 * This is given in minutes
		 * Here the cache for images is
		 * one hour
		 */
		'lifetime' => 60,

		/**
		 * Format can specifiy to which format save the image
		 *              JPEG PNG GIF TIF BMP ICO PSD WebP
		 *   GD         ✔️  ✔️  ✔️   -   -   -   -  ✔️ *
		 *   Imagick    ✔️  ✔️  ✔️  ✔️  ✔️  ✔️ ✔️  ✔️ *
		 *   For WebP support GD driver must be used with PHP 5 >= 5.5.0 or PHP 7
		 *   in order to use imagewebp(). If Imagick is used, it must be compiled
		 *   with libwebp for WebP support.
		 *   resize => will resize the image (boolean)
		 *   fit => Combine cropping and resizing to format image in a smart way (boolean)
		 *   keepRatio => will keep image ratio wile resizing (boolean)
		 *   trim => boolean to trim the image using border color
		 *   inCanvas => to make sure image boundarie is respected
		 *   format => select tha wantd ouput form, yan can just convert images if you want
		 * example :
		 *  'small' => [
		 *      'height' => 500,
		 *      'width' => 250,
		 *      'inCanvas' => true,
		 *      'trim' => ['transparent', null, 10]
		 *  ],
		 *  'smallWebp' => [
		 *      'height' => 500,
		 *      'width' => 250,
		 *      'inCanvas' => true,
		 *      'format' => 'webp'
		 *  ],
		 *  'justconvertImage' => [
		 *      'format' => 'webp'
		 *  ]
		 */
		'templates' => [
			'small_letterbox' => [
				'resize' => true,
				'keepRatio' => true,
				'width' => 200,
				'height' => 60,
				'inCanvas' => true,
				'format' => 'webp'
			],
			'medium_letterbox' => [
				'resize' => true,
				'keepRatio' => true,
				'width' => 500,
				'height' => 150,
				'inCanvas' => true,
				'format' => 'webp'
			],
			'small_landscape' => [
				'resize' => true,
				'keepRatio' => true,
				'width' => 200,
				'height' => 120,
				'inCanvas' => true,
				'format' => 'webp'
			],
			'medium_landscape' => [
				'resize' => true,
				'keepRatio' => true,
				'width' => 500,
				'height' => 300,
				'inCanvas' => true,
				'format' => 'webp'
			],
			'large_landscape' => [
				'resize' => true,
				'keepRatio' => true,
				'width' => 800,
				'height' => 400,
				'inCanvas' => true,
				'format' => 'webp'
			],
			'smallWebp' => [
				'height' => 500,
				'width' => 250,
				'inCanvas' => true,
				'format' => 'webp'
			],
			'justconvertImage' => [
				'format' => 'webp'
			]

		]
	];
