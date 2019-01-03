<?php

/**
 * Description.
 *
 * @param
 * @return
 */
function create($class, $attributes = [], $times = null)
{
    return factory($class, $times)->create($attributes);
}

/**
 * Description.
 *
 * @param
 * @return
 */
function make($class, $attributes = [], $times = null)
{
    return factory($class, $times)->make($attributes);
}

/**
 * Description.
 *
 * @param
 * @return
 */
function raw($class, $attributes = [], $times = null)
{
    return factory($class, $times)->raw($attributes);
}
