fetch_and_parse = function(urls, callback) {

  num_urls = urls.length;
  remaining = num_urls;
  results = new Array(num_urls);

  // This is called after every parse, counts the number of calls, and calls the
  // callback function whenever all urls are parsed
  count_calls = function() {
    num_urls = num_urls - 1;
    if (num_urls === 0) {
      callback(results)
    }
  }

  // Loops over urls, create a function that will save the data at the right
  // place and call the count_call function, and feed this function to
  // papaparser
  for (idx = 0 ; idx < num_urls ; idx++) {
    handle_result =
      (function(idx) {
        return function(result) {
          results[idx] = result.data ;
          count_calls();
        }
      })(idx);

    Papa.parse(urls[idx], {
      download: true,
      header: true,
      complete: handle_result
    });
  }

}

