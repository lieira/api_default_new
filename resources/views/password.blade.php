<!doctype html>
<html lang="pt-br">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

	<title>{{env('PROJECT_NAME')}}</title>

	<style>

		body
		{
			margin: 0;
			padding: 0;
			display: flex !important;
			flex-direction: column !important;
			align-content: center !important;
			justify-content: center !important;
			color: black !important;
		}

		header
		{
			padding: 40px;
			display: flex;
			justify-content: center;
		}

		section
		{
			font-family: 'Ubuntu', sans-serif;
			margin: 0;
			padding: 0;
		}

		.text-title
		{
			text-align: center !important;
			margin: 20px !important;
			color: #86C8FA !important;
		}

		.title
		{
			font-size: 25px;
			letter-spacing: 2px !important;

		}

		.cont
		{
			color: grey !important;
		}

		.cont-2
		{
			color: white !important;
		}

		a
		{
			color: white !important;
			text-decoration: white !important;
			background-color: #86C8FA !important;
			padding: 15px !important;
			border-radius: 5px !important;
			margin: 20px !important;
			display: block !important;
			width: 200px !important;
			color: grey;
		}

		.container
		{
			width: 600px !important;
			background-color: #E7E7E7 !important;
			box-shadow: 0px 0px 15px #9C9C9C !important;
			border-radius: 5px !important;
			border: 2px solid #86C8FA;
		}

		.cont-btns
		{
			margin-top: 40px !important;
			margin-bottom: 50px !important;
			display: flex !important;
			justify-content: center !important;
        }
        
        .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

	</style>

</head>
<body>

<center style="">
	<div class="container">

		<header>
			<img src="https://via.placeholder.com/200" width="200px" class="center" alt="">
		</header>

		<section>

			<span class="text-title title">Olá, {{$name}}</span>

			<div class="text-title cont text-center">
				Clique no link abaixo para redefinir sua senha.
			</div>

            <a href="{{env('PROJECT_URL')}}/auth/recovery-password?token={{$token}}" class="text-title cont-2 text-center">REDEFINIR SENHA</a>

			<br>


			<div class="text-title cont text-center">
				A equipe da <strong>{{env('PROJECT_NAME')}}</strong> agradece
            </div>
            
            <br>

				
             
            <br>




		</section>

	</div>

</center>


</body>
</html>