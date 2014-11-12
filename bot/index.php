<?php
/**
 * Created by PhpStorm.
 * User: iann0036
 * Date: 11/11/2014
 * Time: 11:28 PM
 */

    // Set query
    $query = "hey there";

    $query = strtoupper($query);

    // Get AIML files
    $aiml_files = array();
    $all_files = scandir(__DIR__."/aiml/");
    foreach ($all_files as $file) {
        if (pathinfo($file,PATHINFO_EXTENSION)=="aiml") {
            $aiml_files[] = __DIR__."/aiml/".$file;
        }
    }

    // Sanity check
    if (count($aiml_files)<1)
        die('Can\'t see files!');

    // Process
    function process($query)
    {
        foreach ($GLOBALS['aiml_files'] as $file) {
            $xml = simplexml_load_file($file);
            foreach ($xml->category as $category) {
                $pattern = "/^";
                $pattern_parts = explode(" ", $category->pattern);
                for ($i = 0; $i < count($pattern_parts); $i++) {
                    if (preg_match('/^[a-zA-Z0-9]+$/i', $pattern_parts[$i])) {
                        if ($i > 0)
                            $pattern .= "\\s";
                        $pattern .= $pattern_parts[$i];
                    }
                    if ($pattern_parts[$i] == "*" || $pattern_parts[$i] == "_")
                        $pattern .= "\\s[A-Za-z0-9]+(?:\\s+[A-Za-z0-9]+)*";
                    if ($pattern_parts[$i] == "^" || $pattern_parts[$i] == "#")
                        $pattern .= "(?:\\s+[A-Za-z0-9]+)*";

                }
                $pattern.= "$/i";
                if (preg_match($pattern, $query)) {
                    if (isset($category->template->srai))
                        return process($category->template->srai);
                    return $category->template;
                }
            }
        }
    }

    echo process($query);