<form action="<?= generateURL('register') ?>" method="post" id="registerForm">
    <input type="hidden" name="submited">
    <ul>
        <li><label for="usr">Username : </label><input id="usr" type="text" name="userName"></li>
        <li><label for="pwd">Password : </label><input id="pwd" type="password" name="userPassword"></li>
        <li><label for="pwdConfirmation">Confirm password : </label><input id="pwdConfirmation" type="password" name="userPasswordConfirmation"></li>
        <li><input type="submit"  id="submitForm" value="Register"></li>
    </ul>
</form>