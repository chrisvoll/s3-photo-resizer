S3 Photo Resizer

# Description

Pull an image from from S3 and resize/crop/cache it. This depends on ImageMagick for the fastest possible resizing, and Amazon's S3 SDK for PHP for S3 integration. Files are cached locally and served using .htaccess or PHP.

Please keep in mind that this script is NOT the best way to do this. See [here](http://www.binarymoon.co.uk/2010/11/timthumb-cdn-amazon-s3-good/) for details on what else you can do, from the creator of timthumb. This script is good for if you're only using S3 to save on storage space (though there's nothing stopping you from putting Cloudfront in front of this for CDN benefits).

# Usage

Create `assets/config.php` and enter your S3 details. Load images like this:

```
/resize/640x480.-50/path/to/image/in/s3.jpg
```

The resulting image will have a width of 640px, height of 480px, and a crop offset of -50% from the center of the image.

This is still a work in progress and still kinda sucks. Use at your own risk.

# License

I'll add this eventually.