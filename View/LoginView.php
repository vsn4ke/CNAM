<form action="<?= generateURL('login') ?>" method="post" id="loginForm">
    <input type="hidden" name="submited">
    <ul>
        <li><label for="usr">Username : </label><input id="usr" type="text" name="userName"></li>
        <li><label for="pwd">Password : </label><input id="pwd" type="password" name="userPassword"></li>
        <li><input type="submit"  id="submitForm" value="Login"></li>
    </ul>
</form>