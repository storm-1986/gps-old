</head>
<body>
<div class="container my-5">
	<div class="row justify-content-center">
		<div class="col-sm-6 col-md-4 d-flex justify-content-center align-items-center">
			<form name="autform" id="af" action="?osm=1" method="post">
				<div class="h3 mb-5 text-center"><h1>GPS Control</h1><?= $autmess; ?></div>
				<div class="form-group">
					<input type="text" class="form-control" name="log" id="log" placeholder="Логин" maxlength="30" autofocus/>
				</div>
				<div class="form-group">
					<input type="password" class="form-control" name="pass" id="pass" placeholder="Пароль" maxlength="30"/>
				</div>
				<input name="aut" type="submit" value="Вход" class="btn btn-secondary form-control"/>
			</form>
		</div>
	</div>
</div>
</body>
</html>