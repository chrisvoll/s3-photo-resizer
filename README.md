**NOTE:** This is out of date. I plan to replace it with a python script that will include facial recognition to automatically crop photos.


On-the-fly image resizing and cropping from Amazon S3 with PHP.

# Description

Pull an image from from S3 and resize/crop it. This script depends on ImageMagick for the fastest possible resizing, and Amazon's S3 SDK. Files are cached locally and served using .htaccess or PHP.

Please keep in mind that this script is NOT the best way to do on-the-fly image resizing. See [here](http://www.binarymoon.co.uk/2010/11/timthumb-cdn-amazon-s3-good/) for other options, from someone who knows a lot more about these things than I do. If you're only using S3 to save on disk space, this script should fit your needs, but I wouldn't recommend using it for large sites with a lot of images and users (though there's nothing stopping you from putting Cloudfront in front of it for CDN benefits). Regardless, it's generally a bad idea to load images directly from S3 without a proxy in front, since it's prone to 500 errors and will make your life a living hell.

## Usage

Put the files in /resize/ in the root of your web directory. Create `assets/config.php` and enter your S3 details, and make sure that ImageMagick is installed and your server user can run `convert`. Create a /resize/cache directory that your server user can write to (or it will attempt to create one). Load images like this:

```
http://example.com/resize/640x480.-50/path/to/image/in/s3.jpg
```

Or, without mod_rewrite:

```
http://example.com/resize/resize.php?src=/path/to/image/in/s3.jpg&query=640x480.-50
```

The resulting image will have a width of 640px, height of 480px, and a crop offset of -50% from the center of the image.

This is still a work in progress and still kinda sucks. Use at your own risk.

## To-do

* Cleanup the code
* Fully implement size restrictions
* Image optimization
* Better garbage collection, delete empty folders

## License

Copyright (c) 2013 Chris Voll

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

## Credit

Credit goes to [timthumb](http://code.google.com/p/timthumb/) for the inspiration to make a lighter weight image resizing script that works well with Amazon S3, and to Adam Whitcroft for the excellent [Batch iconset](http://adamwhitcroft.com/batch/) that I used for the 404 image.
