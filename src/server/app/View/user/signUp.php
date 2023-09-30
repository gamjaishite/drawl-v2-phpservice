<main class="container-register">
    <h2>Sign Up</h2>
    <?php if (isset($model['error'])) { ?>
        <div class="alert-error alert-error-signup">
            <p>
                <?= $model['error'] ?>
            </p>
        </div>
    <?php } ?>
    <div class="div-form-signup">
        <form class="form-default" method="post" action='/signup'>
            <div class="form-input-default">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" class="input-default"
                    value="<?= $_POST["email"] ?? "" ?>" />
            </div>
            <div class="form-input-default">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password"
                    class="input-default" />
            </div>
            <button type="submit" class="btn-primary">Sign Up</button>
        </form>
    </div>
</main>