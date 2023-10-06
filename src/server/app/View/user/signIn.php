<div class="signin-container row">
    <img src="/assets/images/Suzume.webp" alt="Sign In Image" class="signin-poster"/>
    <div class="right-side">
        <div class="main-container">
            <div class="welcome-text">
                <h2 class="welcome-text__h2">Hello Again!</h2>
                <p class="welcome-text__h1">Welcome back! Please provide your details</p>
            </div>

            <form class="inputs" action="/signin" method="post">
                <div class="parameter">
                    <label for="email" class="input-required">Email</label>
                    <input type="email" name="email" id="email" class="input-default"
                           value="<?= $model['data']['email'] ?? "" ?>">
                </div>
                <div class="parameter">
                    <label for="password" class="input-required">Password</label>
                    <input type="password" name="password" id="password" class="input-default">
                </div>
                <button class="btn-bold" type="submit">
                    Sign In
                </button>
                <p>Don't have an account? </p> <a href="/signup" class="signup-link">Sign up</a>

            </form>
            <?php if (isset($model['error'])) { ?>
                <div class="alert-error">
                    <p>
                        <?= $model['error'] ?>
                    </p>
                </div>
            <?php } ?>
        </div>
    </div>
</div>