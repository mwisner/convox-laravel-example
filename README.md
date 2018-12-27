# Convox Laravel Example
This project roughly outlines the various requirements and gotchas for running Laravel using [Convox](https://www.convox.com).

This application is setup to run the `app` service (running php-fpm process) as an [Internal](https://docs.convox.com/management/internal-services) service. There is also a separate nginx service that is then exposed to the world which proxies requests to the private app.

Note: If you're just running Laravel the chances are you can actually simplify this and expose the app process directly to the world. Initially this project was created for an application that had additional exposed services that were removed for the purposes of this example.

TODO: Actually delete the extra nginx reverse proxy.

### Application Load Balancers  
Convox uses application load balancers (ALB) to route traffic from the world to specific services. There are a couple of challenges that need to be addressed when running a php-fpm application behind an ALB.

**ALB uses hostnames to route traffic to the correct service**  
This means you CANNOT override the Host header in your nginx reverse proxy. Laravel also uses this header to build things such as full urls to public assets. So you have to specifically set the url you want Laravel uses by editing the `app/Providers/AppServiceProvider.php` file.

**Getting the real IP of the user**  
Because each request actually flows through multiple Albs and nginx services, the X-Forwarded-For header that laravel traditionally uses for ips while behind a reverse proxy actually ends up having multiple ips, and laravel does not magically find the correct one.

Using this setup if you want to find the user's real ip, you'll have to do something like:
```php
$ip = request()->header('X-Real-IP');
if (! $ip) {
    $ip = request()->ip();
}
```

**Services that expose a port MUST expose a URL that returns a 200 status**  

Any service behind an ALB must have some sort of health check URL. Because PHP-FPM is not a web server there are no urls that are accessible directly for health checks. In order for this to work we actually have to install and run nginx inside the application as well, and set up a special healthcheck url.
