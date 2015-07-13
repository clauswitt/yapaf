<?php
namespace yapaf;
/*
  Ben's Magic PHP routing class
  The static (class) methods in this class are used to find an appropriate controller/action to handle our request
 */
class Router {

    static function route(Request $request) {
        $url = explode('?', $request->getServer('REQUEST_URI'));


        $path = $url[0];
        while (substr($path, -1) == '/') {
            $path = mb_substr($path, 0, (mb_strlen($path) - 1));
        }
        $path_components = explode('/', $path);
        $pathMethodArray = explode('.', $path_components[count($path_components) - 1]);


        if (count($pathMethodArray) == 2) {
            $path_components[count($path_components) - 1] = $pathMethodArray[0];
            $path_components[] = $pathMethodArray[1];
        }
        //Loop through all the routes we defined in route.php, and try to find one that matches our request
        foreach ($GLOBALS['routes'] as $route => $controllerString) {
            $route_components = explode("/", $route);
            $routeMethodArray = explode('.', $route_components[count($route_components) - 1]);
            if (count($routeMethodArray) == 2) {
                $route_components[count($route_components) - 1] = $routeMethodArray[0];
                $route_components[] = $routeMethodArray[1];
            }

            $action = "index";
            $module = '';
            $i = 0;
            $objects = array();
            $goodRoute = true;
            $forceRoute = false;
            $path_components = array_pad($path_components, count($route_components), '');
            $parameters = array();

            //Handle routes that call a specific action
            $controller_action_array = explode(":", $controllerString);

            if (count($controller_action_array) == 2) {
                $controller = $controller_action_array[0];
                $action = $controller_action_array[1];
            }
            elseif (count($controller_action_array) == 3) {
                $module = $controller_action_array[0];
                $controller = $controller_action_array[1];
                $action = $controller_action_array[2];
            }
            elseif (count($controller_action_array) == 1) {
                $controller = $controller_action_array[0];
            }

            //Loop through each component of this route until we find a part that doesn't match, or we run out of url
            foreach ($route_components as $route_component) {

                //This part of the route is a named parameter
                if (substr($route_component, 0, 1) == ":") {
                    $parameters[substr($route_component, 1)] = $path_components[$i];
                    //This part of the route is an action for a controller
                }
                elseif ($route_component == "[action]") {
                    if ($path_components[$i] != "") {
                        $action = str_replace("-", "_", $path_components[$i]);
                    }
                }
                elseif ($route_component == "[controller]") {
                    if ($path_components[$i] != "") {
                        $controller = str_replace("-", "_", $path_components[$i]);
                    }
                }
                //The possibility to force a good route... By using the star all subsequent pathparts are automatically
                elseif ($route_component == "*") {
                    echo "Bad match: ".str_replace("-","_",$route_component)." != ".$path_components[$i]."<br />";
                    $forceRoute = true;
                }
                elseif ($route_component != $path_components[$i] && str_replace("-", "_", $route_component) != $path_components[$i]) {
                    echo "Bad match: ".str_replace("-","_",$route_component)." != ".$path_components[$i]."<br />";
                    $goodRoute = false;
                    break;
                }
                $i++;
            }


            //This route is a match for our request, let's get the controller working on it
            if ($forceRoute || ($goodRoute && ($i >= count($path_components) || $path_components[$i] == ""))) {
                $request->set('module', $module);
                $request->set('controller', $controller.'Controller');
                $request->set('action', $action.'Action');
                foreach ($parameters as $key => $value) {
                    $request->set($key, $value);
                }
                return $request;
            }
        }


        return $request;
    }

}

