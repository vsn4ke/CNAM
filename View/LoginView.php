<form action="<?= generateURL('login') ?>" method="post" id="loginForm">
    <input type="hidden" name="submited">
    <ul>
        <li><label for="usr">Username : </label><input id="usr" type="text" name="userName"></li>
        <li><label for="pwd">Password : </label><input id="pwd" type="password" name="userPassword"></li>
        <li><input type="submit"  id="submitForm" value="Login"></li>
    </ul>
</form>
<script type="application/javascript">
    document.getElementById('submitForm').addEventListener("click", function(event){
        event.preventDefault();
        var usr = document.getElementById('usr').value;
        var pwd = document.getElementById('pwd').value;

        if(usr.length < 6 || pwd.length < 6 ){
            document.getElementById('flash').innerHTML = "The username and the password are required and must be at least six characters long.";
        }
        else{
            document.getElementById('loginForm').submit();
        }
    });
</script>