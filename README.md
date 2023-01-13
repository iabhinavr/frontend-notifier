This is a plugin for headless WordPress sites. It watches for content updates, including new post/page publishes, updates, etc. and stores the list of new/modified posts in a file within the plugin directory. Then it notifies the frontend (built using Next.js, Vue, etc) with this data, so that the frontend server can rebuild those posts/pages.

In short, it helps frontend systems to regenerate just the modified posts, without the need to regenerate the entire site. For instance, you can use this plugin with Next.js' on-demand revalidation feature. 

From the plugin's settings page, the site admin can initiate an API request to the site's frontend. Your WordPress site will then send an HTTP request to the frontend API, which contains the data in json format.

By the way, for this to work, the frontend should be properly configured to receive this data.

Also, you need to add two constants to the wp-config.php file:

```php
define( 'FN_SECRET_KEY', 'fghoregdb43573sd'); // change the value to your secret key
define( 'FN_URL', 'https://example.com/api/revalidate'); // change the url to your frontend API endpoint URL
```

The constant `FN_SECRET_KEY` is the secret string that WordPress should send with the request to authenticate itself with the frontend server. This prevents anonymous people from sending requests to your API.

The second constant - `FN_URL` - is the URL to which WordPress sends the request. Again, this depends on your frontend configuration.