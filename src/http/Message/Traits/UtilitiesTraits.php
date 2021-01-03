<?php 
namespace Http\Message\Traits;

use Http\Exceptions\InvalidArgumentException;

trait UtilitiesTraits
{
    /**
     * Validate date Type string.
     * 
     * @param string
     * @return int
     */
    protected function requiredString($paramString)
    {
        if (!is_string($paramString)) {

            throw new InvalidArgumentException(
                sprintf("[ %s ] must be a valid string. Type ( %s ) is given in %s.", 
                    json_encode($paramString), gettype($paramString), __METHOD__
                )
            );

        }

        return $paramString;
    }

    /**
     * Validate date Type int.
     * 
     * @param string
     * @return int
     */
    protected function requiredInt($paramInt)
    {
        if (!is_int($paramInt)) {
            throw new InvalidArgumentException(
                sprintf("[ %s ] must be a valid integer. Type ( %s ) is given. in %s ", 
                    $paramInt, gettype($paramInt), __METHOD__
                )
            );

        }

        return $paramInt;
    } 

    public function isNumericParam($param)
    {
        if (is_numeric($param)) {
            $numeric = (int) $param;
        } else {
            throw new InvalidArgumentException(sprintf("Invalid status code type."));
        }

        return $numeric;
    }

    /**
     * Convert to parameter to integer
     * 
     * @param mixed
     * @return int
     */
    protected function convertToInt($param)
    {
        return !is_int($param) ? (int) $param : (empty($param) ? null : $param);
    }

    /**
     * Convert to parameter to string
     * 
     * @param mixed
     * @return string
     */
    protected function convertToString($param)
    {
        return !is_string($param) ? (string) $param : $param;
    }
} 