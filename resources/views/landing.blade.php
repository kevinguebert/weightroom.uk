<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    <title>Weightroom.uk - Workout tracker</title>
	<!-- <link rel="icon" type="image/x-icon" href="favicon.ico"> -->
	<meta http-equiv="Content-Language" content="en">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.css" rel="stylesheet" />

    <!--     Fonts     -->
    <link href="http://netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css" rel="stylesheet">

<style>
#background-box {
	background-image: url('http://weightroom.uk/img/background-1.jpg');
	background-position: 0 -100px;
	height: 600px;
}
#dark-background {
	background-color:rgba(0,0,0,0.30);
	height: 100%;
}
#logscreen {
	background-image: url('http://weightroom.uk/img/landing/log.png');
}
#volumescreen {
	background-image: url('http://weightroom.uk/img/landing/volume.png');
}
#goalscreen {
	background-image: url('http://weightroom.uk/img/landing/goal.png');
}
#logscreen, #volumescreen, #goalscreen {
	width: 500px;
	height: 300px;
}
#logscreen, #goalscreen {
	margin-left: 80px;
}
#volumescreen {
	margin-right: 80px;
}
.center-box, .main-box {
	margin: 0 auto 0 auto;
	text-align: center;
}
#header-text {
	color: #FFFFFF;
	padding: 5em;
}
#header-text h1, #header-text h2 {
	color: #FFFFFF;
	padding: 0;
}
#header-text h1 {
	font-size: 4vw;
}
#header-text h2 {
	font-size: 3vw;
	padding: 0.5em;
}
.main-box h1 {
	font-size: 48px;
	padding: 50px;
}
.navbar-default {
	background-color: #171717;
    border-color: #000000;
}
.navbar-default .navbar-nav li a, .navbar-default .navbar-text, .navbar-default .navbar-brand {
	color: #c3c3c3;
	margin-right: 2px;
}
.navbar-default .navbar-nav li a:hover, .navbar-default a.navbar-brand:hover {
	color: #adadad;
	background-color: #2f2f2f;
}
.row {
	max-width: 1200px;
	margin: 30px auto 0 auto;
}
.footer {
	margin-top: 50px;
}
.lead {
	text-align: left;
}
</style>
</head>

<body>
<div id="background-box">
	<div id="dark-background">
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="#">weightroom.uk</a> <p class="navbar-text">[beta]</p>
				</div>

				<div class="collapse navbar-collapse navbar-right">
					<ul class="nav navbar-nav">
						<li><a href="{{ route('tools') }}">Tools</a></il>
						<li><a href="{{ route('demo') }}">Demo</a></il>
						<li><a href="//weightroom.uk/blog/">Blog</a></il>
						<li><a href="{{ route('login') }}">Login</a></il>
					</ul>
				</div>
			</div>
		</nav>
		<div class="container-fluid center-box">
			<div id="header-text">
				<h1>Track and analyse your training.</h1>
				<h2>Become the strongest and fittest you can be.</h2>
			</div>
			<form class="form-inline" action="{{ route('register') }}" method="get">
				<div class="form-group">
					<label class="sr-only" for="user_email">Email address</label>
					<input type="email" class="form-control" id="user_email" name="user_email" placeholder="Email">
				</div>
				{{ csrf_field() }}
				<button type="submit" class="btn btn-default">Sign me up</button>
			</form>
		</div>
	</div>
</div>
<div class="container-fluid main-box">
	<h1>Why should I use weightroom.uk?</h1>
	<div class="row">
		<div class="col-md-6">
			<h2>Intuative and simple text based logging</h2>
			<p class="lead">
				Quickly enter your workouts on any device with our simple to learn workout markup.
				Never again will you have to fill out hundreds of boxes, log your workouts as fast as you can type.
			</p>
		</div>
		<div class="col-md-6" id="logscreen">
			&nbsp;
		</div>
	</div>
	<div class="row">
		<div class="col-md-6" id="volumescreen">
			&nbsp;
		</div>
		<div class="col-md-6">
			<h2>Anaylse everything</h2>
			<p class="lead">
				Receive the analytics you <em>need</em> to optimise your fitness and reach your goals.
				We will give you all the information you could need to make sure your training is on track and progressing well.
				<a href="{{ route('demo') }}">[more examples]</a>
			</p>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<h2>Set goals</h2>
			<p class="lead">
				The best way to achieve something is to set measurable goals, and we will help you with just that.
				See you goals neatly arranged so you can see what needs work and where you are excelling.
			</p>
		</div>
		<div class="col-md-6" id="goalscreen">
			&nbsp;
		</div>
	</div>
</div>

<footer class="footer">
  <div class="container">
	<p class="text-muted">2015 &#169; weightroom.uk.<span class="hidden-xs"> Use of this site constitutes acceptance of the site's <a href="{{ route('privacyPolicy') }}" target="_blank">Privacy Policy and Terms of Use</a>.</span></p>
  </div>
</footer>

<script src="http://code.jquery.com/jquery-2.1.3.min.js" charset="utf-8"></script>
<script src="http://getbootstrap.com/dist/js/bootstrap.min.js" charset="utf-8"></script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-1798088-8', 'auto');
  ga('send', 'pageview');

</script>
</body>
</html>
