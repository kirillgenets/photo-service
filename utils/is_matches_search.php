<?php

function is_matches_search($str, $search, $min_symbols_count = 1) {
  if ($search === null) return true;

  $search_words = explode(' ', $search);
  $search_results = array_map(function($item) use ($str, $min_symbols_count) {
    return !empty($item) && mb_strlen($item) >= $min_symbols_count && strpos($str, $item) !== false;
  }, $search_words);

  return in_array(true, $search_results);
}

?>