<?php
session_start();
/**
 * Luminance Framework
 * 
 * @version 1.0.0
 * @copyright 2017-2018
 * @license MIT
 * @author Michael <michaeldoestech@gmail.com>
 * @package Luminance
 */

require_once('vendor/autoload.php');

/**
 * Luminance Router
 */

$request_uri = $_SERVER['REQUEST_URI'];
$app_config = new \Luminance\Configuration\Loader("application");

if($request_uri === "/")
{
    if(isset($app_config->config["default_route"]))
    {
        $default_route = $app_config->config["default_route"];
        $splitter = explode("/", $default_route);
        $hier_prefix = "App\\Controllers\\$splitter[0]";
        if(class_exists($hier_prefix))
        {
            $controller = new $hier_prefix;
            if(method_exists($controller, $splitter[1]))
            {
                $part = $splitter[1];
                $controller->$part();
            }
            else
            {
                $errorPage = new Luminance\Controllers\Error;
                $errorPage->file_not_found();
                return;
            }
        }
        else if(file_exists("app/Controllers/$splitter[0].php"))
        {
            require_once("app/Controllers/$splitter[0].php");
            $controller = new $hier_prefix;
            if(method_exists($controller, $splitter[1]))
            {
                $part = $splitter[1];
                $controller->$part();
            }
            else
            {
                $errorPage = new Luminance\Controllers\Error;
                $errorPage->file_not_found();
                return;
            }
        }
        else
        {
            $errorPage = new Luminance\Controllers\Error;
            $errorPage->file_not_found();
            return;
        }
    }
    else
    {
        $errorPage = new Luminance\Controllers\Error;
        $errorPage->file_not_found();
        return;
    }
}
else if(isset($app_config->config["routes"][$request_uri]))
{
    $index = $app_config->config["routes"][$request_uri];
    $controller = $index["controller"];
    $controller_arr = explode("/", $controller);
    $hier = "App\\Controllers\\".$controller_arr[0];
    if(class_exists($hier))
    {
        $controller = new $hier();
        if(method_exists($controller, $controller_arr[1]))
        {
            $part = $controller_arr[1];
            $controller->$part();
        }
        else
        {
            $errorPage = new Luminance\Controllers\Error;
            $errorPage->file_not_found();
            return;
        }
    }
    else if(file_exists("app/Controllers/$controller_arr[1].php"))
    {
        require_once("app/Controllers/$controller_arr[1].php");
        $controller = new $hier_prefix;
        if(method_exists($controller, $controller_arr[2]))
        {
            $part = $controller_arr[2];
            $controller->$part();
        }
        else
        {
            $errorPage = new Luminance\Controllers\Error;
            $errorPage->file_not_found();
            return;
        }
    }
    else
    {
        $errorPage = new Luminance\Controllers\Error;
        $errorPage->file_not_found();
        return;
    }
}
else
{
    $parts = explode("/", $request_uri);
    $hier_prefix = "App\\Controllers\\$parts[1]";
    if(class_exists($hier_prefix))
    {
        $controller = new $hier_prefix;
        if(method_exists($controller, $parts[2]))
        {
            $part = $parts[2];
            $controller->$part();
        }
        else
        {
            if(method_exists($controller, "default"))
            {
                $controller->default();
            }
            else
            {
                $errorPage = new Luminance\Controllers\Error;
                $errorPage->file_not_found();
            }
        }
    }
    else
    {
        $errorPage = new Luminance\Controllers\Error;
        $errorPage->file_not_found();
    }
}

