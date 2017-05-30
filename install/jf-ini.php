<?php
class Ini {
    
    /**
     * @var Singleton reference to singleton instance
     */
    private static $instance;

    /**
     * gets the instance via lazy initialization (created on first usage).
     *
     * @return self
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * is not allowed to call from outside: private!
     */
    private function __construct()
    {
    
    }

    public static function get()
    {

        /*if (null === static::$instance) {
            static::$instance = new static();
        }*/
        //static::$instance;

        $arg_num = func_num_args();

        $params = func_get_args();

        // Explode the key path
        $parts = explode('.', $params[0]);

        // Count the number of items in the key path
        $length = count($parts);

        // Check the minimum number of arguments is specified
        if($length < 3){
            throw new Exception(
                "ini expects first paramater to have at least 3 components: [filepath].[ini_group].[ini_key]" 
            );
        }

        // Get the key we're looking for
        $key = $parts[ $length - 1 ];

        // Get the group we're looking for
        $group = $parts[ $length - 2 ];

        // Set the folder to search inside
        $path = "/etc/jellyfish";

        // cycle through the parts of the file path
        for($i = 0; $i < $length - 2; ++$i)
        {
            // convert the each part to a string
            $piece = $parts[$i];

            // append the string to the file path
            $path = $path . "/" . $piece;
        }

        // files must always end in ".ini"
        $path = $path . ".ini";

        $file_exists = (bool) file_exists($path);
        $is_file = (bool) is_file($path);
        $is_readable = (bool) is_readable($path);

        // If the path does not exist, throw an error
        if(!$file_exists)
        {
            throw new Exception(
                "File \"" . $path . "\" does not exist"
            );
        }

        // if the file is not a file (a folder), throw an error
        if(!$is_file)
        {
            throw new Exception(
                "File \"" . $path . "\" is not a file"
            );
        }

        // check the file is readable by the current user (usually apache)
        if(!$is_readable)
        {
            throw new Exception(
                "File \"" . $path . "\" is not readable. Check it has the correct permissions."
            );
        }

        // fetch the group contents as an array
        $ini_contents = parse_ini_file($path, true);  

        // Check if the key is in the group contents array
        $group_exists = array_key_exists($group, $ini_contents);

        // if the group does not exist, throw an error
        if(!$group_exists)
        {
            throw new Exception(
                "INI Group \"" . $group . "\" not found."
            );
        }

        // fetch the group contents as an array
        $group_contents = $ini_contents[$group];

        // Check if the key is in the group contents array
        $key_exists = (bool) array_key_exists($key, $group_contents);

        // If the key does not exist
        if(!$key_exists )
        {  
            // ... and if no default has been given
            if($arg_num < 2)
            {  
                // throw an exception to the user so that they can fix the issue
                throw new Exception(
                    "INI Key \"" . $key . "\" not found."
                );
            }
            // else a default has been given
            else
            {
                // ...so return the default
                return $params[1];
            }
        }

        $value = $group_contents[$key];

        // ...and return the value to the script
        return $value; 
    }

    /**
     * prevent the instance from being cloned.
     *
     * @throws SingletonPatternViolationException
     *
     * @return void
     */
    final public function __clone()
    {
        throw new SingletonPatternViolationException('This is a Singleton. __clone usage is forbidden');
    }

    /**
     * prevent from being unserialized.
     *
     * @throws SingletonPatternViolationException
     *
     * @return void
     */
    final public function __wakeup()
    {
        throw new SingletonPatternViolationException('This is a Singleton. __wakeup usage is forbidden');
    }

}