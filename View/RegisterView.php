<form action="<?= generateURL('register') ?>" method="post" id="registerForm">
    <input type="hidden" name="submited">
    <ul>
        <li><label for="usr">Username : </label><input id="usr" type="text" name="userName"></li>
        <li><label for="pwd">Password : </label><input id="pwd" type="password" name="userPassword"></li>
        <li><label for="pwdConfirmation">Confirm password : </label><input id="pwdConfirmation" type="password" name="userPasswordConfirmation"></li>
        <li><input type="submit"  id="submitForm" value="Register"></li>
    </ul>
</form>
<script type="application/javascript">
    document.getElementById('submitForm').addEventListener("click", function(event){
        event.preventDefault();
        var usr = document.getElementById('usr').value;
        var pwd = document.getElementById('pwd').value;
        var pwdC = document.getElementById('pwdConfirmation').value;
        var flash = document.getElementById('flash');

        flash.innerHTML = '';
        if(usr.length < 6 || pwd.length < 6 ){
            flash.innerHTML = "The username and the password are required and must be at least six characters long.";
        }
        else if(pwd !== pwdC){
            flash.innerHTML = "The password and the confirmation are not the same.";
        }
        else{
            document.getElementById('registerForm').submit();
        }
    });
</script>