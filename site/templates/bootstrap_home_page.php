{% use 'default_bootstrap_side_nav.html' %}
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/favicon.ico">

    <title>{{ page_title|e }} - {{ site_name|e }}</title>

    <!-- Bootstrap core CSS -->
    <link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Custom styles for this template -->
    <style>
      /* GLOBAL STYLES
      -------------------------------------------------- */
      /* Padding below the footer and lighter body text */

      body {
        padding-bottom: 40px;
        color: #5a5a5a;
      }

      /* CUSTOMIZE THE NAVBAR
      -------------------------------------------------- */

      /* Special class on .container surrounding .navbar, used for positioning it into place. */
      .navbar-wrapper {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        z-index: 20;
      }

      /* Flip around the padding for proper display in narrow viewports */
      .navbar-wrapper > .container {
        padding-right: 0;
        padding-left: 0;
      }
      .navbar-wrapper .navbar {
        padding-right: 15px;
        padding-left: 15px;
      }
      .navbar-wrapper .navbar .container {
        width: auto;
      }

      /* CUSTOMIZE THE CAROUSEL
      -------------------------------------------------- */

      /* Carousel base class */
      .carousel {
        height: 500px;
        margin-bottom: 60px;
      }
      /* Since positioning the image, we need to help out the caption */
      .carousel-caption {
        z-index: 10;
      }

      /* Declare heights because of positioning of img element */
      .carousel .item {
        height: 500px;
        background-color: #777;
      }
      .carousel-inner > .item > img {
        position: absolute;
        top: 0;
        left: 0;
        min-width: 100%;
        height: 500px;
      }

      /* MARKETING CONTENT
      -------------------------------------------------- */

      /* Center align the text within the three columns below the carousel */
      .marketing .col-lg-4 {
        margin-bottom: 20px;
        text-align: center;
      }
      .marketing h2 {
        font-weight: normal;
      }
      .marketing .col-lg-4 p {
        margin-right: 10px;
        margin-left: 10px;
      }

      /* Featurettes
      ------------------------- */

      .featurette-divider {
        margin: 80px 0; /* Space out the Bootstrap <hr> more */
      }

      /* Thin out the marketing headings */
      .featurette-heading {
        font-weight: 300;
        line-height: 1;
        letter-spacing: -1px;
      }

      /* RESPONSIVE CSS
      -------------------------------------------------- */

      @media (min-width: 768px) {
        /* Navbar positioning foo */
        .navbar-wrapper {
          margin-top: 20px;
        }
        .navbar-wrapper .container {
          padding-right: 15px;
          padding-left: 15px;
        }
        .navbar-wrapper .navbar {
          padding-right: 0;
          padding-left: 0;
        }

        /* The navbar becomes detached from the top, so we round the corners */
        .navbar-wrapper .navbar {
          border-radius: 4px;
        }

        /* Bump up size of carousel content */
        .carousel-caption p {
          margin-bottom: 20px;
          font-size: 21px;
          line-height: 1.4;
        }

        .featurette-heading {
          font-size: 50px;
        }
      }

      @media (min-width: 992px) {
        .featurette-heading {
          margin-top: 120px;
        }
      }
    </style>

    {% block styles_head %}
      {% for item in css_includes %}
        <link rel="stylesheet" type="text/css" href="{{ item }}" media="screen" />
      {% endfor %}
      {{ parent() }}
    {% endblock %}

    {% block js_head %}
      {% for item in js_top %}
        <script type="text/javascript" src="{{ item }}"></script>
      {% endfor %}
    {% endblock %}

    {% if google_analytics_key %}
      <script type="text/javascript">
        var _gaq = _gaq || [];
          _gaq.push(['_setAccount', '{{ google_analytics_key }}']);
          _gaq.push(['_trackPageview']);

          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();
      </script>
    {% endif %}

  </head>
<!-- NAVBAR
================================================== -->
  <body>
    {% if is_dev %}
    <!-- <div class="watermark">Development</div> -->
    {% endif %}

    {#
    {% if navbar is not empty %}
      {% include navbar %}
    {% else %}
    #}
    <div class="navbar-wrapper">
      <div class="container">

        <nav class="navbar navbar-inverse navbar-static-top">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="#">{{ site_name|e }}</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
                <li class="active"><a href="#">Home</a></li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Link One</a></li>
                    <li><a href="#">Link Two</a></li>
                    <li><a href="#">Link Three</a></li>
                    {% if additional_header_links %}
                      {% for path,data in additional_header_links %}
                        <li><a href="{{ path }}">{{ data.text }}</a></li>
                      {% endfor %}
                    {% endif %}
                    <li class="divider"></li>
                    <li class="dropdown-header">Nav header</li>
                    <li><a href="#">Separated link</a></li>
                    <li><a href="#">One more separated link</a></li>
                  </ul>
                </li>
              </ul>
              {% if is_authenticated %}
                <ul class="nav navbar-nav pull-right">
                  <li class="dropdown">
                    <a class="dropdown-toggle" id="default_logged_in" role="button" href="#" data-toggle="dropdown">Logged in as {{ session[session_key].first_name }} {{ session[session_key].last_name }} <b class="caret"></b></a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="default_logged_in">
                      <li><a href="/dashboard/">Dashboard</a></li>
                      {% if preferences_url is not empty %}
                        <li><a href="{{ preferences_url }}">Preferences</a></li>
                      {% endif %}
                      <li><a href="{{ logout_url }}">Logout</a></li>
                    </ul>
                  </li>
                </ul>
              {% elseif login_url is not empty %}
                <ul class="nav navbar-nav pull-right">
                  <li><a onclick="javascript:$.cookie('{{ redirect_cookie_key }}', '{{ request_uri }}', { expires: 7, path: '/' }); window.location.href='{{ login_url }}'" href="javascript:void(0);">Login</a></li>
                </ul>
              {% endif %}
            </div>
          </div>
        </nav>

      </div>
    </div>


    <!-- Carousel
    ================================================== -->
    <div id="myCarousel" class="carousel slide" data-ride="carousel">
      <!-- Indicators -->
      <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
      </ol>
      <div class="carousel-inner" role="listbox">
        <div class="item active">
          <img src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="First slide">
          <div class="container">
            <div class="carousel-caption">
              <h1>Example headline.</h1>
              <p>Note: If you're viewing this page via a <code>file://</code> URL, the "next" and "previous" Glyphicon buttons on the left and right might not load/display properly due to web browser security rules.</p>
              <p><a class="btn btn-lg btn-primary" href="#" role="button">Sign up today</a></p>
            </div>
          </div>
        </div>
        <div class="item">
          <img src="data:image/gif;base64,R0lGODlhAQABAIAAAGZmZgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Second slide">
          <div class="container">
            <div class="carousel-caption">
              <h1>Another example headline.</h1>
              <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <p><a class="btn btn-lg btn-primary" href="#" role="button">Learn more</a></p>
            </div>
          </div>
        </div>
        <div class="item">
          <img src="data:image/gif;base64,R0lGODlhAQABAIAAAFVVVQAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Third slide">
          <div class="container">
            <div class="carousel-caption">
              <h1>One more for good measure.</h1>
              <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <p><a class="btn btn-lg btn-primary" href="#" role="button">Browse gallery</a></p>
            </div>
          </div>
        </div>
      </div>
      <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div><!-- /.carousel -->


    <!-- Marketing messaging and featurettes
    ================================================== -->
    <!-- Wrap the rest of the page in another container to center all the content. -->

    <div class="container marketing">

      <!-- Three columns of text below the carousel -->
      <div class="row">
        <div class="col-lg-4">
          <img class="img-circle" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Generic placeholder image" style="width: 140px; height: 140px;">
          <h2>Heading</h2>
          <p>Donec sed odio dui. Etiam porta sem malesuada magna mollis euismod. Nullam id dolor id nibh ultricies vehicula ut id elit. Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Praesent commodo cursus magna.</p>
          <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
        </div><!-- /.col-lg-4 -->
        <div class="col-lg-4">
          <img class="img-circle" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Generic placeholder image" style="width: 140px; height: 140px;">
          <h2>Heading</h2>
          <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Cras mattis consectetur purus sit amet fermentum. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh.</p>
          <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
        </div><!-- /.col-lg-4 -->
        <div class="col-lg-4">
          <img class="img-circle" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Generic placeholder image" style="width: 140px; height: 140px;">
          <h2>Heading</h2>
          <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
          <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
        </div><!-- /.col-lg-4 -->
      </div><!-- /.row -->


      <!-- START THE FEATURETTES -->

      <hr class="featurette-divider">

      <div class="row featurette">
        <div class="col-md-7">
          <h2 class="featurette-heading">First featurette heading. <span class="text-muted">It'll blow your mind.</span></h2>
          <p class="lead">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.</p>
        </div>
        <div class="col-md-5">
          <img class="featurette-image img-responsive" data-src="holder.js/500x500/auto" alt="Generic placeholder image">
        </div>
      </div>

      <hr class="featurette-divider">

      <div class="row featurette">
        <div class="col-md-5">
          <img class="featurette-image img-responsive" data-src="holder.js/500x500/auto" alt="Generic placeholder image">
        </div>
        <div class="col-md-7">
          <h2 class="featurette-heading">Oh yeah, it's that good. <span class="text-muted">See for yourself.</span></h2>
          <p class="lead">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.</p>
        </div>
      </div>

      <hr class="featurette-divider">

      <div class="row featurette">
        <div class="col-md-7">
          <h2 class="featurette-heading">And lastly, this one. <span class="text-muted">Checkmate.</span></h2>
          <p class="lead">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.</p>
        </div>
        <div class="col-md-5">
          <img class="featurette-image img-responsive" data-src="holder.js/500x500/auto" alt="Generic placeholder image">
        </div>
      </div>

      <hr class="featurette-divider">

      <!-- /END THE FEATURETTES -->

      {% block content %}{% endblock %}

      <!-- FOOTER -->
      <footer>
        <p>
          {% if site_footer is not empty %}
          {% include site_footer %}
          {% endif %}
        </p>
      </footer>

    </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.2/jquery.cookie.js"></script>
    <script src="/vendor/lib/javascripts/docs.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="/vendor/lib/javascripts/ie10-viewport-bug-workaround.js"></script>
    {% block js_bottom %}
      {% for item in js_includes %}
        <script type="text/javascript" src="{{ item }}"></script>
      {% endfor %}
    {% endblock %}
    {% block written_js_bottom %}
      {{ parent() }}
    {% endblock %}
  </body>
</html>
