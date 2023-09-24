<div class="container container-register">
    <h2>Sign Up</h2>
    <?php if (isset($model['error'])) {?>
        <div class="alert-error alert-error-signup">
            <p>
                <?=$model['error']?>
            </p>
        </div>
    <?php }?>
    <div class="div-form-signup">
        <form class="form-default" method="post" action='/signup'>
            <div class="form-input-default">
                <label for='id'>Id</label>
                <input type="text" name='id' id='id' placeholder="Enter your id" class="input-default" value="<?=$_POST['id'] ?? ''?>"/>
            </div>
            <div class="form-input-default">
                <label for='name'>Name</label>
                <input type="text" name='name' id='name' placeholder="Enter your name" class="input-default" value="<?=$_POST['name'] ?? ''?>"/>
            </div>
            <div class="form-input-default">
                <label for='password'>Password</label>
                <input type="password" name='password' id='password' placeholder="Enter your password" class="input-default"/>
            </div>
            <button type="submit" class="btn-primary">Sign Up</button>
        </form>
    </div>
</div>
