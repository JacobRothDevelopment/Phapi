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

- Create a new PHP class ending with "Controller" that extends Phapi\Controller (or use an existing one)

  ```php
  class DefaultController extends \Phapi\Controller
  ```

- Add a `public` method. This method will act as the endpoint

  - This method can be called anything you want, or it can be named after an HTTP method in all caps. This will allow you to call the endpoint without having to specify the method in the url (see `Add a Route` for more info on this)

  - Inside this method, you'll want to specify the HTTP request method (get, put, etc.). That way if an endpoint is being improperly called, the user will know

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

- Add a Route in index.php that fits your new endpoint (or use an existing one)

  - A route will match an request url and method to an endpoint.

  - When adding a route, you can specify variables in the url like `{id}`. Two of these variables cannot be used: `controller` and `action` which specify the class and method to use. These variables can also be made optional by prepending them with a `"?"`, like `{?id}`

  - You can specify which HTTP methods are allowed in the route

  - In mapping the route to an endpoint, you can specify the namespace, class, and even the method

  - This is an overview of how to use Routes. See more under `Class Specifications`

## Class Specifications

- Controller

  - Specify which HTTP Method is allowed. Use only one for each endpoint. Here are some examples:

    ```php
    $this->HttpGet();
    $this->HttpPut();
    $this->HttpPost();
    $this->HttpPatch();
    $this->HttpHead();
    $this->HttpDelete();
    $this->HttpConnect();
    $this->HttpOptions();
    $this->HttpTrace();
    ```

  - Change which HTTP Code is returned

    ```php
    $this->SetResponseCode(HttpCode::Created);
    ```

- Route

  - You will only ever need to initialize these objects in your index.php, but because this object can be complex, I'm going to go into the construct parameters one at a time

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

    - `Name` is not used except for the user to keep track of what each route is used for. You can enter any string value here.

    - `Path` is a pattern used to match the path in a url.

      - For example, if your path was `/v1/{controller}/{action}` then each of the following would be true

        - `/v1/default/getvalues` would map to the method `DefaultController->getvalues()`.

        - `/v1/user/login` would map to `UserController->login()`

      - And each of these would not map and instead throw a 404

        - `/user/login`

        - `/default/getvalues`

        - `/v1/user/login/somethingelse`

      - An important feature of the Path is variables. Path variables are used to pass data from the url to the function call.

        - Specifically, the string used to define the path variable need to be the same string used as the argument of the endpoint method

        - For example, given the method

          ```php
          public function IdGet(int $id)
          ```

          a path must include `{id}` in order to call the endpoint properly. Such a path can look like this: `/api/v1/{controller}/{action}/{id}`

        - As you might be able to tell, there are special variables in a path. Namely, `controller` and `action`. You may use these where and when you see fit for your application, however, naming a method argument `controller` will result in incorrect dat getting passed to your endpoint. I strongly discourage such a practice in a production environment

        - You can also make path variables optional. Simply add a `"?"` in the path variable string, like `{?id}` and ensure that the method allows for a `null` value to be passed to that argument, like

          ```php
          public function IdGet(?int $id)
          ```

          using optional variables allow you to perform a mapping where `/v1/default/idGet/123` and `/v1/default/idGet/` both map to `DefaultController->IdGet($id)` where the latter simply passes `null` as `$id`

    - `HttpMethods` allows you to limit the Http Method used in the Route.

      - Eg. if you want to limit the Route `/v1/{controller}/{action}` to only GET and POST, you'd use `[HttpMethod::Get, HttpMethod::Post]`

      - The default value, `null`, behaves the same as `HttpMethod::All`.

    - `Namespace` allows you to map inputs to Controllers only in a certain namespace. If your Controller classes are not in a namespace, this is to be left blank

      - For example, with the following route

        ```php
        $Routes->Add(new Route(
          "Default",
          "/api/v1/{controller}/{action}",
          HttpMethod::All,
          "V1"
        ));
        ```

        an input of `/v1/user/login` would route to `V1\UserController->login()`

    - `DefaultController` and `DefaultAction` are useful when the Controller class and endpoint method are optional or otherwise not specified by the url input.

      - For example, with the following route

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

- HttpMethod

  - Collections of constants for HTTP Request Codes

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

  - Collection of constants for HTTP

  - I won't list them all here because there are a lot, but here are some examples:

    ```php
    HttpCode::Ok          // 200
    HttpCode::Created     // 201
    HttpCode::NotFound    // 404
    ```

## Passing Data to Endpoints

There are two ways to pass data from a request to an endpoint: by request data, and request url

- Data Passed in Request Data

- Data Passed in Request Url
  - Data passed in the url is handled by path variables. For details on these, see `Class Specifications > Route > Path`
