S3 Photo Resizer

# Description

Pull an image from from S3 and resize/crop/cache it. This depends on ImageMagick for the fastest possible resizing, and Amazon's S3 SDK for PHP for S3 integration. Files are cached locally and served using .htaccess or PHP.

Please keep in mind that this script is NOT the best way to do this. See [here](http://www.binarymoon.co.uk/2010/11/timthumb-cdn-amazon-s3-good/) for details on what else you can do, from the creator of timthumb. This script is good for if you're only using S3 to save on storage space (though there's nothing stopping you from putting Cloudfront in front of this for CDN benefits).

# Usage

Put these files in /resize/ in the root of your web directory. Create `assets/config.php` and enter your S3 details. Load images like this:

```
http://example.com/resize/640x480.-50/path/to/image/in/s3.jpg
```

The resulting image will have a width of 640px, height of 480px, and a crop offset of -50% from the center of the image.

This is still a work in progress and still kinda sucks. Use at your own risk.

# License

Copyright (c) 2013 Chris Voll

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

# Credit

Credit goes to [timthumb](http://code.google.com/p/timthumb/) for the inspiration to make a lighter weight image resizing script that works well with Amazon S3, and to Adam Whitcroft for the excellent [Batch iconset](http://adamwhitcroft.com/batch/) that I used for the 404 image.