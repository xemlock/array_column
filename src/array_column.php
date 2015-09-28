<?php

if (!function_exists('array_column')) {
    /**
     * Return the values from a single column in the input array
     *
     * array_column() returns the values from a single column of the array, identified by
     * the column_key.
     *
     * Optionally, you may provide an index_key to index the values in the returned array
     * by the values from the index_key column in the input array.
     *
     * @since PHP 5.5.0
     * @param array $input      A multi-dimensional array (record set) from which to pull
     *                          a column of values.
     * @param mixed $column_key The column of values to return. This value may be the integer
     *                          key of the column you wish to retrieve, or it may be the
     *                          string key name for an associative array. It may also be NULL
     *                          to return complete arrays (useful together with index_key to
     *                          reindex the array).
     * @param mixed $index_key  (Optional) The column to use as the index/keys for the returned
     *                          array. This value may be the integer key of the column, or it
     *                          may be the string key name.
     * @return array An array of values representing a single column from the input array.
     */
    function array_column($input = null, $column_key = null, $index_key = null)
    {
        $num_args = func_num_args();

        if ($num_args > 3) {
            trigger_error(
                sprintf('array_column() expects at most 3 parameters, %d given', $num_args),
                E_USER_WARNING
            );
            return null;
        }

        if ($num_args < 2) {
            trigger_error(
                sprintf('array_column() expects at least 2 parameters, %d given', $num_args),
                E_USER_WARNING
            );
            return null;
        }

        if (!is_array($input)) {
            trigger_error(
                sprintf('array_column() expects parameter 1 to be array, %s given', gettype($input)),
                E_USER_WARNING
            );
            return null;
        }

        if (null !== $column_key
            && !is_int($column_key)
            && !is_float($column_key)
            && !is_string($column_key)
            && !(is_object($column_key) && method_exists($column_key, '__toString'))
        ) {
            trigger_error(
                'array_column(): The column key should be either a string or an integer',
                E_USER_WARNING
            );
            return false;
        }

        if (null !== $index_key
            && !is_int($index_key)
            && !is_float($index_key)
            && !is_string($index_key)
            && !(is_object($index_key) && method_exists($index_key, '__toString'))
        ) {
            trigger_error(
                'array_column(): The index key should be either a string or an integer',
                E_USER_WARNING
            );
            return false;
        }

        if ($column_key !== null) {
            if (is_int($column_key) || is_float($column_key)) {
                $column_key = (int) $column_key;
            } else {
                $column_key = (string) $column_key;
            }
        }

        if ($index_key !== null) {
            if (is_int($index_key) || is_float($index_key)) {
                $index_key = (int) $index_key;
            } else {
                $index_key = (string) $index_key;
            }
        }

        $output = array();

        foreach ($input as $input_value) {
            // $input_value must be an array, otherwise it will be ignored
            $is_valid_value = is_array($input_value) && (
                null === $column_key ||
                // null value in $input_value[$column_key] is perfectly valid
                // and cannot be ignored
                isset($input_value[$column_key]) ||
                array_key_exists($column_key, $input_value)
            );

            if ($is_valid_value) {
                if (null === $column_key) {
                    $output_value = $input_value;
                } else {
                    $output_value = $input_value[$column_key];
                }

                $output_key = null;

                // value used as an index_key in result array must be not null
                if (null !== $index_key && isset($input_value[$index_key])) {
                    // output key must be a string (or an object with __toString()),
                    // or an integer, but not float, as the latter will not be
                    // coerced to integer (HHVM coerces it, but I consider it as an
                    // incompatibility)
                    $output_key = $input_value[$index_key];

                    if (is_string($output_key)
                        || (is_object($output_key) && method_exists($output_key, '__toString'))
                    ) {
                        $output_key = (string) $output_key;
                    } elseif (!is_int($output_key)) {
                        $output_key = null;
                    }
                }

                if ($output_key === null) {
                    $output[] = $output_value;
                } else {
                    $output[$output_key] = $output_value;
                }
            }
        }

        return $output;
    }
}
