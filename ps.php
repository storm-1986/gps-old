<html>
<head></head>
<body>
<form name="form" action="ps.php" method="post">
<input type="text" name="par" value=""/>
<input type="submit" name="send" value="Пароль">
</form>
<?
if (isset($_POST['send'])){
$pass = $_POST['par'];
echo md5($pass);
}
?>
</body>
</html>