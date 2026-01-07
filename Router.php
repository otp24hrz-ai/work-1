<?php

class Router {
    private $routes = [];
    private $notFoundHandler;
    private $prefix = '';
    
    /**
     * ตั้งค่า prefix สำหรับ routes
     */
    public function setPrefix($prefix) {
        $this->prefix = rtrim($prefix, '/');
        return $this;
    }
    
    /**
     * เพิ่ม GET route
     */
    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
        return $this;
    }
    
    /**
     * เพิ่ม POST route
     */
    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
        return $this;
    }
    
    /**
     * เพิ่ม PUT route
     */
    public function put($path, $handler) {
        $this->addRoute('PUT', $path, $handler);
        return $this;
    }
    
    /**
     * เพิ่ม DELETE route
     */
    public function delete($path, $handler) {
        $this->addRoute('DELETE', $path, $handler);
        return $this;
    }
    
    /**
     * เพิ่ม route ทุก method
     */
    public function any($path, $handler) {
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];
        foreach ($methods as $method) {
            $this->addRoute($method, $path, $handler);
        }
        return $this;
    }
    
    /**
     * จัดกลุ่ม routes ที่มี prefix เดียวกัน
     */
    public function group($prefix, $callback) {
        $previousPrefix = $this->prefix;
        $this->prefix = $previousPrefix . '/' . trim($prefix, '/');
        $callback($this);
        $this->prefix = $previousPrefix;
        return $this;
    }
    
    /**
     * เพิ่ม route เข้าระบบ
     */
    private function addRoute($method, $path, $handler) {
        $path = $this->prefix . '/' . trim($path, '/');
        $path = $path ?: '/';
        
        // แปลง path เป็น regex pattern
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }
    
    /**
     * ตั้งค่า handler สำหรับ 404
     */
    public function setNotFound($handler) {
        $this->notFoundHandler = $handler;
        return $this;
    }
    
    /**
     * รัน router และจับคู่ URL
     */
    public function run() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];
        
        // ตัด query string ออก
        if (false !== $pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        
        $requestUri = rawurldecode($requestUri);
        
        // หา route ที่ตรงกัน
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }
            
            if (preg_match($route['pattern'], $requestUri, $matches)) {
                // กรอง parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                return $this->callHandler($route['handler'], $params);
            }
        }
        
        // ไม่เจอ route ที่ตรงกัน
        return $this->callNotFoundHandler();
    }
    
    /**
     * เรียก handler
     */
    private function callHandler($handler, $params = []) {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }
        
        if (is_string($handler)) {
            // รองรับ format: 'ControllerName@methodName'
            if (strpos($handler, '@') !== false) {
                list($controller, $method) = explode('@', $handler);
                
                if (class_exists($controller)) {
                    $controllerInstance = new $controller();
                    if (method_exists($controllerInstance, $method)) {
                        return call_user_func_array([$controllerInstance, $method], $params);
                    }
                }
            }
            
            // รองรับ include file
            if (file_exists($handler)) {
                extract($params);
                return include $handler;
            }
        }
        
        if (is_array($handler) && count($handler) === 2) {
            list($controller, $method) = $handler;
            if (is_object($controller) || class_exists($controller)) {
                $controllerInstance = is_object($controller) ? $controller : new $controller();
                if (method_exists($controllerInstance, $method)) {
                    return call_user_func_array([$controllerInstance, $method], $params);
                }
            }
        }
        
        throw new Exception("Invalid route handler");
    }
    
    /**
     * เรียก 404 handler
     */
    private function callNotFoundHandler() {
        http_response_code(404);
        
        if ($this->notFoundHandler) {
            return $this->callHandler($this->notFoundHandler);
        }
        
        // แสดง 404 แบบ default
        echo "404 - Page Not Found";
    }
    
    /**
     * สร้าง URL จาก path
     */
    public static function url($path) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . '://' . $host . '/' . ltrim($path, '/');
    }
    
    /**
     * Redirect ไปยัง URL
     */
    public static function redirect($url, $statusCode = 302) {
        header('Location: ' . $url, true, $statusCode);
        exit;
    }
}
