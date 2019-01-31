<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <title>Вход</title>

    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet"
	href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
	integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
	crossorigin="anonymous">
<!-- Optional theme -->
<link rel="stylesheet"
	href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
	integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp"
	crossorigin="anonymous">
<link rel="stylesheet" href="<?= F3::get('BASE') ?>/css/signin.css">	
  </head>

  <body>

    <div class="container">
       
      <form class="form-signin" method="POST">
        <h2 class="form-signin-heading">Вход</h2>
        <label for="inputEmail" class="sr-only">Логин</label>
        <input type="text" id="inputLogin" class="form-control" name="login" placeholder="Логин" value="<?= F3::get('POST.login') ?>" required autofocus>
        <label for="inputPassword" class="sr-only">Пароль</label>
        <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Пароль" value="<?= F3::get('POST.password') ?>" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Войти</button>
      </form>
      
      <? if(F3::get('message')) { ?>
      <div class='alert alert-warning'><h3><?= F3::get('message.text') ?></h3></div>
      <? } ?>

    </div> <!-- /container -->

   
  </body>
</html>
