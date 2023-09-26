<div class="container container-signin">
    <h2>Sign In</h2>
    <?php if (isset($model['error'])) {?>
        <div class="alert-error alert-error-signin">
            <p>
                <?=$model['error']?>
            </p>
        </div>
    <?php }?>
    <div class="div-form-signin">
        <form class="form-default" method="post" action='/signin'>
            <div class="form-input-default">
                <label for='id'>Id</label>
                <input type="text" name='id' id='id' placeholder="Enter your id" class="input-default" value="<?=$_POST['id'] ?? ''?>"/>
            </div>
            <div class="form-input-default">
                <label for='password'>Password</label>
                <input type="password" name='password' id='password' placeholder="Enter your password" class="input-default"/>
            </div>
            <button type="submit" class="btn-primary">Sign In</button>
        </form>
    </div>
</div>
