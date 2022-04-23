# Phapi

A lightweight PHP-based Rest API Framework

Written for PHP 7.4 and 8.0^

# How To Use

## Adding Phapi to your project

- Install

  ```
  composer require jacob-roth/phapi
  ```

- index.php

  ```php
  $Routes = new Phapi\Routes();
  $Routes->Add(new Phapi\Route(
    "Default",
    "/api/v1/{controller}/{action}"
  ));

  $Startup = new Phapi\Startup($Routes);
  $Startup->Run();
  ```

- .htaccess

  ```apache
  <IfModule mod_rewrite.c>
    Options +FollowSymLinks
    RewriteEngine On
    RewriteRule !index.php index.php
  </IfModule>
  ```

## Adding API Endpoint

- Create a new PHP class ending with "Controller" that extends Phapi\Controller (or use an existing Controller class)

  ```php
  class DefaultController extends \Phapi\Controller
  ```

- Add a `public` method to act as the new endpoint

  - This method can be called anything you want, or it can be named as an HTTP request method (this will allow you to call the endpoint without having to specify the method in the url)

  - Inside this method, you'll want to specify the HTTP request method (Get, Put, etc.). Although not strictly necessary, it is good practice.

    ```php
    // This endpoint can be called like
    // PUT /Default/IdPut/2
    public function IdPut(int $id)
    {
      $this->HttpPut();
      return [
        "id" => $id
      ];
    }

    // This endpoint can be called like
    // GET /Default/2
    public function GET(int $id)
    {
      $this->HttpGet();
      return "something";
    }
    ```

- Add a Route in index.php that fits your new endpoint (note that a previously existing Route may already work, so adding a new Route may not be necessary)

  - A Route maps a request to an endpoint

  - When adding a Route, you can specify variables in the url like `{id}`. Two of these variables cannot be used: `controller` and `action` which specify the Controller class and method to use. Route variables may be made optional by pre-pending them with a `"?"`, like `{?id}`

  - You can specify which HTTP request methods are allowed in the route

  - In mapping the route to an endpoint, you can specify the namespace, class, and even the method

  - This is an overview of how to use Routes. For more details, see the `Class Specifications > Route` section

## Class Specifications

- Controller

  - Specify which HTTP request method is allowed. Use only one for each endpoint. Here are some examples:

    ```php
    $this->HttpGet();
    $this->HttpPut();
    $this->HttpPost();
    $this->HttpDelete();
    ```

  - Change which HTTP response code is returned

    ```php
    $this->SetResponseCode(HttpCode::Created);
    ```

- Route

  - You will only ever need to initialize Route objects in your index.php. Because this object can be complex, I'm going to go into the construct parameters one at a time

  - The construct method is as follows:

    ```php
    public function __construct(
      string $Name,
      string $Path,
      ?array $HttpMethods = null,
      string $Namespace = "",
      ?string $DefaultController = null,
      ?string $DefaultAction = null
    )
    ```

    - `Name` is not used except by the user to keep track of how each Route is used. You can enter any string value here.

    - `Path` is a pattern used to match the path in a request url.

      - For example, if your path was `/v1/{controller}/{action}` then each of the following would be true

        - `/v1/default/getValues` would map to the method `DefaultController->getValues()`.

        - `/v1/user/login` would map to `UserController->login()`

      - And each of these would not map and instead throw a 404

        - `/user/login`

        - `/default/getValues`

        - `/v1/user/login/somethingElse`

      - An important feature of the Path is variables. Path variables are used to pass data from the request url to the method call.

        - Specifically, the string used to define the path variable needs to be the same string used as the argument of the endpoint method

        - For example, given the method

          ```php
          public function IdGet(int $id)
          ```

          a path must include `{id}` in order to call the endpoint properly. Such a path can look like this: `/api/v1/{controller}/{action}/{id}`

        - As you might be able to tell, there are special path variables. Namely, `"controller"` and `"action"`. You may use these where and when you see fit for your application, however, naming a method argument `controller` will result in incorrect dat getting passed to your endpoint. I strongly discourage such a practice in a production environment

        - You can also make path variables optional. Simply add a `"?"` in the path variable string, like `{?id}` and ensure that the method allows for a `null` value to be passed to that argument. For example:

          ```php
          public function IdGet(?int $id)
          ```

          using optional variables allow you to perform a mapping where `/v1/default/idGet/123` and `/v1/default/idGet/` both map to `DefaultController->IdGet($id)` where the latter simply passes `null` as `$id`

    - `HttpMethods` allows you to limit the Http request methods used in the Route.

      - If you want to limit the Route `/v1/{controller}/{action}` to only GET and POST requests, you'd use

        ```php
        [HttpMethod::Get, HttpMethod::Post]
        ```

      - The default value, `null`, behaves the same as

        ```php
        HttpMethod::All
        ```

    - `Namespace` allows you to map requests to Controllers that are in the given namespace. If your Controller classes are not in a namespace, this is to be left blank

      - For example, given the following Route

        ```php
        $Routes->Add(new Route(
          "Default",
          "/api/v1/{controller}/{action}",
          HttpMethod::All,
          "V1"
        ));
        ```

        an input of `/v1/user/login` would map to `V1\UserController->login()`

    - `DefaultController` and `DefaultAction` are useful when the Controller class and endpoint method are optional or otherwise not specified by the request url.

      - For example, the following Route

        ```php
        $Routes->Add(new Route(
          "Calling Methods with Special URLs",
          "/{id}",
          null,
          "V2",
          "Special",
          "DoingSomethingSpecial"
        ));
        ```

        will map to `V2\SpecialController->DoingSomethingSpecial($id)`

- ApiException

  - Can be thrown where you encounter an error and Phapi will handle the exception

  - You can specify an Http response code and a message string if you want to return something to the caller

  - Example:

    ```php
    throw new ApiException(HttpCode::BadRequest, "Invalid Input Data");
    ```

- HttpMethod

  - Collections of constants representing HTTP Request Methods

    ```php
    HttpMethod::Get
    HttpMethod::Head
    HttpMethod::Post
    HttpMethod::Put
    HttpMethod::Delete
    HttpMethod::Connect
    HttpMethod::Options
    HttpMethod::Trace
    HttpMethod::Patch
    HttpMethod::All
    ```

- HttpCode

  - Collection of constants representing HTTP Response Codes

  - I won't list them all here because there are a lot, but here are some examples:

    ```php
    HttpCode::Ok          // 200
    HttpCode::Created     // 201
    HttpCode::NotFound    // 404
    ```

## Passing Data to Endpoints

There are two ways to pass data from a request to an endpoint: by request data, and request url

- Data Passed in Request Data

  - If you want to pass request data to an endpoint, simply add an argument with any name and any PHP object type. This could be `"object"` or some custom object.

  - Example:

    ```php
    public function EchoId(RequestObject $req)
    {
      $this->HttpPost();
      return [
        "id" => $req->id
      ];
    }
    ```

- Data Passed in Request Url

  - Data passed in the url is handled by path variables. For details on these, see `Class Specifications > Route > Path`

## Version Limitations With Intent to Upgrade

- Currently Input and Output data can only be JSON. In a future version I intend to add a way to override the I/O format based on the "Content-Type" and "Accept" HTTP headers

- Speaking of HTTP headers, there's a lot of room for enhancements here. Currently, Phapi uses exactly zero of these headers when handling the data. Headers like Content-type, Accept, Accept-Encoding, Authorization, Cookie, and all things CORS could have a use in Phapi. There are not plans to add anything except Content-type and Accept right now, but the consideration is there
