$(function() {
  console.log('Loaded ...');

  Handlebars.registerHelper("studyStatus", function(data, options) {
    var len = data.length;
    var returnData = "";
    for (var i = 0; i < len; i++) {
      data[i].passingYear = (data[i].passingYear < 2015) ? "passed" : "not passed";
      returnData = returnData + options.fn(data[i]);
    }
    return returnData;
  })

  //Retrieve the template data from the HTML .
  var template = $('#handlebars-demo').html();

  var context = {
    "students": [{
      "name": "John",
      "passingYear": 2013
    }, {
      "name": "Doe",
      "passingYear": 2016
    }]
  }

  //Compile the template data into a function
  var templateScript = Handlebars.compile(template);

  var html = templateScript(context);
  //html = 'My name is Ritesh Kumar . I am a developer.'

  $(document.body).append(html);
});
