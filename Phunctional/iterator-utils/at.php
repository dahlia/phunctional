<?php

function at($array, $key, $defaultValue = null) {
	return isset($array[$key]) ? $array[$key] : $defaultValue;
}
